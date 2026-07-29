<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTrabajo;
use App\Models\Cita;
use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function mecanicoId(): ?int
    {
        return Auth::user()?->empleado?->mecanico?->id;
    }

    public function index(): View
    {
        $mecanicoId = $this->mecanicoId();

        $asignaciones = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->with(['ordenTrabajo.cliente', 'ordenTrabajo.vehiculo'])
            ->orderByDesc('fecha_asignacion')
            ->get();

        $activas = $asignaciones->filter(fn($a) => in_array($a->ordenTrabajo->estado, [
            'recibida', 'diagnostico', 'en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion'
        ]));

        $finalizadasHoy = $asignaciones->filter(fn($a) =>
            $a->ordenTrabajo->estado === 'finalizada_mecanico' &&
            $a->fecha_finalizacion?->isToday()
        );

        $counts = [
            'en_proceso' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'en_proceso')->count(),
            'diagnostico' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'diagnostico')->count(),
            'esperando_repuesto' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'esperando_repuesto')->count(),
            'pausada' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'pausada')->count(),
            'pendiente_autorizacion' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'pendiente_autorizacion')->count(),
            'finalizadas_hoy' => $finalizadasHoy->count(),
            'total_activas' => $activas->count(),
        ];

        $ordenesDisponibles = collect();
        $sucursalId = Auth::user()?->sucursal_id;
        if ($sucursalId) {
            $idsConAsignacion = AsignacionTrabajo::whereHas('ordenTrabajo', fn($q) => $q->where('sucursal_id', $sucursalId))
                ->pluck('orden_trabajo_id')->toArray();

            $ordenesDisponibles = OrdenTrabajo::where('sucursal_id', $sucursalId)
                ->whereNotIn('estado', ['entregada', 'anulada', 'cancelada'])
                ->whereNotIn('id', $idsConAsignacion)
                ->with(['cliente:id,nombre_completo', 'vehiculo:id,placa,marca,modelo'])
                ->orderByDesc('fecha_emision')
                ->limit(15)
                ->get();
        }

        $citasPendientes = Cita::where('mecanico_id', $mecanicoId)
            ->whereIn('estado', ['confirmada', 'atendida'])
            ->whereDate('fecha', '>=', now()->subDay())
            ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa,marca,modelo', 'servicio:id,nombre'])
            ->withCount('autorizaciones')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        return view('mecanico.dashboard', compact(
            'counts', 'activas', 'finalizadasHoy', 'ordenesDisponibles', 'citasPendientes'
        ));
    }

    public function citasIndex(): View
    {
        $mecanicoId = $this->mecanicoId();

        $citas = Cita::where('mecanico_id', $mecanicoId)
            ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa,marca,modelo', 'servicio:id,nombre', 'sucursal:id,nombre'])
            ->withCount('autorizaciones')
            ->orderByRaw("FIELD(estado, 'confirmada', 'atendida', 'cancelada')")
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(20);

        return view('mecanico.citas.index', compact('citas'));
    }
}
