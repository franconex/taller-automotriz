<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTrabajo;
use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function mecanicoId(): ?int
    {
        return Auth::user()->empleado?->mecanico?->id;
    }

    public function index(): View
    {
        $mecanicoId = $this->mecanicoId();

        $programadas = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'programada'))
            ->count();

        $recibidas = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'recibida'))
            ->count();

        $enDiagnostico = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'en_diagnostico'))
            ->count();

        $enProceso = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'en_proceso'))
            ->count();

        $esperandoRepuesto = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'esperando_repuesto'))
            ->count();

        $pendienteAutorizacion = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->where('estado', 'pendiente_autorizacion'))
            ->count();

        $finalizados = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', ['finalizada_mecanico', 'lista_entrega']))
            ->count();

        $trabajosActuales = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', ['recibida', 'en_diagnostico', 'en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion']))
            ->with([
                'ordenTrabajo.cliente',
                'ordenTrabajo.vehiculo',
                'ordenTrabajo.estimaciones' => fn ($q) => $q->latest()->limit(1),
            ])
            ->get();

        $terminados = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', ['finalizada_mecanico', 'lista_entrega', 'entregada']))
            ->whereNotNull('fecha_finalizacion')
            ->latest('fecha_finalizacion')
            ->limit(10)
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        return view('mecanico.dashboard', compact(
            'programadas', 'recibidas', 'enDiagnostico', 'enProceso',
            'esperandoRepuesto', 'pendienteAutorizacion', 'finalizados',
            'trabajosActuales', 'terminados'
        ));
    }
}
