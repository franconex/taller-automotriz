<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTrabajo;
use App\Models\Autorizacion;
use App\Models\Cita;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function mecanico(): ?Mecanico
    {
        return Auth::user()?->empleado?->mecanico;
    }

    private function mecanicoId(): ?int
    {
        return $this->mecanico()?->id;
    }

    public function index(): View|RedirectResponse
    {
        $mecanicoId = $this->mecanicoId();
        $mecanico = $this->mecanico();

        if (! $mecanico) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tu cuenta de mecánico no está configurada correctamente. Contacta al administrador.');
        }

        $asignaciones = collect();
        $activas = collect();
        $finalizadasHoy = collect();
        $counts = [
            'en_proceso' => 0, 'diagnostico' => 0, 'esperando_repuesto' => 0,
            'pausada' => 0, 'pendiente_autorizacion' => 0,
            'finalizadas_hoy' => 0, 'total_activas' => 0, 'cotizaciones_pendientes' => 0,
        ];

        if ($mecanicoId) {
            $asignaciones = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
                ->with(['ordenTrabajo.cliente', 'ordenTrabajo.vehiculo'])
                ->orderByDesc('fecha_asignacion')
                ->get();

            $activas = $asignaciones->filter(fn($a) => in_array($a->ordenTrabajo->estado ?? '', [
                'recibida', 'diagnostico', 'en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion'
            ]));

            $finalizadasHoy = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
                ->whereHas('ordenTrabajo', fn($q) => $q->where('estado', 'finalizada_mecanico'))
                ->whereDate('fecha_finalizacion', now()->toDateString())
                ->count();

            $counts = [
                'en_proceso' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'en_proceso')->count(),
                'diagnostico' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'diagnostico')->count(),
                'esperando_repuesto' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'esperando_repuesto')->count(),
                'pausada' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'pausada')->count(),
                'pendiente_autorizacion' => $activas->filter(fn($a) => $a->ordenTrabajo->estado === 'pendiente_autorizacion')->count(),
                'finalizadas_hoy' => $finalizadasHoy,
                'total_activas' => $activas->count(),
                'cotizaciones_pendientes' => Autorizacion::where('estado', 'pendiente')
                    ->where(function ($q) use ($mecanicoId) {
                        $q->whereHas('cita', fn($sq) => $sq->where('mecanico_id', $mecanicoId))
                          ->orWhereHas('ordenTrabajo.asignaciones', fn($sq) => $sq->where('mecanico_id', $mecanicoId));
                    })->count(),
            ];
        }

        $ordenesDisponibles = collect();
        $sucursalId = Auth::user()?->empleado?->sucursal_id;
        if ($sucursalId && $mecanicoId) {
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

        $citasPendientes = $mecanicoId
            ? Cita::where('mecanico_id', $mecanicoId)
                ->whereIn('estado', ['confirmada', 'atendida'])
                ->whereDate('fecha', '>=', now()->subDay())
                ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa,marca,modelo', 'servicio:id,nombre'])
                ->withCount('autorizaciones')
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get()
            : collect();

        return view('mecanico.dashboard', compact(
            'counts', 'activas', 'finalizadasHoy', 'ordenesDisponibles', 'citasPendientes', 'mecanico', 'mecanicoId'
        ));
    }

    public function citasIndex(): View
    {
        $mecanicoId = $this->mecanicoId();

        $citas = $mecanicoId
            ? Cita::where('mecanico_id', $mecanicoId)
                ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa,marca,modelo', 'servicio:id,nombre', 'sucursal:id,nombre'])
                ->withCount('autorizaciones')
                ->orderByRaw("FIELD(estado, 'confirmada', 'atendida', 'cancelada')")
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->paginate(20)
            : collect();

        return view('mecanico.citas.index', compact('citas'));
    }

    public function toggleDisponibilidad(): RedirectResponse
    {
        $mecanico = $this->mecanico();
        if (! $mecanico) {
            return back()->with('error', 'No se encontró tu perfil de mecánico.');
        }

        $mecanico->disponibilidad = $mecanico->disponibilidad === 'disponible' ? 'ocupado' : 'disponible';
        $mecanico->save();

        $texto = $mecanico->disponibilidad === 'disponible' ? 'disponible' : 'ocupado';

        return back()->with('success', "Ahora estás marcado como {$texto}.");
    }
}
