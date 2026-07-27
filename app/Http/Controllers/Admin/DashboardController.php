<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auditoria;
use App\Models\Cita;
use App\Models\Inventario;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function index(Request $request): View
    {
        $usuario = Auth::user();
        $sucursalId = $this->usuarioSucursalId();
        $hoy = Carbon::today();
        $hace7dias = Carbon::now()->subDays(7);

        $ordenesBase = OrdenTrabajo::query();
        if ($sucursalId) {
            $ordenesBase->where('sucursal_id', $sucursalId);
        }

        $ordenesActivas = (clone $ordenesBase)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso'])
            ->count();

        $citasHoy = Cita::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', $hoy)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->count();

        $vehiculosListos = (clone $ordenesBase)
            ->where('estado', 'finalizada')
            ->count();

        $pagosPagados = '(SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.orden_trabajo_id = ordenes_trabajo.id AND pagos.estado = \'confirmado\')';

        $pagosPendientes = (clone $ordenesBase)
            ->whereNotIn('estado', ['entregada', 'anulada'])
            ->whereRaw('ordenes_trabajo.total_general > ' . $pagosPagados)
            ->count();

        $stockBajo = Inventario::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereHas('repuesto', function ($q) {
                $q->whereColumn('inventarios.cantidad_actual', '<=', 'repuestos.stock_minimo');
            })
            ->count();

        $ordenesAtrasadas = (clone $ordenesBase)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso'])
            ->where('fecha_emision', '<', $hace7dias)
            ->count();

        $citasCanceladas = Cita::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->where('estado', 'cancelada')
            ->whereDate('updated_at', $hoy)
            ->count();

        $usuariosBloqueados = User::query()
            ->where('estado', 'inactivo')
            ->count();

        $mecanicosBase = Mecanico::query()
            ->whereHas('empleado', fn ($q) => $sucursalId ? $q->where('sucursal_id', $sucursalId) : $q);

        $mecanicosDisponibles = (clone $mecanicosBase)->where('disponibilidad', 'disponible')->count();
        $mecanicosOcupados = (clone $mecanicosBase)->where('disponibilidad', 'ocupado')->count();

        $ordenesRecientes = (clone $ordenesBase)
            ->with(['cliente', 'vehiculo'])
            ->orderByDesc('fecha_emision')
            ->limit(8)
            ->get();

        $citasDelDia = Cita::query()
            ->with(['cliente', 'vehiculo', 'sucursal'])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', $hoy)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('hora')
            ->limit(8)
            ->get();

        $alertasInventario = Inventario::query()
            ->with(['repuesto', 'sucursal'])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereHas('repuesto', function ($q) {
                $q->whereColumn('inventarios.cantidad_actual', '<=', 'repuestos.stock_minimo');
            })
            ->orderBy('cantidad_actual')
            ->limit(8)
            ->get();

        $actividadReciente = Auditoria::query()
            ->with('usuario')
            ->orderByDesc('fecha_accion')
            ->limit(8)
            ->get();

        $ordenesPorEstado = (clone $ordenesBase)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->orderBy('estado')
            ->pluck('total', 'estado');

        $ingresosMensuales = Pago::query()
            ->where('estado', 'confirmado')
            ->when($sucursalId, fn ($q) => $q->whereHas('ordenTrabajo', fn ($oq) => $oq->where('sucursal_id', $sucursalId)))
            ->where('fecha_pago', '>=', now()->subMonths(6)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(fecha_pago, '%Y-%m') as mes"), DB::raw('SUM(monto) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $citasProximas = Cita::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha', [now()->startOfDay(), now()->addDays(6)->endOfDay()])
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->select('fecha', DB::raw('COUNT(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $serviciosTop = \App\Models\Servicio::query()
            ->where('estado', true)
            ->withCount(['citas' => function ($q) {
                $q->when($this->usuarioSucursalId(), fn ($sq) => $sq->where('sucursal_id', $this->usuarioSucursalId()));
            }])
            ->having('citas_count', '>', 0)
            ->orderByDesc('citas_count')
            ->limit(5)
            ->get(['id', 'nombre']);

        $metricas = [
            'ordenes_activas' => $ordenesActivas,
            'citas_hoy' => $citasHoy,
            'vehiculos_listos' => $vehiculosListos,
            'pagos_pendientes' => $pagosPendientes,
        ];

        $accesos = [
            ['route' => 'admin.clientes.create',  'perm' => 'clientes.crear',  'icon' => 'bi-person-vcard',   'titulo' => 'Registrar cliente',  'desc' => 'Agrega un cliente al sistema.'],
            ['route' => 'admin.vehiculos.create', 'perm' => 'vehiculos.crear', 'icon' => 'bi-car-front',      'titulo' => 'Registrar vehículo', 'desc' => 'Asocia un vehículo a un cliente.'],
            ['route' => 'admin.citas.create',     'perm' => 'citas.crear',     'icon' => 'bi-calendar-check', 'titulo' => 'Crear cita',         'desc' => 'Agenda una nueva cita.'],
            ['route' => 'admin.ordenes.create',   'perm' => 'ordenes.crear',   'icon' => 'bi-clipboard-check','titulo' => 'Abrir orden',        'desc' => 'Emite una nueva orden de trabajo.'],
            ['route' => 'admin.pagos.create',     'perm' => 'pagos.registrar', 'icon' => 'bi-cash-coin',      'titulo' => 'Registrar pago',     'desc' => 'Registra un pago a una orden.'],
        ];

        return view('admin.dashboard', [
            'usuario' => $usuario,
            'metricas' => $metricas,
            'alertas' => [
                'stock_bajo' => $stockBajo,
                'ordenes_atrasadas' => $ordenesAtrasadas,
                'citas_canceladas' => $citasCanceladas,
                'usuarios_bloqueados' => $usuariosBloqueados,
                'mecanicos_disponibles' => $mecanicosDisponibles,
                'mecanicos_ocupados' => $mecanicosOcupados,
            ],
            'graficos' => [
                'ordenesPorEstado' => $ordenesPorEstado,
                'ingresosMensuales' => $ingresosMensuales,
                'citasProximas' => $citasProximas,
                'serviciosTop' => $serviciosTop,
            ],
            'ordenesRecientes' => $ordenesRecientes,
            'citasDelDia' => $citasDelDia,
            'alertasInventario' => $alertasInventario,
            'actividadReciente' => $actividadReciente,
            'accesos' => $accesos,
        ]);
    }

}
