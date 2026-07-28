<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTrabajo;
use App\Models\AvanceOrden;
use App\Models\DiagnosticoOrden;
use App\Models\EstimacionOrden;
use App\Models\EvidenciaTrabajo;
use App\Models\OrdenRepuesto;
use App\Models\OrdenServicio;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrdenController extends Controller
{
    private function mecanicoId(): ?int
    {
        return Auth::user()->empleado?->mecanico?->id;
    }

    private function ordenAsignada(OrdenTrabajo $orden): OrdenTrabajo
    {
        $mecanicoId = $this->mecanicoId();
        $asignacion = AsignacionTrabajo::where('orden_trabajo_id', $orden->id)
            ->where('mecanico_id', $mecanicoId)
            ->first();

        if (! $asignacion) {
            abort(403, 'No tienes acceso a esta orden.');
        }

        return $orden;
    }

    public function index(): View
    {
        $mecanicoId = $this->mecanicoId();

        $ordenes = OrdenTrabajo::whereHas('asignaciones', fn ($q) => $q->where('mecanico_id', $mecanicoId))
            ->with(['cliente', 'vehiculo', 'asignaciones.mecanico.empleado'])
            ->orderByDesc('fecha_emision')
            ->paginate(20);

        return view('mecanico.ordenes.index', compact('ordenes'));
    }

    public function show(OrdenTrabajo $orden): View
    {
        $this->ordenAsignada($orden);

        $asignacion = AsignacionTrabajo::where('orden_trabajo_id', $orden->id)
            ->where('mecanico_id', $this->mecanicoId())
            ->first();

        $diagnostico = $orden->diagnosticos()->latest()->first();
        $servicios = $orden->servicios()->get();
        $estimacion = $orden->estimaciones()->latest()->first();
        $avances = $orden->avances()->latest()->get();
        $repuestos = $orden->repuestosMecanico()->get();
        $evidencias = $orden->evidenciasTrabajo()->latest()->get();

        $orden->load('cliente', 'vehiculo.modelo', 'cita');

        return view('mecanico.ordenes.show', compact(
            'orden', 'asignacion', 'diagnostico', 'servicios',
            'estimacion', 'avances', 'repuestos', 'evidencias'
        ));
    }

    public function diagnostico(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'problema_encontrado' => 'nullable|string|max:5000',
            'causa_probable' => 'nullable|string|max:5000',
            'recomendacion' => 'nullable|string|max:5000',
            'observacion_cliente' => 'nullable|string|max:2000',
            'observacion_interna' => 'nullable|string|max:2000',
        ]);

        DiagnosticoOrden::create(array_merge($data, [
            'orden_trabajo_id' => $orden->id,
            'mecanico_id' => $this->mecanicoId(),
        ]));

        if ($orden->estado === 'recibida' || $orden->estado === 'programada') {
            $orden->update(['estado' => 'diagnostico']);
        }

        $asignacion = $this->asignacionActiva($orden);
        if ($asignacion && ! $asignacion->fecha_inicio) {
            $asignacion->update(['fecha_inicio' => now()]);
        }

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Diagnóstico registrado correctamente.');
    }

    public function servicios(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'subservicio_id' => 'nullable|exists:subservicios,id',
            'observacion' => 'nullable|string|max:2000',
        ]);

        $servicio = \App\Models\Servicio::find($data['servicio_id']);
        $subservicio = $data['subservicio_id'] ? \App\Models\Subservicio::find($data['subservicio_id']) : null;

        OrdenServicio::create([
            'orden_trabajo_id' => $orden->id,
            'servicio_id' => $data['servicio_id'],
            'subservicio_id' => $data['subservicio_id'],
            'nombre_servicio' => $servicio->nombre,
            'nombre_subservicio' => $subservicio?->nombre,
            'precio_base' => $subservicio?->precio_base ?? $servicio->precio_base,
            'tiempo_estimado_minutos' => $subservicio?->duracion_estimada_minutos ?? $servicio->duracion_estimada_minutos,
            'observacion' => $data['observacion'] ?? null,
        ]);

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Servicio agregado a la orden.');
    }

    public function tiempo(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'tiempo_minimo_minutos' => ['required', 'integer', 'min:1'],
            'tiempo_maximo_minutos' => ['required', 'integer', 'min:1', 'gte:tiempo_minimo_minutos'],
            'fecha_estimada_entrega' => ['nullable', 'date'],
            'motivo' => ['nullable', 'string', 'max:1000'],
            'nota_cliente' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($orden, $data) {
            EstimacionOrden::create([
                'orden_trabajo_id' => $orden->id,
                'mecanico_id' => $this->mecanicoId(),
                'duracion_minima_minutos' => $data['tiempo_minimo_minutos'],
                'duracion_maxima_minutos' => $data['tiempo_maximo_minutos'],
                'fecha_estimada_entrega' => $data['fecha_estimada_entrega'] ?? null,
                'motivo' => $data['motivo'] ?? null,
                'observacion_cliente' => $data['nota_cliente'] ?? null,
            ]);
        });

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Tiempo estimado guardado.');
    }

    public function avances(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:5000',
            'porcentaje' => 'nullable|integer|min:0|max:100',
            'nota_cliente' => 'nullable|string|max:2000',
            'nota_interna' => 'nullable|string|max:2000',
            'visible_cliente' => 'boolean',
        ]);

        AvanceOrden::create(array_merge($data, [
            'orden_trabajo_id' => $orden->id,
            'mecanico_id' => $this->mecanicoId(),
            'estado' => $orden->estado,
            'visible_cliente' => $request->boolean('visible_cliente'),
        ]));

        $asignacion = $this->asignacionActiva($orden);
        if ($asignacion) {
            $asignacion->update(['porcentaje_avance' => $data['porcentaje'] ?? $asignacion->porcentaje_avance]);
        }

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Avance registrado.');
    }

    public function repuestos(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'repuesto_id' => 'required|exists:repuestos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:1000',
        ]);

        $repuesto = Repuesto::find($data['repuesto_id']);

        OrdenRepuesto::create([
            'orden_trabajo_id' => $orden->id,
            'repuesto_id' => $data['repuesto_id'],
            'mecanico_id' => $this->mecanicoId(),
            'cantidad' => $data['cantidad'],
            'estado' => 'solicitado',
            'motivo' => $data['motivo'] ?? null,
            'precio_unitario_snapshot' => $repuesto->precio_venta ?? 0,
        ]);

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Repuesto agregado a la orden.');
    }

    public function evidencias(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $data = $request->validate([
            'archivo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'tipo' => 'nullable|in:problema,avance,final',
            'descripcion' => 'nullable|string|max:255',
            'visible_cliente' => 'boolean',
        ]);

        $ruta = $request->file('archivo')->store('evidencias', 'public');

        EvidenciaTrabajo::create([
            'asignacion_trabajo_id' => $this->asignacionActiva($orden)?->id,
            'usuario_id' => Auth::id(),
            'archivo' => $ruta,
            'descripcion' => $data['descripcion'] ?? null,
        ]);

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Evidencia subida.');
    }

    public function cambiarEstado(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        $estado = $request->input('estado');
        $permitidos = ['diagnostico', 'en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion'];

        if (! in_array($estado, $permitidos)) {
            return back()->with('error', 'Estado no permitido.');
        }

        $transiciones = [
            'recibida' => ['diagnostico'],
            'diagnostico' => ['en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion'],
            'en_proceso' => ['esperando_repuesto', 'pausada', 'pendiente_autorizacion'],
            'esperando_repuesto' => ['en_proceso', 'pendiente_autorizacion'],
            'pausada' => ['diagnostico', 'en_proceso'],
            'pendiente_autorizacion' => ['en_proceso'],
        ];

        $origen = $orden->estado;

        if (! isset($transiciones[$origen]) || ! in_array($estado, $transiciones[$origen])) {
            return back()->with('error', "No puedes cambiar de '{$origen}' a '{$estado}'.");
        }

        $orden->update(['estado' => $estado]);

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', "Estado cambiado a " . ucfirst(str_replace('_', ' ', $estado)) . ".");
    }

    public function finalizar(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->ordenAsignada($orden);

        if (! in_array($orden->estado, ['en_proceso', 'diagnostico', 'esperando_repuesto', 'pausada'])) {
            return back()->with('error', 'No puedes finalizar esta orden en su estado actual.');
        }

        DB::transaction(function () use ($orden) {
            $orden->update([
                'estado' => 'finalizada_mecanico',
                'fecha_fin' => now(),
            ]);

            $asignacion = $this->asignacionActiva($orden);
            if ($asignacion) {
                $asignacion->update([
                    'fecha_finalizacion' => now(),
                    'porcentaje_avance' => 100,
                ]);
            }
        });

        return redirect()->route('mecanico.ordenes.index')
            ->with('success', 'Trabajo finalizado correctamente. El vehículo está listo para entrega.');
    }

    private function asignacionActiva(OrdenTrabajo $orden): ?AsignacionTrabajo
    {
        return $orden->asignaciones()
            ->where('mecanico_id', $this->mecanicoId())
            ->whereNull('fecha_finalizacion')
            ->first();
    }
}
