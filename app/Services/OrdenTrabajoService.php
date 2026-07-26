<?php

namespace App\Services;

use App\Models\DetalleOrdenTrabajo;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use Illuminate\Support\Facades\DB;

class OrdenTrabajoService
{
    public function agregarRepuesto(OrdenTrabajo $orden, array $datos): DetalleOrdenTrabajo
    {
        if (in_array($orden->estado, ['finalizada', 'entregada', 'anulada'])) {
            throw new \RuntimeException('No se pueden agregar repuestos a una orden ' . $orden->estado . '.');
        }

        $repuesto = Repuesto::findOrFail($datos['repuesto_id']);
        $cantidad = (int) ($datos['cantidad'] ?? 1);
        $precioUnitario = $datos['precio_unitario'] ?? $repuesto->precio_venta;
        $subtotal = $cantidad * (float) $precioUnitario;

        return DB::transaction(function () use ($orden, $repuesto, $cantidad, $precioUnitario, $subtotal, $datos) {
            $inventario = Inventario::where('sucursal_id', $orden->sucursal_id)
                ->where('repuesto_id', $repuesto->id)
                ->first();

            $stockDisponible = $inventario ? (int) $inventario->cantidad_actual : 0;
            $stockSuficiente = $stockDisponible >= $cantidad;

            if ($stockSuficiente) {
                $existenciaAnterior = $stockDisponible;
                $existenciaNueva = $stockDisponible - $cantidad;

                $inventario->cantidad_actual = $existenciaNueva;
                $inventario->fecha_actualizacion = now();
                $inventario->save();

                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'usuario_id' => auth()->id(),
                    'orden_trabajo_id' => $orden->id,
                    'tipo' => 'salida_orden',
                    'cantidad' => $cantidad,
                    'existencia_anterior' => $existenciaAnterior,
                    'existencia_nueva' => $existenciaNueva,
                    'motivo' => "Consumo en orden {$orden->numero_orden}: {$repuesto->nombre} x{$cantidad}",
                    'fecha_movimiento' => now(),
                ]);
            }

            $detalle = DetalleOrdenTrabajo::create([
                'orden_trabajo_id' => $orden->id,
                'tipo' => 'repuesto',
                'repuesto_id' => $repuesto->id,
                'descripcion' => $datos['descripcion'] ?? $repuesto->nombre,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
                'observaciones' => $stockSuficiente
                    ? null
                    : "SIN STOCK — se necesita solicitar compra",
            ]);

            $this->actualizarTotales($orden);

            return $detalle;
        });
    }

    public function eliminarRepuesto(OrdenTrabajo $orden, DetalleOrdenTrabajo $detalle): void
    {
        DB::transaction(function () use ($orden, $detalle) {
            $inventario = Inventario::where('sucursal_id', $orden->sucursal_id)
                ->where('repuesto_id', $detalle->repuesto_id)
                ->first();

            if ($inventario) {
                $existenciaAnterior = (int) $inventario->cantidad_actual;
                $cantidad = (int) $detalle->cantidad;

                $inventario->cantidad_actual = $existenciaAnterior + $cantidad;
                $inventario->fecha_actualizacion = now();
                $inventario->save();

                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'usuario_id' => auth()->id(),
                    'orden_trabajo_id' => $orden->id,
                    'tipo' => 'devolucion',
                    'cantidad' => $cantidad,
                    'existencia_anterior' => $existenciaAnterior,
                    'existencia_nueva' => $existenciaAnterior + $cantidad,
                    'motivo' => "Devolución por eliminación de detalle en orden {$orden->numero_orden}",
                    'fecha_movimiento' => now(),
                ]);
            }

            $detalle->delete();
            $this->actualizarTotales($orden);
        });
    }

    public function buscarRepuestosConStock(OrdenTrabajo $orden, ?string $busqueda = null): array
    {
        $query = Repuesto::query()
            ->select('repuestos.*')
            ->leftJoin('inventarios', function ($join) use ($orden) {
                $join->on('repuestos.id', '=', 'inventarios.repuesto_id')
                    ->where('inventarios.sucursal_id', '=', $orden->sucursal_id);
            })
            ->selectRaw('COALESCE(inventarios.cantidad_actual, 0) as stock_actual')
            ->selectRaw('COALESCE(inventarios.cantidad_reservada, 0) as stock_reservado')
            ->where('repuestos.estado', true);

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $termino = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $busqueda) . '%';
                $q->where('repuestos.nombre', 'like', $termino)
                    ->orWhere('repuestos.codigo', 'like', $termino)
                    ->orWhere('repuestos.codigo_barras', 'like', $termino);
            });
        }

        $repuestos = $query->orderBy('repuestos.nombre')->take(20)->get();

        $idsEnOrden = $orden->detalles()
            ->where('tipo', 'repuesto')
            ->pluck('repuesto_id')
            ->toArray();

        return $repuestos->map(function ($r) use ($idsEnOrden) {
            $disponible = (int) ($r->stock_actual ?? 0);
            $stockMinimo = (int) ($r->stock_minimo ?? 0);

            return [
                'id' => $r->id,
                'codigo' => $r->codigo,
                'nombre' => $r->nombre,
                'precio_venta' => (float) ($r->precio_venta ?? 0),
                'stock_actual' => $disponible,
                'stock_minimo' => $stockMinimo,
                'stock_bajo' => $disponible <= $stockMinimo,
                'sin_stock' => $disponible <= 0,
                'ya_en_orden' => in_array($r->id, $idsEnOrden),
            ];
        })->toArray();
    }

    public function actualizarTotales(OrdenTrabajo $orden): void
    {
        $subtotalRepuestos = (float) $orden->detalles()
            ->where('tipo', 'repuesto')
            ->sum('subtotal');

        $subtotalServicios = (float) $orden->detalles()
            ->where('tipo', 'servicio')
            ->sum('subtotal');

        $descuento = (float) ($orden->descuento ?? 0);
        $total = $subtotalRepuestos + $subtotalServicios - $descuento;

        $orden->update([
            'subtotal_repuestos' => max(0, $subtotalRepuestos),
            'subtotal_servicios' => max(0, $subtotalServicios),
            'total_general' => max(0, $total),
        ]);
    }

    public function sugerirSolicitudCompra(OrdenTrabajo $orden): ?array
    {
        $detallesSinStock = $orden->detalles()
            ->where('tipo', 'repuesto')
            ->where('observaciones', 'like', '%SIN STOCK%')
            ->with('repuesto')
            ->get();

        if ($detallesSinStock->isEmpty()) {
            return null;
        }

        return [
            'sucursal_id' => $orden->sucursal_id,
            'motivo' => "Repuestos sin stock para orden {$orden->numero_orden}",
            'productos' => $detallesSinStock->map(fn ($d) => [
                'repuesto_id' => $d->repuesto_id,
                'nombre' => $d->repuesto?->nombre ?? $d->descripcion,
                'cantidad' => (int) $d->cantidad,
            ])->toArray(),
        ];
    }
}
