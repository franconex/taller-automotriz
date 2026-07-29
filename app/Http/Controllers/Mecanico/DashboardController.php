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
    /**
     * Devuelve el mecanico_id del usuario logueado, o null si no es mecánico.
     */
    private function mecanicoId(): ?int
    {
        return Auth::user()?->empleado?->mecanico?->id;
    }

    /**
     * Devuelve la sucursal_id del usuario logueado.
     */
    private function sucursalId(): ?int
    {
        return Auth::user()?->sucursal_id;
    }

    public function index(): View
    {
        $mecanicoId = $this->mecanicoId();
        $sucursalId = $this->sucursalId();

        /* ============================================================
           1. TRABAJOS ASIGNADOS A ESTE MECÁNICO (por asignaciones_trabajo)
           ============================================================ */
        $misAsignaciones = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', [
                'programada', 'recibida', 'diagnostico', 'en_proceso',
                'esperando_repuesto', 'pausada', 'pendiente_autorizacion',
            ]))
            ->with([
                'ordenTrabajo.cliente',
                'ordenTrabajo.vehiculo',
                'ordenTrabajo.serviciosMecanico',
                'ordenTrabajo.repuestosMecanico',
            ])
            ->orderByDesc('fecha_asignacion')
            ->get();

        /* Contadores por estado */
        $counts = [
            'programada' => 0,
            'recibida' => 0,
            'diagnostico' => 0,
            'en_proceso' => 0,
            'esperando_repuesto' => 0,
            'pausada' => 0,
            'pendiente_autorizacion' => 0,
            'finalizada' => 0,
        ];

        foreach ($misAsignaciones as $a) {
            $estado = $a->ordenTrabajo->estado ?? 'programada';
            if (isset($counts[$estado])) {
                $counts[$estado]++;
            }
        }

        /* Finalizados recientes */
        $terminados = AsignacionTrabajo::where('mecanico_id', $mecanicoId)
            ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', [
                'finalizada_mecanico', 'lista_entrega', 'entregada',
            ]))
            ->whereNotNull('fecha_finalizacion')
            ->latest('fecha_finalizacion')
            ->limit(10)
            ->with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->get();

        /* ============================================================
           2. CITAS CONFIRMADAS SIN ORDEN (alerta: orden no fue creada)
           ============================================================ */
        $citasSinOrden = Cita::when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->where('mecanico_id', $mecanicoId)
            ->where('estado', 'confirmada')
            ->whereDoesntHave('ordenTrabajo')
            ->with(['cliente:id,nombre_completo', 'vehiculo:id,placa,marca,modelo', 'servicio:id,nombre'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        /* ============================================================
           3. ÓRDENES DISPONIBLES (sin mecánico asignado, de mi sucursal)
           ============================================================ */
        $ordenesDisponibles = collect();
        if ($sucursalId) {
            $ordenesIdsConAsignacion = AsignacionTrabajo::whereHas('ordenTrabajo', fn ($q) => $q->where('sucursal_id', $sucursalId))
                ->pluck('orden_trabajo_id')
                ->toArray();

            $ordenesDisponibles = OrdenTrabajo::where('sucursal_id', $sucursalId)
                ->whereNotIn('estado', ['entregada', 'anulada', 'cancelada'])
                ->whereNotIn('id', $ordenesIdsConAsignacion)
                ->with(['cliente:id,nombre_completo', 'vehiculo:id,placa,marca,modelo'])
                ->orderByDesc('fecha_emision')
                ->limit(20)
                ->get();
        }

        return view('mecanico.dashboard', compact(
            'counts',
            'misAsignaciones',
            'terminados',
            'citasSinOrden',
            'ordenesDisponibles'
        ));
    }
}
