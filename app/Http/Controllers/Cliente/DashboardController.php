<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\CitaStoreRequest;
use App\Models\Auditoria;
use App\Models\Autorizacion;
use App\Models\Cita;
use App\Models\Comprobante;
use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vehiculo;
use App\Notifications\AutorizacionAprobada;
use App\Notifications\CitaSolicitada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function clienteId(): ?int
    {
        return Auth::user()->cliente_id;
    }

    public function index(): View
    {
        $clienteId = $this->clienteId();

        $proximaCita = Cita::where('cliente_id', $clienteId)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->whereDate('fecha', '>=', now())
            ->with('vehiculo', 'sucursal')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        $vehiculos = Vehiculo::where('cliente_id', $clienteId)
            ->where('estado', true)
            ->get();

        $ordenActiva = OrdenTrabajo::where('cliente_id', $clienteId)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso', 'pausada'])
            ->with('vehiculo')
            ->first();

        $avance = 0;
        $ultimaActualizacion = null;
        if ($ordenActiva) {
            $asignacion = $ordenActiva->asignaciones()
                ->whereNotNull('porcentaje_avance')
                ->latest()
                ->first();
            if ($asignacion) {
                $avance = $asignacion->porcentaje_avance;
                $ultimaActualizacion = $asignacion->updated_at;
            }
        }

        $enTaller = OrdenTrabajo::where('cliente_id', $clienteId)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso'])
            ->count();

        $saldoPendiente = OrdenTrabajo::where('cliente_id', $clienteId)
            ->whereNotIn('estado', ['entregada', 'anulada'])
            ->get()
            ->sum(function ($o) {
                $pagado = $o->pagos()->where('estado', 'confirmado')->sum('monto');
                return max(0, (float) $o->total_general - $pagado);
            });

        $cliente = Auth::user()->cliente;

        $autorizacionesPendientes = Autorizacion::where('estado', 'pendiente')
            ->where(function ($q) use ($clienteId) {
                $q->whereHas('ordenTrabajo', fn ($sq) => $sq->where('cliente_id', $clienteId))
                  ->orWhereHas('cita', fn ($sq) => $sq->where('cliente_id', $clienteId));
            })
            ->count();

        return view('cliente.dashboard', compact(
            'cliente', 'proximaCita', 'vehiculos', 'ordenActiva',
            'avance', 'ultimaActualizacion', 'enTaller', 'saldoPendiente',
            'autorizacionesPendientes'
        ));
    }

    public function vehiculos(): View
    {
        $vehiculos = Vehiculo::where('cliente_id', $this->clienteId())
            ->where('estado', true)
            ->with('modelo.marca')
            ->get();

        return view('cliente.vehiculos', compact('vehiculos'));
    }

    public function vehiculoShow(Vehiculo $vehiculo): View
    {
        Gate::authorize('view', $vehiculo);

        $vehiculo->load('modelo.marca');

        return view('cliente.vehiculo-show', compact('vehiculo'));
    }

    public function vehiculoCreate(): View
    {
        return view('cliente.vehiculo-create');
    }

    public function vehiculoStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'placa' => ['required', 'string', 'max:20', 'unique:vehiculos,placa'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:2099'],
            'color' => ['nullable', 'string', 'max:50'],
            'numero_chasis' => ['nullable', 'string', 'max:50'],
            'kilometraje_actual' => ['nullable', 'integer', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'placa' => 'placa',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'anio' => 'año',
            'color' => 'color',
            'numero_chasis' => 'chasis',
            'kilometraje_actual' => 'kilometraje',
            'observaciones' => 'observaciones',
        ]);

        $data['cliente_id'] = $this->clienteId();
        $data['estado'] = true;
        $data['kilometraje_actual'] = $data['kilometraje_actual'] ?? 0;

        Vehiculo::create($data);

        return redirect()->route('cliente.vehiculos')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function citas(): View
    {
        $citas = Cita::where('cliente_id', $this->clienteId())
            ->with('vehiculo', 'sucursal', 'servicio')
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();

        return view('cliente.citas', compact('citas'));
    }

    public function citaShow(Cita $cita): View
    {
        Gate::authorize('view', $cita);

        $cita->load('vehiculo', 'sucursal', 'servicio');

        return view('cliente.cita-show', compact('cita'));
    }

    public function seguimiento(): View
    {
        $clienteId = $this->clienteId();

        $ordenActiva = OrdenTrabajo::where('cliente_id', $clienteId)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso', 'pausada', 'finalizada_mecanico', 'lista_entrega'])
            ->with([
                'vehiculo',
                'asignaciones.mecanico.empleado',
                'asignaciones.notasVisiblesCliente',
                'detalles.servicio',
                'detalles.repuesto',
                'serviciosMecanico',
                'repuestosMecanico.repuesto',
            ])
            ->first();

        $asignacion = $ordenActiva?->asignaciones->first();
        $estimacion = $ordenActiva?->estimaciones()->latest()->first();
        $avances = $ordenActiva?->avances()->where('visible_cliente', true)->latest()->get() ?? collect();
        $diagnostico = $ordenActiva?->diagnosticos()->latest()->first();

        // Autorizaciones pendientes del cliente (cotizaciones)
        $cotizacionesPendientes = Autorizacion::where('estado', 'pendiente')
            ->where(function ($q) use ($clienteId) {
                $q->whereHas('ordenTrabajo', fn ($sq) => $sq->where('cliente_id', $clienteId))
                  ->orWhereHas('cita', fn ($sq) => $sq->where('cliente_id', $clienteId));
            })
            ->with(['cita.vehiculo', 'cita.mecanico.empleado:id,nombre_completo', 'servicios', 'repuestos.repuesto'])
            ->orderByDesc('fecha_solicitud')
            ->get();

        return view('cliente.seguimiento', compact(
            'ordenActiva', 'asignacion', 'estimacion', 'avances', 'diagnostico', 'cotizacionesPendientes'
        ));
    }

    public function historial(): View
    {
        $ordenes = OrdenTrabajo::where('cliente_id', $this->clienteId())
            ->whereIn('estado', ['finalizada', 'entregada', 'anulada'])
            ->with('vehiculo')
            ->orderByDesc('fecha_emision')
            ->get();

        return view('cliente.historial', compact('ordenes'));
    }

    public function autorizaciones(): View
    {
        $clienteId = $this->clienteId();

        $autorizaciones = Autorizacion::where(function ($q) use ($clienteId) {
                $q->whereHas('ordenTrabajo', fn ($sq) => $sq->where('cliente_id', $clienteId))
                  ->orWhereHas('cita', fn ($sq) => $sq->where('cliente_id', $clienteId));
            })
            ->with(['ordenTrabajo.vehiculo', 'cita.vehiculo', 'cita.mecanico.empleado:id,nombre_completo', 'usuarioSolicitante'])
            ->latest('fecha_solicitud')
            ->get();

        return view('cliente.autorizaciones', compact('autorizaciones'));
    }

    public function autorizacionResponder(Request $request, Autorizacion $autorizacione): RedirectResponse
    {
        Gate::authorize('respond', $autorizacione);

        $data = $request->validate([
            'accion' => ['required', 'in:autorizada,rechazada,requiere_informacion'],
            'comentario_cliente' => ['nullable', 'string', 'max:2000'],
        ]);

        $ordenCreada = null;

        DB::transaction(function () use ($request, $autorizacione, $data, &$ordenCreada) {
            $estadoAnterior = $autorizacione->estado;

            $autorizacione->update([
                'estado' => $data['accion'],
                'comentario_cliente' => $data['comentario_cliente'] ?? null,
                'fecha_respuesta' => now(),
                'respondido_por_id' => Auth::id(),
            ]);

            // Si autoriza: crear orden si no existe, o cambiar estado si ya existe
            if ($data['accion'] === 'autorizada') {
                $orden = $autorizacione->ordenTrabajo;

                if (! $orden && $autorizacione->cita_id) {
                    // Cotización desde cita → crear orden + asignación
                    $cita = $autorizacione->cita;
                    $mecanicoId = $cita?->mecanico_id ?? $autorizacione->usuario_solicitante?->empleado?->mecanico?->id;

                    $siguienteId = (\App\Models\OrdenTrabajo::withTrashed()->max('id') ?? 0) + 1;
                    $numeroOrden = 'OT-' . str_pad((string) $siguienteId, 6, '0', STR_PAD_LEFT);

                    $orden = \App\Models\OrdenTrabajo::create([
                        'numero_orden'          => $numeroOrden,
                        'cliente_id'            => $cita?->cliente_id ?? $autorizacione->cita?->cliente_id,
                        'vehiculo_id'           => $cita?->vehiculo_id,
                        'sucursal_id'           => $cita?->sucursal_id,
                        'usuario_recepcion_id'  => $autorizacione->usuario_solicitante_id,
                        'cita_id'               => $cita?->id,
                        'fecha_emision'         => now(),
                        'descripcion_problema'  => $autorizacione->descripcion,
                        'estado'                => 'recibida',
                        'descuento'             => 0,
                        'kilometraje_ingreso'   => 0,
                    ]);

                    // Mover servicios de la cotización a la orden
                    \App\Models\OrdenServicio::where('autorizacion_id', $autorizacione->id)
                        ->update(['orden_trabajo_id' => $orden->id, 'autorizacion_id' => null]);

                    // Mover repuestos de la cotización a la orden
                    \App\Models\OrdenRepuesto::where('autorizacion_id', $autorizacione->id)
                        ->update(['orden_trabajo_id' => $orden->id, 'autorizacion_id' => null]);

                    // Asignar al mecánico
                    if ($mecanicoId) {
                        \App\Models\AsignacionTrabajo::create([
                            'orden_trabajo_id'     => $orden->id,
                            'mecanico_id'          => $mecanicoId,
                            'usuario_asignador_id' => $autorizacione->usuario_solicitante_id,
                            'actividad_asignada'   => $autorizacione->titulo,
                            'prioridad'            => 'normal',
                            'estado'               => 'pendiente',
                            'fecha_asignacion'     => now(),
                        ]);
                    }

                    // Copiar diagnóstico de la cotización a la orden
                    if ($autorizacione->diagnostico_mecanico) {
                        \App\Models\DiagnosticoOrden::create([
                            'orden_trabajo_id'    => $orden->id,
                            'mecanico_id'          => $mecanicoId,
                            'problema_encontrado'  => $autorizacione->diagnostico_mecanico,
                            'observacion_cliente'  => $autorizacione->descripcion,
                        ]);
                    }

                    // Copiar foto de diagnóstico a la primera asignación
                    $asignacionCreada = $orden->asignaciones()->whereNull('fecha_finalizacion')->first();
                    if ($autorizacione->foto_diagnostico && $asignacionCreada) {
                        \App\Models\EvidenciaTrabajo::create([
                            'asignacion_trabajo_id' => $asignacionCreada->id,
                            'usuario_id'            => $autorizacione->usuario_solicitante_id,
                            'archivo'               => $autorizacione->foto_diagnostico,
                            'descripcion'           => 'Foto de diagnóstico de cotización',
                        ]);
                    }

                    // Cita → atendida
                    if ($cita) {
                        $cita->update(['estado' => 'atendida', 'estado_anterior' => $cita->estado]);
                    }

                    // Vincular autorización con la orden
                    $autorizacione->update(['orden_trabajo_id' => $orden->id]);

                    $ordenCreada = $orden;
                } elseif ($orden) {
                    // Ya existía orden → cambiar estado
                    if (in_array($orden->estado, ['programada', 'recibida', 'diagnostico', 'pendiente_autorizacion'])) {
                        $orden->update(['estado' => 'en_proceso']);
                    }
                }
            }

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'responder',
                'modulo' => 'autorizaciones',
                'entidad_tipo' => 'Autorizacion',
                'entidad_id' => $autorizacione->id,
                'datos_anteriores' => json_encode(['estado' => $estadoAnterior]),
                'datos_nuevos' => json_encode(['estado' => $data['accion']]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'fecha_accion' => now(),
            ]);
        });

        // Notificar al mecánico si autorizó
        if ($data['accion'] === 'autorizada') {
            $orden = $ordenCreada ?? $autorizacione->ordenTrabajo;
            if ($orden) {
                $asignacion = $orden->asignaciones()->whereNull('fecha_finalizacion')->first();
                $mecanico = $asignacion?->mecanico;
                $userMecanico = $mecanico?->empleado?->user;
                if (! $userMecanico) {
                    $userMecanico = \App\Models\User::where('empleado_id', $mecanico?->empleado_id)->first();
                }
                if ($userMecanico) {
                    $userMecanico->notify(new AutorizacionAprobada($autorizacione));
                }
            }
        }

        $mensaje = match($data['accion']) {
            'autorizada' => 'Autorizaste la cotización. ' . ($ordenCreada ? 'La orden fue creada y el mecánico comenzará a trabajar.' : 'El mecánico comenzará a trabajar.'),
            'rechazada' => 'Rechazaste la cotización.',
            'requiere_informacion' => 'Solicitaste más información.',
            default => 'Respuesta registrada.',
        };

        return redirect()->route('cliente.autorizaciones')
            ->with('success', $mensaje);
    }

    public function ordenShow(OrdenTrabajo $ordene): View
    {
        Gate::authorize('view', $ordene);

        $ordene->load([
            'vehiculo',
            'detalles.servicio',
            'detalles.repuesto',
            'asignaciones.mecanico.empleado',
            'asignaciones.notasVisiblesCliente',
        ]);

        $pagos = $ordene->pagos()->where('estado', 'confirmado')->with('metodoPago')->get();
        $comprobante = $ordene->pagos()->where('estado', 'confirmado')->first()?->comprobante;

        return view('cliente.orden-show', compact('ordene', 'pagos', 'comprobante'));
    }

    public function pagos(): View
    {
        $ordenesConPagos = OrdenTrabajo::where('cliente_id', $this->clienteId())
            ->whereHas('pagos', fn ($q) => $q->where('estado', 'confirmado'))
            ->with(['vehiculo', 'pagos.metodoPago', 'pagos.comprobante'])
            ->orderByDesc('fecha_emision')
            ->get();

        $ordenesPendientes = OrdenTrabajo::where('cliente_id', $this->clienteId())
            ->whereNotIn('estado', ['entregada', 'anulada'])
            ->with('vehiculo')
            ->orderByDesc('fecha_emision')
            ->get()
            ->filter(function ($o) {
                $pagado = $o->pagos()->where('estado', 'confirmado')->sum('monto');
                return (float) $o->total_general > (float) $pagado;
            });

        return view('cliente.pagos', compact('ordenesConPagos', 'ordenesPendientes'));
    }

    public function pagoShow(Pago $pago): View
    {
        Gate::authorize('view', $pago);

        $pago->load(['ordenTrabajo.vehiculo', 'metodoPago', 'comprobante']);

        return view('cliente.pago-show', compact('pago'));
    }

    public function comprobanteShow(Comprobante $comprobante): View
    {
        Gate::authorize('view', $comprobante);

        $comprobante->load(['pago.ordenTrabajo.vehiculo', 'pago.metodoPago']);

        return view('cliente.comprobante-show', compact('comprobante'));
    }

    public function citaCreate(): View
    {
        $vehiculos = Vehiculo::where('cliente_id', $this->clienteId())
            ->where('estado', true)->get();

        $sucursales = Sucursal::where('estado', true)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('nombre')
            ->get();

        return view('cliente.cita-create', compact('vehiculos', 'sucursales'));
    }

    public function citaStore(CitaStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['cliente_id'] = $this->clienteId();
        $data['usuario_id'] = Auth::id();
        $data['estado'] = 'solicitada';
        $data['hora_fin'] = \Carbon\Carbon::parse($data['hora'])->addHour()->format('H:i');
        $data['deja_vehiculo'] = $request->boolean('deja_vehiculo');
        $data['descripcion_problema'] = trim($data['descripcion_problema'] ?? '') ?: 'Sin descripción';

        $cita = Cita::create($data);

        $recepcionistas = User::whereHas('rol', fn ($q) => $q->where('nombre', 'Recepcionista'))->get();
        Notification::send($recepcionistas, new CitaSolicitada($cita));

        return redirect()->route('cliente.citas')
            ->with('success', 'Cita solicitada correctamente. Recibirás la confirmación pronto.');
    }

    public function citaCancel(Request $request, Cita $cita): RedirectResponse
    {
        Gate::authorize('view', $cita);

        if (! $cita->esPasableCancelar()) {
            return back()->with('error', 'No puedes cancelar esta cita en su estado actual.');
        }

        $data = $request->validate(['cancelado_motivo' => 'nullable|string|max:1000']);

        $cita->update([
            'estado' => 'cancelada',
            'estado_anterior' => $cita->estado,
            'cancelado_motivo' => $data['cancelado_motivo'] ?? 'Cancelada por el cliente',
            'cancelado_por_id' => Auth::id(),
            'cancelado_en' => now(),
        ]);

        return redirect()->route('cliente.citas')
            ->with('success', 'Cita cancelada correctamente.');
    }

    public function perfil(): View
    {
        $cliente = Auth::user()->cliente;

        return view('cliente.perfil', compact('cliente'));
    }

    public function perfilUpdate(Request $request)
    {
        $cliente = Auth::user()->cliente;

        $data = $request->validate([
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente->update($data);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
