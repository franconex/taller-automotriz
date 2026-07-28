<?php

namespace App\Http\Controllers\Admin;

use App\Models\AsignacionTrabajo;
use App\Models\EstimacionOrden;
use App\Models\EvidenciaTrabajo;
use App\Models\NotaTrabajo;
use App\Models\OrdenTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MecanicoPanelController extends AdminController
{
    private function mecanicoId(): ?int
    {
        return Auth::user()->empleado?->mecanico?->id;
    }

    public function index(): View
    {
        $mecanicoId = $this->mecanicoId();

        $trabajoActual = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', ['diagnostico', 'en_proceso']))
            ->whereNull('fecha_finalizacion')
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->first();

        $programadas = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'programada'))
            ->with(['ordenTrabajo.cliente', 'ordenTrabajo.vehiculo', 'ordenTrabajo.cita'])
            ->get()
            ->sortBy(fn ($a) => $a->ordenTrabajo?->cita?->fecha?->format('Y-m-d') . ' ' . ($a->ordenTrabajo?->cita?->hora ?? '00:00'));

        $pendientes = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'recibida'))
            ->whereNull('fecha_inicio')
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        $enDiagnostico = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'diagnostico'))
            ->whereNull('fecha_finalizacion')
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        $enProceso = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'en_proceso'))
            ->whereNull('fecha_finalizacion')
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        $pausadas = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'pausada'))
            ->whereNull('fecha_finalizacion')
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        $finalizadasHoy = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereDate('fecha_finalizacion', today())
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        $historial = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereNotNull('fecha_finalizacion')
            ->latest('fecha_finalizacion')
            ->limit(20)
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        return view('admin.mi-panel.index', compact(
            'trabajoActual', 'programadas', 'pendientes', 'enDiagnostico', 'enProceso',
            'pausadas', 'finalizadasHoy', 'historial'
        ));
    }

    public function show(OrdenTrabajo $ordene): View
    {
        Gate::authorize('view', $ordene);

        $ordene->load([
            'cliente',
            'vehiculo',
            'sucursal',
            'asignaciones' => fn ($q) => $q->where('mecanico_id', $this->mecanicoId()),
            'asignaciones.notas.usuario',
            'asignaciones.evidencias',
            'detalles.servicio',
            'detalles.repuesto',
        ]);

        $asignacion = $ordene->asignaciones->first();

        return view('admin.mi-panel.show', compact('ordene', 'asignacion'));
    }

    public function iniciar(OrdenTrabajo $ordene): RedirectResponse
    {
        if ($ordene->estado === 'programada') {
            return redirect()->route('admin.mi-panel.show', $ordene)
                ->with('error', 'El vehículo aún no ha llegado al taller. Espera a que recepción registre la llegada.');
        }

        Gate::authorize('work', $ordene);

        $this->transicionEstado($ordene, 'diagnostico');

        $asignacion = $this->asignacionActiva($ordene);
        if ($asignacion && ! $asignacion->fecha_inicio) {
            $asignacion->update(['fecha_inicio' => now()]);
        }

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Trabajo iniciado. Registra el diagnóstico.');
    }

    public function registrarDiagnostico(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate([
            'diagnostico_mecanico' => 'required|string|max:5000',
        ]);

        $asignacion = $this->asignacionActiva($ordene);
        $asignacion?->update(['diagnostico_mecanico' => $data['diagnostico_mecanico']]);

        $ordene->update(['diagnostico_general' => $data['diagnostico_mecanico']]);

        $this->transicionEstado($ordene, 'en_proceso');

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Diagnóstico registrado. El trabajo está en proceso.');
    }

    public function registrarAvance(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate([
            'porcentaje_avance' => 'required|integer|min:0|max:100',
            'proximo_paso' => 'nullable|string|max:2000',
        ]);

        $asignacion = $this->asignacionActiva($ordene);
        $asignacion?->update([
            'porcentaje_avance' => $data['porcentaje_avance'],
            'proximo_paso' => $data['proximo_paso'] ?? $asignacion->proximo_paso,
        ]);

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Avance registrado.');
    }

    public function pausar(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $this->transicionEstado($ordene, 'pausada');

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Trabajo pausado.');
    }

    public function reanudar(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $ultimoEstado = $ordene->asignaciones()
            ->where('mecanico_id', $this->mecanicoId())
            ->value('diagnostico_mecanico');

        $estadoDestino = $ultimoEstado ? 'en_proceso' : 'diagnostico';
        $this->transicionEstado($ordene, $estadoDestino);

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Trabajo reanudado.');
    }

    public function finalizar(OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $this->transicionEstado($ordene, 'finalizada');

        $asignacion = $this->asignacionActiva($ordene);
        $asignacion?->update([
            'fecha_finalizacion' => now(),
            'porcentaje_avance' => 100,
        ]);

        return redirect()->route('admin.mi-panel.index')
            ->with('success', 'Trabajo finalizado correctamente.');
    }

    public function guardarNota(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('createNote', $ordene);

        $data = $request->validate([
            'contenido' => 'required|string|max:5000',
            'visible_cliente' => 'boolean',
        ]);

        $asignacion = $this->asignacionActiva($ordene);
        if (! $asignacion) {
            return back()->with('error', 'No tienes una asignación activa en esta orden.');
        }

        NotaTrabajo::create([
            'asignacion_trabajo_id' => $asignacion->id,
            'usuario_id' => Auth::id(),
            'contenido' => $data['contenido'],
            'visible_cliente' => $request->boolean('visible_cliente'),
        ]);

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Nota guardada.');
    }

    public function subirEvidencia(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('uploadEvidence', $ordene);

        $data = $request->validate([
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $asignacion = $this->asignacionActiva($ordene);
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

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Evidencia subida.');
    }

    public function estimarTiempo(Request $request, OrdenTrabajo $ordene): RedirectResponse
    {
        Gate::authorize('work', $ordene);

        $data = $request->validate([
            'duracion_minima_minutos' => ['required', 'integer', 'min:1', 'max:14400'],
            'duracion_maxima_minutos' => ['required', 'integer', 'min:1', 'max:14400', 'gte:duracion_minima_minutos'],
            'fecha_estimada_entrega' => ['nullable', 'date'],
            'observacion_cliente' => ['nullable', 'string', 'max:1000'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ]);

        $asignacion = $this->asignacionActiva($ordene);
        if (! $asignacion) {
            return back()->with('error', 'No tienes una asignación activa en esta orden.');
        }

        $mecanicoId = $this->mecanicoId();

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

        return redirect()->route('admin.mi-panel.show', $ordene)
            ->with('success', 'Tiempo estimado guardado correctamente.');
    }

    public function verEstimacion(OrdenTrabajo $ordene)
    {
        Gate::authorize('view', $ordene);

        $estimacion = $ordene->estimaciones()->latest()->first();

        if (! $estimacion) {
            return response()->json(['estimacion' => null]);
        }

        return response()->json([
            'estimacion' => [
                'id' => $estimacion->id,
                'minutos_min' => $estimacion->duracion_minima_minutos,
                'minutos_max' => $estimacion->duracion_maxima_minutos,
                'horas_min' => round($estimacion->duracion_minima_minutos / 60, 1),
                'horas_max' => round($estimacion->duracion_maxima_minutos / 60, 1),
                'fecha_entrega' => $estimacion->fecha_estimada_entrega?->format('d/m/Y H:i'),
                'observacion' => $estimacion->observacion_cliente,
            ],
        ]);
    }

    private function asignacionActiva(OrdenTrabajo $ordene): ?AsignacionTrabajo
    {
        return $ordene->asignaciones()
            ->where('mecanico_id', $this->mecanicoId())
            ->whereNull('fecha_finalizacion')
            ->first();
    }

    private function transicionEstado(OrdenTrabajo $ordene, string $destino): void
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
