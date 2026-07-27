<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MovimientoInventarioRequest;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Repuesto;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MovimientoInventarioController extends AdminController
{
    public function index(Request $request): View
    {
        $query = MovimientoInventario::query()
            ->with(['inventario.repuesto', 'inventario.sucursal', 'sucursalOrigen', 'sucursalDestino', 'usuario']);

        if ($sucursalId = $this->usuarioSucursalId()) {
            $query->whereHas('inventario', fn ($q) => $q->where('sucursal_id', $sucursalId));
        }

        $this->aplicarFiltros($request, $query, ['tipo', 'repuesto_id', 'sucursal_id']);
        $this->aplicarBusqueda($query, $request, [
            'motivo',
            'inventario.repuesto.nombre',
        ]);

        $movimientos = $query->orderByDesc('fecha_movimiento')->paginate(20)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();
        $repuestos = Repuesto::orderBy('nombre')->get();

        return view('admin.movimientos-inventario.index', [
            'movimientos' => $movimientos,
            'sucursales' => $sucursales,
            'repuestos' => $repuestos,
        ]);
    }

    public function create(): View
    {
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();
        $repuestos = Repuesto::orderBy('nombre')->get();

        return view('admin.movimientos-inventario.create', [
            'sucursales' => $sucursales,
            'repuestos' => $repuestos,
        ]);
    }

    public function store(MovimientoInventarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($datos) {
            $sucursalId = $datos['tipo'] === 'transferencia'
                ? $datos['sucursal_origen_id']
                : $datos['sucursal_id'];

            $inventario = Inventario::firstOrCreate(
                [
                    'sucursal_id' => $sucursalId,
                    'repuesto_id' => $datos['repuesto_id'],
                ],
                [
                    'cantidad_actual' => 0,
                    'cantidad_reservada' => 0,
                    'fecha_actualizacion' => now(),
                ]
            );

            $anterior = (int) $inventario->cantidad_actual;
            $cantidad = (int) $datos['cantidad'];

            // Tipos que suman stock
            $tiposEntrada = [
                'entrada_inicial', 'entrada_compra', 'devolucion',
                'ajuste_positivo', 'liberacion_reserva',
            ];
            // Tipos que restan stock
            $tiposSalida = [
                'salida_orden', 'consumo', 'dañado', 'vencido', 'perdida',
                'devolucion_proveedor', 'reserva',
            ];

            $nuevo = match (true) {
                in_array($datos['tipo'], $tiposEntrada) => $anterior + $cantidad,
                in_array($datos['tipo'], $tiposSalida) => max(0, $anterior - $cantidad),
                in_array($datos['tipo'], ['ajuste', 'ajuste_negativo']) => $cantidad,
                default => $anterior,
            };

            if ($datos['tipo'] === 'transferencia' && ! empty($datos['sucursal_destino_id'])) {
                $invDestino = Inventario::firstOrCreate(
                    [
                        'sucursal_id' => $datos['sucursal_destino_id'],
                        'repuesto_id' => $datos['repuesto_id'],
                    ],
                    [
                        'cantidad_actual' => 0,
                        'cantidad_reservada' => 0,
                        'fecha_actualizacion' => now(),
                    ]
                );
                $invDestino->cantidad_actual = (int) $invDestino->cantidad_actual + $cantidad;
                $invDestino->fecha_actualizacion = now();
                $invDestino->save();
            }

            $inventario->cantidad_actual = $nuevo;
            $inventario->fecha_actualizacion = now();
            $inventario->save();

            // Actualizar costo promedio cuando es entrada
            if (in_array($datos['tipo'], $tiposEntrada)) {
                $costoActual = (float) ($inventario->costo_promedio ?? 0);
                $nuevoCosto = (float) ($inventario->repuesto->costo_compra ?? 0);

                if ($costoActual == 0) {
                    $inventario->costo_promedio = $nuevoCosto;
                } elseif ($nuevoCosto > 0) {
                    $totalCantidad = $anterior + $cantidad;
                    $inventario->costo_promedio = (($costoActual * $anterior) + ($nuevoCosto * $cantidad)) / max(1, $totalCantidad);
                }
                $inventario->save();
            }

            MovimientoInventario::create([
                'inventario_id' => $inventario->id,
                'sucursal_origen_id' => $datos['sucursal_origen_id'] ?? null,
                'sucursal_destino_id' => $datos['sucursal_destino_id'] ?? null,
                'usuario_id' => auth()->id(),
                'orden_trabajo_id' => $datos['orden_trabajo_id'] ?? null,
                'tipo' => $datos['tipo'],
                'cantidad' => $cantidad,
                'existencia_anterior' => $anterior,
                'existencia_nueva' => $nuevo,
                'motivo' => $datos['motivo'],
                'fecha_movimiento' => now(),
            ]);
        });

        return $this->redirigirALista('admin.movimientos-inventario.index', 'Movimiento de inventario registrado con éxito.');
    }

    public function show(MovimientoInventario $movimiento): View
    {
        $movimiento->load(['inventario.repuesto', 'inventario.sucursal', 'sucursalOrigen', 'sucursalDestino', 'usuario', 'ordenTrabajo']);

        return view('admin.movimientos-inventario.show', [
            'movimiento' => $movimiento,
        ]);
    }

    public function destroy(MovimientoInventario $movimiento): RedirectResponse
    {
        $movimiento->delete();

        return $this->redirigirConExito('movimientos de inventario', 'eliminado');
    }

    public function route(MovimientoInventario $movimiento): View
    {
        $movimiento->load([
            'inventario.repuesto',
            'inventario.sucursal',
            'sucursalOrigen',
            'sucursalDestino',
            'usuario',
        ]);

        $sucursales = Sucursal::query()
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('nombre')
            ->get();

        return view('admin.movimientos-inventario.route', [
            'movimiento' => $movimiento,
            'sucursales' => $sucursales,
        ]);
    }
}
