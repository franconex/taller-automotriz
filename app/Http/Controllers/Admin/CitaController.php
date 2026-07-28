<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CitaRequest;
use App\Models\Auditoria;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Mecanico;
use App\Models\ModeloVehiculo;
use App\Models\OrdenTrabajo;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use App\Notifications\CitaConfirmada;
use App\Notifications\CitaRechazada;
use App\Notifications\OrdenAsignada;
use App\Notifications\VehiculoRecibido;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CitaController extends AdminController
{
    /* =========================================================
       VISTA PRINCIPAL
       ========================================================= */

    public function index(Request $request): View
    {
        $fechaSeleccionada = $request->input('fecha', now()->toDateString());
        try {
            Carbon::parse($fechaSeleccionada);
        } catch (\Throwable $e) {
            $fechaSeleccionada = now()->toDateString();
        }

        $citasDelDia = Cita::query()
            ->with(['cliente', 'vehiculo', 'sucursal', 'mecanico.empleado', 'servicio'])
            ->deFecha($fechaSeleccionada)
            ->when(true, fn ($q) => $this->scopeSucursal($q, 'sucursal_id'))
            ->orderBy('hora')
            ->get();

        $proximasCitas = Cita::query()
            ->with(['cliente', 'vehiculo', 'sucursal', 'mecanico.empleado', 'servicio'])
            ->futuras()
            ->when(true, fn ($q) => $this->scopeSucursal($q, 'sucursal_id'))
            ->where('fecha', '!=', $fechaSeleccionada)
            ->where('estado', '!=', 'cancelada')
            ->limit(5)
            ->get();

        $clientes   = $this->clientesParaSelect();
        $vehiculos  = Vehiculo::with('cliente')->orderBy('placa')->get();
        $servicios  = Servicio::where('estado', true)->orderBy('nombre')->get();
        $mecanicos  = Mecanico::with('empleado')
            ->where('disponibilidad', 'disponible')
            ->get()
            ->filter(fn ($m) => $m->empleado && $m->empleado->estado)
            ->sortBy(fn ($m) => $m->empleado->nombre_completo)
            ->values();

        $modelos = ModeloVehiculo::with('marca', 'tipoVehiculo')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $sucursales = $this->sucursalesParaSelect();

        $puedeCrear      = $request->user()?->tienePermiso('citas.crear') ?? false;
        $puedeEditar      = $request->user()?->tienePermiso('citas.editar') ?? false;
        $puedeCancelar    = $request->user()?->tienePermiso('citas.cancelar') ?? false;
        $puedeReprogramar = $puedeEditar;
        $puedeConvertir   = $request->user()?->tienePermiso('ordenes.crear') ?? false;

        return view('admin.citas.index', [
            'citasDelDia'        => $citasDelDia,
            'proximasCitas'      => $proximasCitas,
            'fechaSeleccionada'  => $fechaSeleccionada,
            'clientes'           => $clientes,
            'vehiculos'          => $vehiculos,
            'servicios'          => $servicios,
            'mecanicos'          => $mecanicos,
            'modelos'            => $modelos,
            'sucursales'         => $sucursales,
            'mostrarFiltroSucursal' => $sucursales->count() > 1,
            'estados'            => Cita::ESTADOS,
            'puedeCrear'         => $puedeCrear,
            'puedeEditar'        => $puedeEditar,
            'puedeCancelar'      => $puedeCancelar,
            'puedeReprogramar'   => $puedeReprogramar,
            'puedeConvertir'     => $puedeConvertir,
        ]);
    }

    /* =========================================================
       ENDPOINT JSON PARA FullCalendar
       ========================================================= */

    public function eventos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start'        => ['required', 'date'],
            'end'          => ['required', 'date', 'after_or_equal:start'],
            'sucursal_id'  => ['nullable', 'integer', Rule::exists('sucursales', 'id')],
            'servicio_id'  => ['nullable', 'integer', Rule::exists('servicios', 'id')],
            'mecanico_id'  => ['nullable', 'integer', Rule::exists('mecanicos', 'id')],
            'estado'       => ['nullable', 'in:solicitada,propuesta,pendiente,confirmada,atendida,cancelada,rechazada,no_asistio'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'in' => 'El :attribute seleccionado no es válido.',
        ]);

        $start = Carbon::parse($validated['start'])->toDateString();
        $end   = Carbon::parse($validated['end'])->toDateString();

        $query = Cita::query()
            ->with(['cliente:id,nombre_completo', 'vehiculo:id,placa', 'mecanico.empleado:id,nombre_completo', 'servicio:id,nombre'])
            ->enRango($start, $end)
            ->when(true, fn ($q) => $this->scopeSucursal($q, 'sucursal_id'))
            ->when(! empty($validated['sucursal_id']), fn ($q) => $q->where('sucursal_id', $validated['sucursal_id']))
            ->when(! empty($validated['servicio_id']), fn ($q) => $q->where('servicio_id', $validated['servicio_id']))
            ->when(! empty($validated['mecanico_id']), fn ($q) => $q->where('mecanico_id', $validated['mecanico_id']))
            ->when(! empty($validated['estado']), fn ($q) => $q->where('estado', $validated['estado']), fn ($q) => $q->whereIn('estado', ['confirmada', 'atendida']));

        $eventos = $query->get()->map(function (Cita $cita) {
            $cliente  = $cita->cliente?->nombre_completo ?? '—';
            $servicio = $cita->servicio?->nombre ?? $cita->tipo;
            $vehiculo = $cita->vehiculo?->placa ?? '';
            $mecanico = $cita->mecanico?->empleado?->nombre_completo ?? null;
            $color    = $cita->estado_color;

            $fechaStr = $cita->fecha->format('Y-m-d');
            $start = Carbon::parse($fechaStr . ' ' . $cita->hora);

            $horaFin = $cita->hora_fin
                ? Carbon::parse($cita->hora_fin)->format('H:i:s')
                : $start->copy()->addHour()->format('H:i:s');
            $end = Carbon::parse($fechaStr . ' ' . $horaFin);

            return [
                'id'              => $cita->id,
                'title'           => $cliente . ($vehiculo ? " · {$vehiculo}" : ''),
                'start'           => $start->toIso8601String(),
                'end'             => $end->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'cliente'   => $cliente,
                    'vehiculo'  => $vehiculo,
                    'servicio'  => $servicio,
                    'mecanico'  => $mecanico,
                    'estado'    => $cita->estado,
                    'estado_label' => $cita->estado_label,
                    'hora'      => $cita->hora,
                    'hora_fin'  => $cita->hora_fin,
                ],
            ];
        });

        return response()->json($eventos);
    }

    /* =========================================================
       CRUD
       ========================================================= */

    public function store(CitaRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $cita = DB::transaction(function () use ($request) {
                $datos = $request->validated();
                $datos['usuario_id'] = Auth::id();
                $datos['estado'] = $datos['estado'] ?? 'pendiente';
                $datos['descripcion_problema'] = trim($datos['descripcion_problema'] ?? '') ?: 'Cita agendada';
                $datos['deja_vehiculo'] = (bool) ($datos['deja_vehiculo'] ?? false);
                $datos['costo_consulta'] = $datos['costo_consulta'] ?? 0;
                $datos['hora_fin'] = $datos['hora_fin'] ?? $this->calcularHoraFin($datos['hora'], $datos['duracion_minutos'] ?? 60);
                $datos['estado_anterior'] = $datos['estado'];

                return Cita::create($datos);
            });
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                throw $e;
            }
            return back()->withErrors($e->errors())->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'cita' => $cita->load('cliente', 'vehiculo', 'servicio', 'mecanico.empleado')], 201);
        }

        return $this->redirigirALista('admin.citas.index', 'Cita creada con éxito.');
    }

    public function show(Cita $cita): JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        $cita->loadMissing(['cliente', 'vehiculo', 'sucursal', 'servicio', 'mecanico.empleado', 'usuario', 'reprogramadoPor', 'canceladoPor', 'ordenTrabajo']);

        return response()->json([
            'id'                  => $cita->id,
            'cliente'             => $cita->cliente?->nombre_completo,
            'cliente_telefono'    => $cita->cliente?->telefono,
            'vehiculo'            => $cita->vehiculo?->placa,
            'vehiculo_detalle'    => $cita->vehiculo?->placa,
            'servicio'            => $cita->servicio?->nombre ?? ucfirst((string) $cita->tipo),
            'mecanico'            => $cita->mecanico?->empleado?->nombre_completo,
            'sucursal'            => $cita->sucursal?->nombre,
            'fecha'               => $cita->fecha?->format('Y-m-d'),
            'fecha_label'         => $cita->fecha?->format('d/m/Y'),
            'hora'                => $cita->hora,
            'hora_fin'            => $cita->hora_fin,
            'estado'              => $cita->estado,
            'estado_label'        => $cita->estado_label,
            'estado_color'        => $cita->estado_color,
            'descripcion_problema'=> $cita->descripcion_problema,
            'observaciones'       => $cita->observaciones,
            'deja_vehiculo'       => (bool) $cita->deja_vehiculo,
            'costo_consulta'      => (float) $cita->costo_consulta,
            'tipo'                => $cita->tipo,
            'servicio_id'         => $cita->servicio_id,
            'mecanico_id'         => $cita->mecanico_id,
            'sucursal_id'         => $cita->sucursal_id,
            'cliente_id'          => $cita->cliente_id,
            'vehiculo_id'         => $cita->vehiculo_id,
            'usuario'             => $cita->usuario?->nombre,
            'reprogramado_por'   => $cita->reprogramadoPor?->nombre,
            'reprogramado_en'     => $cita->reprogramado_en?->format('d/m/Y H:i'),
            'motivo_reprogramacion' => $cita->motivo_reprogramacion,
            'cancelado_por'       => $cita->canceladoPor?->nombre,
            'cancelado_en'        => $cita->cancelado_en?->format('d/m/Y H:i'),
            'cancelado_motivo'    => $cita->cancelado_motivo,
            'created_at'          => $cita->created_at?->format('d/m/Y H:i'),
            'orden_id'            => $cita->ordenTrabajo?->id,
            'orden_numero'        => $cita->ordenTrabajo?->numero_orden,
            'es_pasable_reprogramar' => $cita->esPasableReprogramar(),
            'es_pasable_confirmar'   => $cita->esPasableConfirmar(),
            'es_pasable_cancelar'    => $cita->esPasableCancelar(),
            'es_pasable_no_asistio'  => $cita->esPasableNoAsistio(),
            'ya_paso'                => $cita->yaPaso(),
            'tiene_orden'            => (bool) $cita->ordenTrabajo,
        ]);
    }

    public function update(CitaRequest $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        try {
            DB::transaction(function () use ($request, $cita) {
                $datos = $request->validated();
                $datos['descripcion_problema'] = trim($datos['descripcion_problema'] ?? '') ?: 'Cita agendada';
                $datos['deja_vehiculo'] = (bool) ($datos['deja_vehiculo'] ?? false);
                $datos['estado_anterior'] = $cita->estado;
                $datos['hora_fin'] = $datos['hora_fin'] ?? $this->calcularHoraFin($datos['hora'], $datos['duracion_minutos'] ?? 60);

                $cita->update($datos);
            });
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                throw $e;
            }
            return back()->withErrors($e->errors())->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'cita' => $cita->fresh()->load('cliente', 'vehiculo', 'servicio', 'mecanico.empleado')]);
        }

        return $this->redirigirConExito('citas', 'actualizada');
    }

    /* =========================================================
       ACCIONES
       ========================================================= */

    public function reprogramar(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableReprogramar()) {
            $msg = 'La cita no se puede reprogramar en su estado actual.';
            return $this->respuestaAccion($request, $msg, false);
        }

        $request->merge(['__accion' => 'reprogramar']);
        $datos = $request->validate([
            'fecha'                 => ['required', 'date'],
            'hora'                  => ['required', 'date_format:H:i'],
            'hora_fin'              => ['nullable', 'date_format:H:i', 'after:hora'],
            'duracion_minutos'      => ['nullable', 'integer', 'min:5', 'max:600'],
            'mecanico_id'           => ['nullable', 'exists:mecanicos,id'],
            'sucursal_id'           => ['required', 'exists:sucursales,id'],
            'motivo_reprogramacion' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'date_format' => 'El campo :attribute no coincide con el formato :format.',
            'after' => 'El campo :attribute debe ser una hora posterior a :date.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe tener al menos :min.',
            'max' => 'El campo :attribute no debe superar :max.',
            'string' => 'El campo :attribute debe ser texto.',
            'exists' => 'El :attribute seleccionado no es válido.',
        ], [
            'motivo_reprogramacion' => 'motivo de reprogramación',
        ]);

        try {
            DB::transaction(function () use ($cita, $datos) {
                $estadoAnterior = $cita->estado;
                $cita->fill([
                    'fecha'                 => $datos['fecha'],
                    'hora'                  => $datos['hora'],
                    'hora_fin'              => $datos['hora_fin'] ?? $this->calcularHoraFin($datos['hora'], $datos['duracion_minutos'] ?? 60),
                    'duracion_minutos'      => $datos['duracion_minutos'] ?? null,
                    'mecanico_id'           => $datos['mecanico_id'] ?? $cita->mecanico_id,
                    'sucursal_id'           => $datos['sucursal_id'],
                    'estado'                => $estadoAnterior, // reprogramar no cambia estado
                    'estado_anterior'       => $estadoAnterior,
                    'motivo_reprogramacion' => $datos['motivo_reprogramacion'],
                    'reprogramado_por_id'   => Auth::id(),
                    'reprogramado_en'       => now(),
                ])->save();

                $this->registrarAuditoria('citas', $cita->id, 'reprogramar', [
                    'antes'  => ['fecha' => null, 'hora' => null],
                    'despues' => ['fecha' => $cita->fecha, 'hora' => $cita->hora],
                ]);
            });
        } catch (ValidationException $e) {
            return $this->respuestaAccion($request, 'Error al reprogramar', false, $e->errors());
        }

        return $this->respuestaAccion($request, 'La cita fue reprogramada correctamente.', true);
    }

    public function confirmar(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableConfirmar()) {
            return $this->respuestaAccion($request, 'Solo se pueden confirmar citas solicitadas, propuestas o pendientes.', false);
        }

        if ($cita->ordenTrabajo) {
            return $this->respuestaAccion($request, 'La cita ya tiene una orden de trabajo asociada.', false);
        }

        $request->merge(['__accion' => 'confirmar']);
        $datos = $request->validate([
            'mecanico_id' => ['required', 'exists:mecanicos,id'],
        ], [], ['mecanico_id' => 'mecánico']);

        $mecanico = Mecanico::with('empleado')->find($datos['mecanico_id']);
        if (! $mecanico || ! $mecanico->empleado?->estado) {
            return $this->respuestaAccion($request, 'El mecánico seleccionado no está activo.', false);
        }

        if ($this->mecanicoOcupado($mecanico->id, $cita->fecha, $cita->hora, $cita->horaFinCalculada())) {
            return $this->respuestaAccion($request, 'El mecánico ya tiene otra cita en ese horario.', false);
        }

        DB::transaction(function () use ($cita, $mecanico) {
            $cita->estado_anterior = $cita->estado;
            $cita->mecanico_id = $mecanico->id;
            $cita->estado = 'confirmada';
            $cita->save();

            $this->registrarAuditoria('citas', $cita->id, 'confirmar', [
                'antes'       => $cita->estado_anterior,
                'despues'     => 'confirmada',
                'mecanico_id' => $mecanico->id,
            ]);
        });

        // Notificar al cliente
        if ($cita->cliente?->user) {
            $cita->cliente->user->notify(new CitaConfirmada($cita));
        }

        // Notificar al mecánico asignado
        $userMecanico = $mecanico->empleado?->user;
        if (! $userMecanico) {
            $userMecanico = User::where('empleado_id', $mecanico->empleado_id)->first();
        }
        if ($userMecanico) {
            $userMecanico->notify(new \App\Notifications\CitaSolicitada($cita));
        }

        $msg = 'Cita confirmada. ' . $cita->cliente?->nombre_completo . ' será atendido por ' . ($mecanico->empleado?->nombre_completo ?? 'el mecánico') . '.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function registrarLlegada(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if ($cita->estado !== 'confirmada') {
            return $this->respuestaAccion($request, 'Solo se puede registrar llegada de citas confirmadas.', false);
        }

        $orden = $cita->ordenTrabajo;
        if (! $orden) {
            return $this->respuestaAccion($request, 'La cita no tiene una orden asociada.', false);
        }

        DB::transaction(function () use ($cita, $orden) {
            $cita->update([
                'estado' => 'atendida',
                'estado_anterior' => $cita->estado,
            ]);

            $orden->update([
                'estado' => 'recibida',
                'fecha_inicio' => now(),
            ]);

            $orden->asignaciones()->whereNull('fecha_finalizacion')->update([
                'fecha_inicio' => now(),
            ]);

            $this->registrarAuditoria('citas', $cita->id, 'registrar_llegada', [
                'cita_estado' => 'atendida',
                'orden_estado' => 'recibida',
            ]);
        });

        // Notificar al mecánico asignado
        $asignacion = $orden->asignaciones()->whereNull('fecha_finalizacion')->first();
        if ($asignacion?->mecanico?->empleado?->user) {
            $asignacion->mecanico->empleado->user->notify(new VehiculoRecibido($orden));
        }

        return $this->respuestaAccion($request, 'Llegada registrada. El vehículo está en taller.', true);
    }

    private function mecanicoOcupado(int $mecanicoId, $fecha, string $hora, string $horaFin): bool
    {
        try {
            $inicio = Carbon::parse($fecha->format('Y-m-d') . ' ' . $hora);
            $fin = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaFin);
        } catch (\Throwable $e) {
            return true;
        }

        return Cita::where('mecanico_id', $mecanicoId)
            ->whereDate('fecha', $fecha)
            ->whereIn('estado', ['confirmada', 'pendiente'])
            ->get()
            ->filter(function ($c) use ($inicio, $fin) {
                $cInicio = Carbon::parse($c->fecha->format('Y-m-d') . ' ' . $c->hora);
                $cFin = Carbon::parse($c->fecha->format('Y-m-d') . ' ' . ($c->hora_fin ?: Carbon::parse($c->hora)->addHour()->format('H:i')));
                return $cInicio->lt($fin) && $cFin->gt($inicio);
            })
            ->isNotEmpty();
    }

    public function cancelar(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableCancelar()) {
            return $this->respuestaAccion($request, 'La cita no se puede cancelar en su estado actual.', false);
        }

        $request->merge(['__accion' => 'cancelar']);
        $datos = $request->validate([
            'cancelado_motivo' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
        ], [
            'cancelado_motivo' => 'motivo de cancelación',
        ]);

        DB::transaction(function () use ($cita, $datos) {
            $estadoAnterior = $cita->estado;
            $cita->estado_anterior   = $estadoAnterior;
            $cita->estado            = 'cancelada';
            $cita->cancelado_motivo  = $datos['cancelado_motivo'];
            $cita->cancelado_por_id  = Auth::id();
            $cita->cancelado_en      = now();
            $cita->save();

            $this->registrarAuditoria('citas', $cita->id, 'cancelar', [
                'antes'  => $estadoAnterior,
                'despues' => 'cancelada',
                'motivo' => $datos['cancelado_motivo'],
            ]);
        });

        return $this->respuestaAccion($request, 'La cita fue cancelada correctamente.', true);
    }

    public function marcarNoAsistio(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableNoAsistio()) {
            return $this->respuestaAccion($request, 'Solo se puede marcar no asistió en citas pasadas y no canceladas/atendidas.', false);
        }

        DB::transaction(function () use ($cita) {
            $estadoAnterior = $cita->estado;
            $cita->estado_anterior = $estadoAnterior;
            $cita->estado = 'no_asistio';
            $cita->save();
            $this->registrarAuditoria('citas', $cita->id, 'no_asistio', [
                'antes'  => $estadoAnterior,
                'despues' => 'no_asistio',
            ]);
        });

        return $this->respuestaAccion($request, 'La cita fue marcada como no asistida.', true);
    }

    public function proponer(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableProponer()) {
            return $this->respuestaAccion($request, 'Solo se pueden proponer citas solicitadas o pendientes.', false);
        }

        $request->merge(['__accion' => 'proponer']);
        $datos = $request->validate([
            'fecha'                 => ['required', 'date'],
            'hora'                  => ['required', 'date_format:H:i'],
            'hora_fin'              => ['nullable', 'date_format:H:i', 'after:hora'],
            'duracion_minutos'      => ['nullable', 'integer', 'min:5', 'max:600'],
            'sucursal_id'           => ['required', 'exists:sucursales,id'],
            'motivo_reprogramacion' => ['required', 'string', 'min:3', 'max:1000'],
        ], [], ['motivo_reprogramacion' => 'motivo de la propuesta']);

        DB::transaction(function () use ($cita, $datos) {
            $cita->update([
                'fecha'                 => $datos['fecha'],
                'hora'                  => $datos['hora'],
                'hora_fin'              => $datos['hora_fin'] ?? $this->calcularHoraFin($datos['hora'], $datos['duracion_minutos'] ?? 60),
                'duracion_minutos'      => $datos['duracion_minutos'] ?? null,
                'sucursal_id'           => $datos['sucursal_id'],
                'estado'                => 'propuesta',
                'estado_anterior'       => $cita->estado,
                'motivo_reprogramacion' => $datos['motivo_reprogramacion'],
                'reprogramado_por_id'   => Auth::id(),
                'reprogramado_en'       => now(),
            ]);
        });

        return $this->respuestaAccion($request, 'Se propuso un nuevo horario al cliente.', true);
    }

    public function rechazar(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if (! $cita->esPasableRechazar()) {
            return $this->respuestaAccion($request, 'No se puede rechazar esta cita en su estado actual.', false);
        }

        $request->merge(['__accion' => 'rechazar']);
        $datos = $request->validate([
            'cancelado_motivo' => ['required', 'string', 'min:3', 'max:1000'],
        ], [], ['cancelado_motivo' => 'motivo de rechazo']);

        DB::transaction(function () use ($cita, $datos) {
            $cita->update([
                'estado'          => 'rechazada',
                'estado_anterior' => $cita->estado,
                'cancelado_motivo'=> $datos['cancelado_motivo'],
                'cancelado_por_id'=> Auth::id(),
                'cancelado_en'    => now(),
            ]);
        });

        // Notificar al cliente
        if ($cita->cliente?->user) {
            $cita->cliente->user->notify(new CitaRechazada($cita));
        }

        return $this->respuestaAccion($request, 'La cita fue rechazada.', true);
    }

    public function convertirEnOrden(Request $request, Cita $cita): RedirectResponse|JsonResponse
    {
        $this->asegurarSucursalPermitida($cita);

        if ($cita->ordenTrabajo) {
            return $this->respuestaAccion($request, 'La cita ya tiene una orden de trabajo asociada.', false);
        }

        if ($cita->estaCancelada() || $cita->estaAtendida() || $cita->estado === 'no_asistio') {
            return $this->respuestaAccion($request, 'No se puede convertir a orden una cita cancelada o finalizada.', false);
        }

        try {
            $orden = DB::transaction(function () use ($cita) {
                $siguienteId = (OrdenTrabajo::withTrashed()->max('id') ?? 0) + 1;
                $numeroOrden  = 'OT-' . str_pad((string) $siguienteId, 6, '0', STR_PAD_LEFT);

                $orden = OrdenTrabajo::create([
                    'numero_orden'          => $numeroOrden,
                    'cliente_id'            => $cita->cliente_id,
                    'vehiculo_id'           => $cita->vehiculo_id,
                    'sucursal_id'           => $cita->sucursal_id,
                    'usuario_recepcion_id'  => Auth::id(),
                    'cita_id'               => $cita->id,
                    'fecha_emision'         => now(),
                    'descripcion_problema'  => $cita->descripcion_problema,
                    'estado'                => 'recibida',
                    'descuento'             => 0,
                    'kilometraje_ingreso'   => 0,
                ]);

                // Si la cita ya tiene mecánico asignado, crear asignación automáticamente
                if ($cita->mecanico_id) {
                    AsignacionTrabajo::create([
                        'orden_trabajo_id'     => $orden->id,
                        'mecanico_id'          => $cita->mecanico_id,
                        'usuario_asignador_id' => Auth::id(),
                        'actividad_asignada'   => $cita->descripcion_problema ?? 'Atención de orden',
                        'prioridad'            => 'normal',
                        'estado'               => 'pendiente',
                        'fecha_asignacion'     => now(),
                    ]);
                }

                $cita->estado_anterior = $cita->estado;
                $cita->estado = 'atendida';
                $cita->save();

                $this->registrarAuditoria('citas', $cita->id, 'convertir_orden', [
                    'orden_id'     => $orden->id,
                    'orden_numero' => $orden->numero_orden,
                ]);

                return $orden;
            });
        } catch (\Throwable $e) {
            return $this->respuestaAccion($request, 'No se pudo crear la orden: ' . $e->getMessage(), false);
        }

        $msg = "La cita fue convertida en la orden {$orden->numero_orden}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => $msg, 'orden_id' => $orden->id]);
        }

        return back()->with('success', $msg);
    }

    /* =========================================================
       ENDPOINTS JSON AUXILIARES
       ========================================================= */

    public function tablaDia(Request $request): JsonResponse
    {
        $fecha = $request->input('fecha', now()->toDateString());
        try {
            Carbon::parse($fecha);
        } catch (\Throwable $e) {
            $fecha = now()->toDateString();
        }

        $citas = Cita::query()
            ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa', 'mecanico.empleado:id,nombre_completo', 'servicio:id,nombre'])
            ->deFecha($fecha)
            ->when(true, fn ($q) => $this->scopeSucursal($q, 'sucursal_id'))
            ->orderBy('hora')
            ->get();

        $data = $citas->map(function (Cita $c) {
            return [
                'id'           => $c->id,
                'hora'         => $c->hora,
                'hora_fin'     => $c->hora_fin,
                'cliente'      => $c->cliente?->nombre_completo ?? '—',
                'telefono'     => $c->cliente?->telefono,
                'vehiculo'     => $c->vehiculo?->placa,
                'servicio'     => $c->servicio?->nombre ?? ucfirst((string) $c->tipo),
                'mecanico'     => $c->mecanico?->empleado?->nombre_completo ?? '—',
                'estado'       => $c->estado,
                'estado_label' => $c->estado_label,
            ];
        });

        return response()->json([
            'fecha'  => $fecha,
            'citas'  => $data,
            'puede_editar'    => $request->user()?->tienePermiso('citas.editar') ?? false,
            'puede_cancelar'  => $request->user()?->tienePermiso('citas.cancelar') ?? false,
        ]);
    }

    public function proximas(Request $request): JsonResponse
    {
        $desde = $request->input('desde', now()->toDateString());

        $citas = Cita::query()
            ->with(['cliente:id,nombre_completo', 'mecanico.empleado:id,nombre_completo', 'servicio:id,nombre'])
            ->where('fecha', '>=', $desde)
            ->where('estado', '!=', 'cancelada')
            ->when(true, fn ($q) => $this->scopeSucursal($q, 'sucursal_id'))
            ->orderBy('fecha')
            ->orderBy('hora')
            ->limit(5)
            ->get();

        $data = $citas->map(function (Cita $c) {
            return [
                'id'           => $c->id,
                'fecha'        => $c->fecha->format('Y-m-d'),
                'fecha_label'  => $c->fecha->format('d/m/Y'),
                'hora'         => $c->hora,
                'cliente'      => $c->cliente?->nombre_completo ?? '—',
                'servicio'     => $c->servicio?->nombre ?? ucfirst((string) $c->tipo),
                'estado'       => $c->estado,
                'estado_label' => $c->estado_label,
                'estado_color' => $c->estado_color,
            ];
        });

        return response()->json(['citas' => $data]);
    }

    /* =========================================================
       QUICK CREATE (cliente / vehículo desde el modal de cita)
       ========================================================= */

    public function quickCliente(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre_completo' => ['required', 'string', 'max:150'],
            'ci'              => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('clientes', 'ci')->whereNull('deleted_at')],
            'telefono'        => ['required', 'string', 'max:20'],
            'email'           => ['nullable', 'email', 'max:100'],
        ], [], [
            'nombre_completo' => 'nombre completo',
            'ci'              => 'cédula de identidad',
            'telefono'        => 'teléfono',
            'email'           => 'correo electrónico',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $datos = $validator->validated();
        $datos['estado'] = true;
        $datos['fecha_registro'] = now();

        $cliente = Cliente::create($datos);

        return response()->json([
            'ok'      => true,
            'message' => 'Cliente registrado.',
            'cliente' => ['id' => $cliente->id, 'nombre_completo' => $cliente->nombre_completo],
        ]);
    }

    public function quickVehiculo(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'marca'      => ['required', 'string', 'max:100'],
            'modelo'     => ['required', 'string', 'max:100'],
            'placa'      => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'placa')->whereNull('deleted_at')],
            'color'      => ['nullable', 'string', 'max:50'],
            'anio'       => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
        ], [], [
            'cliente_id' => 'cliente',
            'marca'      => 'marca',
            'modelo'     => 'modelo',
            'placa'      => 'placa',
            'color'      => 'color',
            'anio'       => 'año',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $datos = $validator->validated();
        $datos['estado'] = true;
        $datos['kilometraje_actual'] = 0;

        $vehiculo = Vehiculo::create($datos);
        $vehiculo->load('cliente');

        return response()->json([
            'ok'      => true,
            'message' => 'Vehículo registrado.',
            'vehiculo' => [
                'id'         => $vehiculo->id,
                'cliente_id' => $vehiculo->cliente_id,
                'label'      => $vehiculo->placa . ' — ' . ($vehiculo->cliente?->nombre_completo ?? ''),
            ],
        ]);
    }

    /* =========================================================
       HELPERS
       ========================================================= */

    protected function asegurarSucursalPermitida(Cita $cita): void
    {
        $sucursalId = $this->usuarioSucursalId();
        if ($sucursalId !== null && (int) $cita->sucursal_id !== (int) $sucursalId) {
            abort(403, 'No tienes acceso a esta cita.');
        }
    }

    protected function clientesParaSelect()
    {
        return Cliente::orderBy('nombre_completo')->get();
    }

    protected function sucursalesParaSelect()
    {
        $q = Sucursal::orderBy('nombre');
        if ($sucursalId = $this->usuarioSucursalId()) {
            $q->where('id', $sucursalId);
        }
        return $q->get();
    }

    protected function calcularHoraFin(string $hora, int $minutos): string
    {
        try {
            return Carbon::createFromFormat('H:i', $hora)->addMinutes($minutos)->format('H:i');
        } catch (\Throwable $e) {
            return $hora;
        }
    }

    protected function respuestaAccion(Request $request, string $mensaje, bool $ok = true, array $errors = []): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => $ok,
                'message' => $mensaje,
                'errors' => $errors,
            ], $ok ? 200 : 422);
        }

        return back()->with($ok ? 'success' : 'error', $mensaje);
    }

    protected function registrarAuditoria(string $entidad, int $entidadId, string $accion, array $detalle = []): void
    {
        try {
            $antes  = $detalle['antes']  ?? null;
            $despues = $detalle['despues'] ?? null;
            $motivo  = $detalle['motivo']  ?? null;
            $extra   = $detalle;
            unset($extra['antes'], $extra['despues'], $extra['motivo']);

            Auditoria::create([
                'usuario_id'       => Auth::id(),
                'modulo'           => $entidad,
                'entidad_tipo'     => $entidad,
                'entidad_id'       => $entidadId,
                'accion'           => $accion,
                'datos_anteriores'  => $antes !== null
                    ? array_merge((array) $antes, (array) ($extra ?: []))
                    : ($extra ?: null),
                'datos_nuevos'      => $despues !== null
                    ? array_merge((array) $despues, $motivo ? ['motivo' => $motivo] : [])
                    : null,
                'ip_address'       => request()?->ip(),
                'user_agent'       => request()?->userAgent(),
                'fecha_accion'     => now(),
            ]);
        } catch (\Throwable $e) {
            // No fallar la operación por error de auditoría
        }
    }
}
