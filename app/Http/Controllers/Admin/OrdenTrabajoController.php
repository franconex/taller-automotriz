<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrdenTrabajoDetalleRequest;
use App\Http\Requests\Admin\OrdenTrabajoRequest;
use App\Models\Cliente;
use App\Models\DetalleOrdenTrabajo;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use App\Services\OrdenTrabajoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class OrdenTrabajoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:ordenes.crear', only: ['create', 'store']),
            new Middleware('permiso:ordenes.editar', only: ['edit', 'update']),
            new Middleware('permiso:ordenes.actualizar_estado', only: ['toggle', 'cambiarEstadoOrden']),
            new Middleware('permiso:ordenes.cancelar', only: ['cancelar']),
            new Middleware('permiso:roles.editar', only: ['destroy']),
        ];
    }
    public function index(Request $request): View
    {
        $query = OrdenTrabajo::query()->with(['cliente', 'vehiculo', 'sucursal']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'sucursal_id']);
        $this->aplicarBusqueda($query, $request, [
            'numero_orden',
            'descripcion_problema',
            'cliente.nombre_completo',
            'vehiculo.placa',
        ]);

        $ordenes = $query->orderByDesc('fecha_emision')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes.index', [
            'ordenes' => $ordenes,
            'sucursales' => $sucursales,
        ]);
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $vehiculos = Vehiculo::with('cliente')->orderBy('placa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes.create', [
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
            'orden' => new \App\Models\OrdenTrabajo(),
        ]);
    }

    public function store(OrdenTrabajoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['numero_orden'] = $datos['numero_orden'] ?? 'OT-' . str_pad((string) (OrdenTrabajo::max('id') + 1), 6, '0', STR_PAD_LEFT);
        $datos['fecha_emision'] = now();
        $datos['usuario_recepcion_id'] = auth()->id();
        $datos['estado'] = $datos['estado'] ?? 'recibida';
        $datos['descuento'] = $datos['descuento'] ?? 0;

        OrdenTrabajo::create($datos);

        return $this->redirigirALista('admin.ordenes.index', 'Orden de trabajo creada con éxito.');
    }

    public function show(OrdenTrabajo $ordene): View
    {
        $ordene->load([
            'cliente',
            'vehiculo.modelo.marcaVehiculo',
            'sucursal',
            'usuarioRecepcion',
            'cita',
            'pagos.comprobante',
            'detalles.repuesto',
            'detalles.servicio',
        ]);

        return view('admin.ordenes.show', [
            'orden' => $ordene,
        ]);
    }

    public function edit(OrdenTrabajo $ordene): View
    {
        $ordene->load(['detalles.repuesto', 'detalles.servicio']);

        $clientes = Cliente::orderBy('nombre_completo')->get();
        $vehiculos = Vehiculo::with('cliente')->orderBy('placa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes.edit', [
            'orden' => $ordene,
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
        ]);
    }

    public function update(OrdenTrabajoRequest $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $datos = $request->validated();
        $ordene->update($datos);

        return $this->redirigirConExito('órdenes de trabajo', 'actualizada');
    }

    public function destroy(OrdenTrabajo $ordene): RedirectResponse
    {
        if ($ordene->pagos()->exists()) {
            return back()->with('error', 'No se puede eliminar la orden porque tiene pagos registrados.');
        }

        $ordene->delete();

        return $this->redirigirConExito('órdenes de trabajo', 'eliminada');
    }

    public function toggle(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $estados = ['recibida', 'diagnostico', 'en_proceso', 'finalizada', 'entregada'];
        $actual = array_search($ordene->estado, $estados);
        $nuevo = $estados[min($actual + 1, count($estados) - 1)];
        $ordene->estado = $nuevo;
        $ordene->save();

        return back()->with('success', "La orden fue marcada como {$nuevo}.");
    }

    public function cambiarEstadoOrden(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', 'in:recibida,diagnostico,en_proceso,finalizada,entregada,anulada'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'in' => 'El :attribute seleccionado no es válido.',
        ], ['estado' => 'estado']);

        $nuevoEstado = $request->input('estado');
        $cambios = [];

        if ($nuevoEstado === 'en_proceso' && ! $ordene->fecha_inicio) {
            $cambios['fecha_inicio'] = now();
        }
        if ($nuevoEstado === 'finalizada' && ! $ordene->fecha_fin) {
            $cambios['fecha_fin'] = now();
        }
        if ($nuevoEstado === 'entregada' && ! $ordene->fecha_entrega) {
            $cambios['fecha_entrega'] = now();
        }

        $ordene->fill($cambios);
        $ordene->estado = $nuevoEstado;
        $ordene->save();

        return back()->with('success', "La orden fue actualizada a {$nuevoEstado}.");
    }

    public function cancelar(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $ordene->estado = 'anulada';
        $ordene->save();

        return back()->with('success', 'La orden fue anulada correctamente.');
    }

    public function editRepuestos(OrdenTrabajo $ordene): View
    {
        $ordene->load(['detalles.repuesto', 'detalles.servicio']);

        $repuestos = Repuesto::query()
            ->select('repuestos.*')
            ->leftJoin('inventarios', function ($join) use ($ordene) {
                $join->on('repuestos.id', '=', 'inventarios.repuesto_id')
                    ->where('inventarios.sucursal_id', '=', $ordene->sucursal_id);
            })
            ->selectRaw('COALESCE(inventarios.cantidad_actual, 0) as stock_actual')
            ->where('repuestos.estado', true)
            ->orderBy('repuestos.nombre')
            ->get();

        return view('admin.ordenes.repuestos', [
            'orden' => $ordene,
            'repuestos' => $repuestos,
        ]);
    }

    public function agregarDetalle(OrdenTrabajoDetalleRequest $request, OrdenTrabajo $ordene, OrdenTrabajoService $service): RedirectResponse
    {
        try {
            $service->agregarRepuesto($ordene, $request->validated());
            return back()->with('success', 'Repuesto agregado a la orden correctamente.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function eliminarDetalle(OrdenTrabajo $ordene, DetalleOrdenTrabajo $detalle, OrdenTrabajoService $service): RedirectResponse
    {
        if ($detalle->orden_trabajo_id !== $ordene->id) {
            abort(404);
        }

        $service->eliminarRepuesto($ordene, $detalle);

        return back()->with('success', 'Repuesto eliminado de la orden y stock restaurado.');
    }

    public function repuestosJson(Request $request, OrdenTrabajo $ordene, OrdenTrabajoService $service)
    {
        $busqueda = $request->input('q', '');
        $repuestos = $service->buscarRepuestosConStock($ordene, $busqueda);

        return response()->json($repuestos);
    }

    public function sugerirCompra(OrdenTrabajo $ordene, OrdenTrabajoService $service)
    {
        $sugerencia = $service->sugerirSolicitudCompra($ordene);

        if (! $sugerencia) {
            return back()->with('info', 'No hay repuestos sin stock en esta orden.');
        }

        return redirect()->route('admin.solicitudes-compra.create', [
            'repuestos' => array_column($sugerencia['productos'], 'repuesto_id'),
            'orden_id' => $ordene->id,
        ]);
    }
}
