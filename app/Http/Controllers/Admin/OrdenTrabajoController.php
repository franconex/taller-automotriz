<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrdenTrabajoDetalleRequest;
use App\Http\Requests\Admin\OrdenTrabajoRequest;
use App\Models\AsignacionTrabajo;
use App\Models\Cliente;
use App\Models\DetalleOrdenTrabajo;
use App\Models\EstimacionOrden;
use App\Models\EvidenciaTrabajo;
use App\Models\Mecanico;
use App\Models\MetodoPago;
use App\Models\NotaTrabajo;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use App\Models\Vehiculo;
use App\Notifications\OrdenAsignada;
use App\Services\OrdenTrabajoService;
use Illuminate\Http\JsonResponse;
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
            new Middleware('permiso:ordenes.actualizar_estado', only: ['toggle', 'cambiarEstadoOrden', 'actualizarMiEstado', 'agregarObservacionMecanico', 'subirFoto', 'agregarServicioMecanico', 'finalizarTrabajo', 'iniciarTrabajo', 'registrarDiagnosticoMecanico', 'avanceMecanico', 'pausarMecanico', 'reanudarMecanico', 'finalizarMecanico', 'estimarTiempoMecanico']),
            new Middleware('permiso:ordenes.cancelar', only: ['cancelar']),
            new Middleware('permiso:roles.editar', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $mecanicoAsignadoId = $this->mecanicoDelUsuario();
        $esMecanicoRuta = $request->query('mecanico') || $user->tieneRol('Mecánico');

        $query = OrdenTrabajo::query()
            ->with(['cliente', 'vehiculo', 'sucursal', 'asignaciones.mecanico.empleado']);

        if ($mecanicoAsignadoId) {
            $query->whereHas('asignaciones', fn ($q) => $q->where('mecanico_id', $mecanicoAsignadoId));
        } elseif ($esMecanicoRuta) {
            $asignaciones = AsignacionTrabajo::whereIn('estado', ['pendiente', 'en_proceso', 'esperando_repuestos', 'finalizado'])
                ->pluck('orden_trabajo_id');
            $query->whereIn('id', $asignaciones);
        } else {
            $this->scopeSucursal($query, 'sucursal_id');
        }

        $this->aplicarFiltros($request, $query, ['estado', 'sucursal_id', 'mecanico_id']);
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

        $mecanicos = Mecanico::with('empleado')
            ->when($this->usuarioSucursalId(), fn ($q) => $q->whereHas('empleado', fn ($sq) => $sq->where('sucursal_id', $this->usuarioSucursalId())))
            ->orderBy('disponibilidad')
            ->get();

        $esMecanico = $esMecanicoRuta && $user->tienePermiso('ordenes.actualizar_estado');

        return view('admin.ordenes.index', [
            'ordenes' => $ordenes,
            'sucursales' => $sucursales,
            'mecanicos' => $mecanicos,
            'esMecanico' => $esMecanico,
            'mecanicoAsignadoId' => $mecanicoAsignadoId,
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
        $mecanicos = $this->mecanicosDisponibles();
        $tiposServicio = TipoServicio::with(['servicios' => fn ($q) => $q->where('estado', true)->orderBy('nombre')])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes.create', [
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
            'mecanicos' => $mecanicos,
            'tiposServicio' => $tiposServicio,
            'orden' => new \App\Models\OrdenTrabajo(),
        ]);
    }

    public function store(OrdenTrabajoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['numero_orden'] = $datos['numero_orden'] ?? 'OT-' . str_pad((string) (OrdenTrabajo::max('id') + 1), 6, '0', STR_PAD_LEFT);
        $datos['fecha_emision'] = now();
        $datos['usuario_recepcion_id'] = Auth::id();
        $datos['estado'] = $datos['estado'] ?? 'recibida';
        $datos['descuento'] = $datos['descuento'] ?? 0;

        $mecanicoId = $request->input('mecanico_id');
        $serviciosIds = $request->input('servicios_ids', []);

        DB::transaction(function () use ($datos, $mecanicoId, $serviciosIds) {
            $orden = OrdenTrabajo::create($datos);

            if (!empty($serviciosIds)) {
                $servicios = Servicio::whereIn('id', $serviciosIds)->get();
                $subtotalServicios = 0;
                foreach ($servicios as $servicio) {
                    $orden->detalles()->create([
                        'tipo' => 'servicio',
                        'servicio_id' => $servicio->id,
                        'descripcion' => $servicio->nombre,
                        'cantidad' => 1,
                        'precio_unitario' => (float) $servicio->precio_base,
                        'subtotal' => (float) $servicio->precio_base,
                    ]);
                    $subtotalServicios += (float) $servicio->precio_base;
                }
                $orden->subtotal_servicios = $subtotalServicios;
                $orden->total_general = $subtotalServicios + (float) ($orden->subtotal_repuestos ?? 0) - (float) ($orden->descuento ?? 0);
                $orden->save();
            }

            if ($mecanicoId) {
                $this->crearAsignacion($orden->id, (int) $mecanicoId, $datos['descripcion_problema'] ?? 'Trabajo asignado');
            }
        });

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
            'asignaciones.mecanico.empleado',
            'cita',
            'pagos.comprobante',
            'detalles.repuesto',
            'detalles.servicio',
            'fotos',
            'detalles.asignacionTrabajo.mecanico.empleado',
        ]);

        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo.modelo.marcaVehiculo'])
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->where('estado', 'finalizada')
            ->where('id', '!=', $ordene->id)
            ->orderByDesc('fecha_emision')
            ->limit(50)
            ->get();
        $metodosPago = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return view('admin.ordenes.show', [
            'orden' => $ordene,
            'mecanicoAsignadoId' => $this->mecanicoDelUsuario(),
            'ordenes' => $ordenes,
            'metodosPago' => $metodosPago,
        ]);
    }

    public function edit(OrdenTrabajo $ordene): View
    {
        $ordene->load(['detalles.repuesto', 'detalles.servicio', 'asignaciones.mecanico.empleado']);

        $clientes = Cliente::orderBy('nombre_completo')->get();
        $vehiculos = Vehiculo::with('cliente')->orderBy('placa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();
        $mecanicos = $this->mecanicosDisponibles(null, $ordene->id);
        $tiposServicio = TipoServicio::with(['servicios' => fn ($q) => $q->where('estado', true)->orderBy('nombre')])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.ordenes.edit', [
            'orden' => $ordene,
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
            'mecanicos' => $mecanicos,
            'tiposServicio' => $tiposServicio,
        ]);
    }

    public function update(OrdenTrabajoRequest $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $datos = $request->validated();
        $mecanicoId = $request->input('mecanico_id');

        DB::transaction(function () use ($datos, $mecanicoId, $ordene) {
            $ordene->update($datos);

            if ($mecanicoId !== null && $mecanicoId !== '') {
                $this->reemplazarAsignacion($ordene->id, (int) $mecanicoId, $datos['descripcion_problema'] ?? 'Trabajo asignado');
            }
        });

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
            'estado' => ['required', 'in:recibida,diagnostico,en_proceso,finalizada_mecanico,lista_entrega,finalizada,entregada,anulada'],
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

    public function actualizarMiEstado(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $asignacion = $ordene->asignaciones->first();
        if (!$asignacion) {
            return back()->with('error', 'Esta orden no tiene un mecánico asignado.');
        }

        $mecanicoId = $asignacion->mecanico_id;

        $validados = $request->validate([
            'estado_asignacion' => ['required', 'in:pendiente,en_proceso,esperando_repuestos,finalizado'],
        ]);

        $progresion = ['pendiente', 'en_proceso', 'esperando_repuestos', 'finalizado'];
        $idxActual = array_search($asignacion->estado, $progresion);
        $idxNuevo = array_search($validados['estado_asignacion'], $progresion);

        if ($idxNuevo < $idxActual) {
            return back()->with('error', 'No puedes retroceder a un estado anterior.');
        }

        DB::transaction(function () use ($asignacion, $validados, $mecanicoId) {
            $datosAsig = ['estado' => $validados['estado_asignacion']];
            $datosOrd = [];

            if ($validados['estado_asignacion'] === 'en_proceso' && !$asignacion->fecha_inicio) {
                $datosAsig['fecha_inicio'] = now();
                $datosOrd['fecha_inicio'] = now();
            }
            if ($validados['estado_asignacion'] === 'finalizado') {
                $datosAsig['fecha_finalizacion'] = now();
                $datosOrd['fecha_fin'] = now();
            }
            $asignacion->update($datosAsig);

            $mapEstados = [
                'pendiente' => 'recibida',
                'en_proceso' => 'en_proceso',
                'esperando_repuestos' => 'diagnostico',
                'finalizado' => 'finalizada',
            ];
            $datosOrd['estado'] = $mapEstados[$validados['estado_asignacion']];
            $asignacion->ordenTrabajo->update($datosOrd);

            if ($validados['estado_asignacion'] === 'finalizado') {
                Mecanico::where('id', $mecanicoId)->update(['disponibilidad' => 'disponible']);
            } elseif ($validados['estado_asignacion'] === 'en_proceso') {
                Mecanico::where('id', $mecanicoId)->update(['disponibilidad' => 'ocupado']);
            }
        });

        return back()->with('success', 'Estado actualizado.');
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

    public function agregarObservacionMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $asignacion = $ordene->asignaciones->first();
        if (!$asignacion) {
            return back()->with('error', 'No puedes modificar esta orden.');
        }

        $validados = $request->validate([
            'observaciones' => ['required', 'string', 'max:2000'],
        ]);

        $nueva = now()->format('d/m/Y H:i') . " - [Mecánico] " . $validados['observaciones'];
        $previas = $ordene->observaciones;
        $ordene->update([
            'observaciones' => $previas ? $previas . "\n" . $nueva : $nueva,
        ]);

        $obsAsig = $asignacion->observaciones;
        $asignacion->update([
            'observaciones' => $obsAsig ? $obsAsig . "\n" . $nueva : $nueva,
        ]);

        return back()->with('success', 'Observación agregada correctamente.');
    }

    public function subirFoto(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $asignacion = $ordene->asignaciones->first();
        if (!$asignacion) {
            return back()->with('error', 'No puedes modificar esta orden.');
        }

        $validados = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $archivo = $request->file('foto');
        $nombreOriginal = $archivo->getClientOriginalName();
        $ruta = $archivo->store('ordenes-fotos/' . $ordene->id, 'public');

        $ordene->fotos()->create([
            'usuario_id' => Auth::id(),
            'ruta' => $ruta,
            'nombre_original' => $nombreOriginal,
            'tipo' => $archivo->getClientMimeType(),
            'descripcion' => $validados['descripcion'] ?? null,
        ]);

        return back()->with('success', 'Foto subida correctamente.');
    }

    public function agregarServicioMecanico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        if (!$ordene->asignaciones->first()) {
            return back()->with('error', 'No puedes modificar esta orden.');
        }

        $validados = $request->validate([
            'descripcion_servicio' => ['required', 'string', 'max:500'],
            'costo_servicio' => ['required', 'numeric', 'min:0'],
        ]);

        $servicio = \App\Models\Servicio::firstOrCreate(
            ['nombre' => $validados['descripcion_servicio']],
            ['precio' => $validados['costo_servicio'], 'estado' => true]
        );

        $detalle = $ordene->detalles()->create([
            'tipo' => 'servicio',
            'servicio_id' => $servicio->id,
            'descripcion' => $validados['descripcion_servicio'],
            'cantidad' => 1,
            'precio_unitario' => $validados['costo_servicio'],
            'subtotal' => $validados['costo_servicio'],
        ]);

        $ordene->subtotal_servicios = ($ordene->subtotal_servicios ?? 0) + $validados['costo_servicio'];
        $ordene->total_general = ($ordene->subtotal_servicios ?? 0) + ($ordene->subtotal_repuestos ?? 0) - ($ordene->descuento ?? 0);
        $ordene->save();

        return back()->with('success', 'Servicio agregado a la orden.');
    }

    public function finalizarTrabajo(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        $asignacion = $ordene->asignaciones->first();
        if (!$asignacion || $asignacion->estado === 'finalizado') {
            return back()->with('error', 'No puedes finalizar esta orden.');
        }

        $validados = $request->validate([
            'nota_final' => ['nullable', 'string', 'max:2000'],
            'foto_final' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        DB::transaction(function () use ($ordene, $asignacion, $validados) {
            $asignacion->update([
                'estado' => 'finalizado',
                'fecha_finalizacion' => now(),
            ]);

            $ordene->update([
                'estado' => 'finalizada',
                'fecha_fin' => now(),
            ]);

            Mecanico::where('id', $asignacion->mecanico_id)->update(['disponibilidad' => 'disponible']);

            if (!empty($validados['nota_final'])) {
                $nueva = now()->format('d/m/Y H:i') . " - [Finalización] " . $validados['nota_final'];
                $ordene->update([
                    'observaciones' => $ordene->observaciones ? $ordene->observaciones . "\n" . $nueva : $nueva,
                ]);
            }

            if ($request->hasFile('foto_final')) {
                $archivo = $request->file('foto_final');
                $ruta = $archivo->store('ordenes-fotos/' . $ordene->id, 'public');
                $ordene->fotos()->create([
                    'usuario_id' => Auth::id(),
                    'ruta' => $ruta,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'tipo' => $archivo->getClientMimeType(),
                    'descripcion' => 'Foto final - ' . now()->format('d/m/Y H:i'),
                ]);
            }
        });

        return back()->with('success', 'Trabajo finalizado correctamente. Ya estás disponible para nuevas asignaciones.');
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

    private function mecanicoDelUsuario(): ?int
    {
        $user = Auth::user();
        if (!$user) return null;
        if ($user->empleado_id) {
            $mecanico = Mecanico::where('empleado_id', $user->empleado_id)->first();
            if ($mecanico) return $mecanico->id;
        }
        if ($user->tieneRol('Mecánico')) {
            $mecanico = Mecanico::whereHas('empleado', fn ($q) => $q->where('email', $user->email))->first();
            if ($mecanico) return $mecanico->id;
        }
        return null;
    }

    private function mecanicosDisponibles(?int $sucursalId = null, ?int $exceptoOrdenId = null)
    {
        $sucursalId = $sucursalId ?: $this->usuarioSucursalId();

        $idsOcupados = AsignacionTrabajo::whereIn('estado', ['pendiente', 'en_proceso', 'esperando_repuestos'])
            ->when($exceptoOrdenId, fn ($q) => $q->where('orden_trabajo_id', '!=', $exceptoOrdenId))
            ->pluck('mecanico_id')
            ->unique();

        return Mecanico::with('empleado')
            ->when($sucursalId, fn ($q) => $q->whereHas('empleado', fn ($sq) => $sq->where('sucursal_id', $sucursalId)))
            ->where(function ($q) use ($exceptoOrdenId, $idsOcupados) {
                $q->where('disponibilidad', 'disponible')
                  ->whereNotIn('id', $idsOcupados);
                if ($exceptoOrdenId) {
                    $q->orWhereHas('asignaciones', fn ($aq) => $aq->where('orden_trabajo_id', $exceptoOrdenId));
                }
            })
            ->get()
            ->sortBy('empleado.nombre_completo')
            ->values();
    }

    public function mecanicosPorSucursal(Request $request): JsonResponse
    {
        $sucursalId = $request->input('sucursal_id');
        $exceptoOrdenId = $request->input('excepto_orden_id');

        $mecanicos = $this->mecanicosDisponibles($sucursalId ? (int) $sucursalId : null, $exceptoOrdenId ? (int) $exceptoOrdenId : null);

        return response()->json($mecanicos->map(fn ($m) => [
            'id' => $m->id,
            'nombre' => $m->empleado->nombre_completo ?? 'Mecánico #' . $m->id,
            'disponibilidad' => $m->disponibilidad,
        ]));
    }

    private function crearAsignacion(int $ordenId, int $mecanicoId, string $actividad): void
    {
        AsignacionTrabajo::create([
            'orden_trabajo_id' => $ordenId,
            'mecanico_id' => $mecanicoId,
            'usuario_asignador_id' => Auth::id(),
            'actividad_asignada' => $actividad,
            'fecha_asignacion' => now(),
            'estado' => 'pendiente',
        ]);

        Mecanico::where('id', $mecanicoId)->update(['disponibilidad' => 'ocupado']);

        $mecanico = Mecanico::with('empleado.user')->find($mecanicoId);
        $user = $mecanico?->empleado?->user;
        if ($user) {
            $orden = OrdenTrabajo::find($ordenId);
            if ($orden) {
                $user->notify(new OrdenAsignada($orden));
            }
        }
    }

    private function reemplazarAsignacion(int $ordenId, int $mecanicoId, string $actividad): void
    {
        $asignacionesAnteriores = AsignacionTrabajo::where('orden_trabajo_id', $ordenId)->get();

        foreach ($asignacionesAnteriores as $a) {
            $a->delete();
            Mecanico::where('id', $a->mecanico_id)->update(['disponibilidad' => 'disponible']);
        }

        $this->crearAsignacion($ordenId, $mecanicoId, $actividad);
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
