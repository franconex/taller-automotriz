<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrdenTrabajoDetalleRequest;
use App\Http\Requests\Admin\OrdenTrabajoRequest;
use App\Models\AsignacionTrabajo;
use App\Models\Cliente;
use App\Models\DetalleOrdenTrabajo;
use App\Models\EstimacionOrden;
use App\Models\EvidenciaTrabajo;
use App\Models\NotaTrabajo;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use App\Services\OrdenTrabajoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class OrdenTrabajoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:ordenes.ver', only: ['index', 'show']),
            new Middleware('permiso:ordenes.crear', only: ['create', 'store']),
            new Middleware('permiso:ordenes.editar', only: ['edit', 'update']),
            new Middleware('permiso:ordenes.actualizar_estado', only: ['toggle', 'cambiarEstadoOrden', 'iniciarTrabajo', 'registrarDiagnosticoMecanico', 'avanceMecanico', 'pausarMecanico', 'reanudarMecanico', 'finalizarMecanico', 'estimarTiempoMecanico']),
            new Middleware('permiso:ordenes.cancelar', only: ['cancelar']),
            new Middleware('permiso:roles.editar', only: ['destroy']),
        ];
    }
    public function index(Request $request): View
    {
        $query = OrdenTrabajo::query()->with(['cliente', 'vehiculo', 'sucursal', 'asignaciones.mecanico.empleado']);

        // Si es mecánico, filtrar solo sus órdenes asignadas
        if (Auth::user()->tieneRol('Mecánico')) {
            $mecanicoId = Auth::user()->empleado?->mecanico?->id;
            $query->whereHas('asignaciones', fn ($q) => $q->where('mecanico_id', $mecanicoId));
        }

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

        $mecanicoActualId = Auth::user()->empleado?->mecanico?->id;

        return view('admin.ordenes.index', [
            'ordenes' => $ordenes,
            'sucursales' => $sucursales,
            'mecanicoActualId' => $mecanicoActualId,
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
        Gate::authorize('view', $ordene);

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

    /* =============================================================
       ACCIONES DEL MECÁNICO (migradas desde MecanicoPanelController)
       ============================================================= */

    public function iniciarTrabajo(OrdenTrabajo $ordene): RedirectResponse
    {
        if ($ordene->estado === 'programada') {
            return redirect()->route('admin.ordenes.show', $ordene)
                ->with('error', 'El vehículo aún no ha llegado al taller.');
        }

        Gate::authorize('work', $ordene);

        $this->transicionMecanico($ordene, 'diagnostico');

        $asignacion = $this->asignacionDelMecanico($ordene);
        if ($asignacion && ! $asignacion->fecha_inicio) {
            $asignacion->update(['fecha_inicio' => now()]);
        }

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Trabajo iniciado.');
    }

    public function registrarDiagnosticoMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate(['diagnostico_mecanico' => 'required|string|max:5000']);

        $asignacion = $this->asignacionDelMecanico($ordene);
        $asignacion?->update(['diagnostico_mecanico' => $data['diagnostico_mecanico']]);
        $ordene->update(['diagnostico_general' => $data['diagnostico_mecanico']]);

        $this->transicionMecanico($ordene, 'en_proceso');

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Diagnóstico registrado.');
    }

    public function avanceMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate([
            'porcentaje_avance' => 'required|integer|min:0|max:100',
            'proximo_paso' => 'nullable|string|max:2000',
        ]);

        $asignacion = $this->asignacionDelMecanico($ordene);
        $asignacion?->update([
            'porcentaje_avance' => $data['porcentaje_avance'],
            'proximo_paso' => $data['proximo_paso'] ?? $asignacion->proximo_paso,
        ]);

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Avance registrado.');
    }

    public function pausarMecanico(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);
        $this->transicionMecanico($ordene, 'pausada');
        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Trabajo pausado.');
    }

    public function reanudarMecanico(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $mecanicoId = Auth::user()->empleado?->mecanico?->id;
        $ultimoEstado = $ordene->asignaciones()->where('mecanico_id', $mecanicoId)->value('diagnostico_mecanico');
        $estadoDestino = $ultimoEstado ? 'en_proceso' : 'diagnostico';
        $this->transicionMecanico($ordene, $estadoDestino);

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Trabajo reanudado.');
    }

    public function finalizarMecanico(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $this->transicionMecanico($ordene, 'finalizada');

        $asignacion = $this->asignacionDelMecanico($ordene);
        $asignacion?->update(['fecha_finalizacion' => now(), 'porcentaje_avance' => 100]);

        return redirect()->route('admin.ordenes.index')
            ->with('success', 'Trabajo finalizado correctamente.');
    }

    public function estimarTiempoMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate([
            'duracion_minima_minutos' => ['required', 'integer', 'min:1', 'max:14400'],
            'duracion_maxima_minutos' => ['required', 'integer', 'min:1', 'max:14400', 'gte:duracion_minima_minutos'],
            'fecha_estimada_entrega' => ['nullable', 'date'],
            'observacion_cliente' => ['nullable', 'string', 'max:1000'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ]);

        $asignacion = $this->asignacionDelMecanico($ordene);
        if (! $asignacion) {
            return back()->with('error', 'No tienes una asignación activa en esta orden.');
        }

        $mecanicoId = Auth::user()->empleado?->mecanico?->id;

        DB::transaction(function () use ($ordene, $mecanicoId, $data) {
            EstimacionOrden::create([
                'orden_trabajo_id' => $ordene->id,
                'mecanico_id' => $mecanicoId,
                'duracion_minima_minutos' => $data['duracion_minima_minutos'],
                'duracion_maxima_minutos' => $data['duracion_maxima_minutos'],
                'fecha_estimada_entrega' => $data['fecha_estimada_entrega'] ?? null,
                'observacion_cliente' => $data['observacion_cliente'] ?? null,
                'motivo' => $data['motivo'] ?? null,
            ]);
        });

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Tiempo estimado guardado.');
    }

    public function guardarNotaMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('createNote', $ordene);

        $data = $request->validate([
            'contenido' => 'required|string|max:5000',
            'visible_cliente' => 'boolean',
        ]);

        $asignacion = $this->asignacionDelMecanico($ordene);
        if (! $asignacion) {
            return back()->with('error', 'No tienes una asignación activa en esta orden.');
        }

        NotaTrabajo::create([
            'asignacion_trabajo_id' => $asignacion->id,
            'usuario_id' => Auth::id(),
            'contenido' => $data['contenido'],
            'visible_cliente' => $request->boolean('visible_cliente'),
        ]);

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Nota guardada.');
    }

    public function subirEvidenciaMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('uploadEvidence', $ordene);

        $data = $request->validate([
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $asignacion = $this->asignacionDelMecanico($ordene);
        if (! $asignacion) {
            return back()->with('error', 'No tienes una asignación activa en esta orden.');
        }

        $ruta = $request->file('archivo')->store('evidencias', 'public');

        EvidenciaTrabajo::create([
            'asignacion_trabajo_id' => $asignacion->id,
            'usuario_id' => Auth::id(),
            'archivo' => $ruta,
            'descripcion' => $data['descripcion'] ?? null,
        ]);

        return redirect()->route('admin.ordenes.show', $ordene)
            ->with('success', 'Evidencia subida.');
    }

    private function asignacionDelMecanico(OrdenTrabajo $ordene): ?AsignacionTrabajo
    {
        $mecanicoId = Auth::user()->empleado?->mecanico?->id;
        return $ordene->asignaciones()
            ->where('mecanico_id', $mecanicoId)
            ->whereNull('fecha_finalizacion')
            ->first();
    }

    private function transicionMecanico(OrdenTrabajo $ordene, string $destino): void
    {
        $origen = $ordene->estado;

        if ($origen === 'programada') {
            abort(422, 'El vehículo aún no ha llegado. No puedes cambiar el estado.');
        }

        $transiciones = [
            'recibida' => ['diagnostico'],
            'diagnostico' => ['en_proceso', 'pausada'],
            'en_proceso' => ['pausada', 'finalizada'],
            'pausada' => ['diagnostico', 'en_proceso'],
        ];

        if (! isset($transiciones[$origen]) || ! in_array($destino, $transiciones[$origen])) {
            abort(422, "Transición de '{$origen}' a '{$destino}' no permitida.");
        }

        $cambios = ['estado' => $destino];

        if ($destino === 'en_proceso' && ! $ordene->fecha_inicio) {
            $cambios['fecha_inicio'] = now();
        }

        if ($destino === 'finalizada' && ! $ordene->fecha_fin) {
            $cambios['fecha_fin'] = now();
        }

        $ordene->update($cambios);
    }
}
