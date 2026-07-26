<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RecepcionCompraRequest;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrdenCompraController extends AdminController
{
    public function index(Request $request): View
    {
        $query = OrdenCompra::query()
            ->with(['proveedor', 'sucursal', 'usuarioSolicitante', 'detalles']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'proveedor_id', 'sucursal_id']);
        $this->aplicarBusqueda($query, $request, ['numero']);

        $ordenes = $query->orderByDesc('fecha_emision')->paginate(15)->withQueryString();

        $proveedores = Proveedor::where('estado', true)->orderBy('nombre_empresa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes-compra.index', [
            'ordenes' => $ordenes,
            'proveedores' => $proveedores,
            'sucursales' => $sucursales,
        ]);
    }

    public function show(OrdenCompra $orden): View
    {
        $orden->load([
            'solicitudCompra',
            'cotizacion',
            'proveedor',
            'sucursal',
            'usuarioSolicitante',
            'usuarioAprobador',
            'detalles.repuesto',
        ]);

        return view('admin.ordenes-compra.show', [
            'orden' => $orden,
        ]);
    }

    public function marcarEnviada(Request $request, OrdenCompra $orden): RedirectResponse
    {
        if (! in_array($orden->estado, ['aprobada', 'pendiente_aprobacion'])) {
            return back()->with('error', 'La orden debe estar aprobada para marcarla como enviada.');
        }

        $request->validate([
            'enviada_medio' => ['required', 'string', 'in:whatsapp,llamada,correo,presencial,doc_fisico,otro'],
            'enviada_fecha' => ['nullable', 'date'],
        ]);

        $orden->update([
            'estado' => 'enviada',
            'enviada_medio' => $request->enviada_medio,
            'enviada_fecha' => $request->enviada_fecha ?: now(),
        ]);

        return back()->with('success', "Orden {$orden->numero} marcada como enviada.");
    }

    public function recibir(OrdenCompra $orden): View
    {
        if (! in_array($orden->estado, ['enviada', 'parcialmente_recibida'])) {
            abort(404, 'La orden no está lista para recepción.');
        }

        $orden->load(['detalles.repuesto', 'proveedor', 'sucursal']);

        return view('admin.ordenes-compra.recibir', [
            'orden' => $orden,
        ]);
    }

    public function procesarRecepcion(RecepcionCompraRequest $request, OrdenCompra $orden): RedirectResponse
    {
        if (! in_array($orden->estado, ['enviada', 'parcialmente_recibida'])) {
            return back()->with('error', 'La orden no está lista para recepción.');
        }

        $datos = $request->validated();

        DB::transaction(function () use ($datos, $orden) {
            $totalCompletado = true;
            $parcial = false;

            foreach ($datos['items'] as $itemData) {
                $detalle = $orden->detalles()->findOrFail($itemData['id']);

                $recibida = (int) $itemData['cantidad_recibida'];
                $aceptada = (int) $itemData['cantidad_aceptada'];
                $rechazada = (int) $itemData['cantidad_rechazada'];

                $detalle->update([
                    'cantidad_recibida' => $recibida,
                    'cantidad_aceptada' => $aceptada,
                    'cantidad_rechazada' => $rechazada,
                    'motivo_rechazo' => $itemData['motivo_rechazo'] ?? null,
                ]);

                if ($aceptada > 0) {
                    $inventario = Inventario::firstOrCreate(
                        [
                            'sucursal_id' => $orden->sucursal_id,
                            'repuesto_id' => $detalle->repuesto_id,
                        ],
                        [
                            'cantidad_actual' => 0,
                            'cantidad_reservada' => 0,
                            'fecha_actualizacion' => now(),
                        ]
                    );

                    $anterior = (int) $inventario->cantidad_actual;
                    $inventario->cantidad_actual = $anterior + $aceptada;
                    $inventario->fecha_actualizacion = now();
                    $inventario->save();

                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'usuario_id' => Auth::id(),
                        'tipo' => 'entrada',
                        'cantidad' => $aceptada,
                        'existencia_anterior' => $anterior,
                        'existencia_nueva' => $inventario->cantidad_actual,
                        'motivo' => "Entrada por compra - OC: {$orden->numero}",
                        'fecha_movimiento' => now(),
                    ]);
                }

                if ($recibida < $detalle->cantidad_solicitada) {
                    $parcial = true;
                }
            }

            $nuevoEstado = $parcial ? 'parcialmente_recibida' : 'recibida';
            $orden->update(['estado' => $nuevoEstado]);
        });

        return redirect()->route('admin.ordenes-compra.show', $orden)
            ->with('success', 'Recepción procesada. El inventario fue actualizado.');
    }

    public function cancelar(OrdenCompra $orden): RedirectResponse
    {
        if (in_array($orden->estado, ['recibida', 'cancelada'])) {
            return back()->with('error', 'No se puede cancelar una orden recibida o ya cancelada.');
        }

        $orden->update(['estado' => 'cancelada']);

        return back()->with('success', "Orden {$orden->numero} cancelada.");
    }
}
