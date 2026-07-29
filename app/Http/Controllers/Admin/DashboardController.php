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

        $rol = $usuario->rol->nombre;

        if ($rol === 'Mecánico') {
            return redirect()->route('mecanico.dashboard');
        }

        if ($rol === 'Recepcionista') {
            return $this->recepcionistaDashboard($sucursalId, $hoy, $usuario);
        }

        if ($rol === 'Gerente') {
            return $this->gerenteDashboard($sucursalId, $hoy, $usuario);
        }

        return $this->adminDashboard($sucursalId, $hoy, $usuario, $request);
    }

    private function adminDashboard($sucursalId, $hoy, $usuario, $request): View
    {
        $hace7dias = Carbon::now()->subDays(7);

        $ordenesBase = OrdenTrabajo::query();
        if ($sucursalId) $ordenesBase->where('sucursal_id', $sucursalId);

        $ordenesActivas = (clone $ordenesBase)->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso'])->count();
        $citasHoy = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereDate('fecha',$hoy)->whereIn('estado',['pendiente','confirmada'])->count();
        $vehiculosListos = (clone $ordenesBase)->where('estado', 'finalizada')->count();

        $pagosPendientes = (clone $ordenesBase)
            ->whereNotIn('estado', ['entregada', 'anulada'])
            ->where(function ($query) {
                $query->whereRaw(
                    'ordenes_trabajo.total_general > (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.orden_trabajo_id = ordenes_trabajo.id AND pagos.estado = ?)',
                    ['confirmado']
                );
            })
            ->count();

        $stockBajo = Inventario::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->whereHas('repuesto', fn($q)=>$q->whereColumn('inventarios.cantidad_actual','<=','repuestos.stock_minimo'))
            ->count();

        $ordenesAtrasadas = (clone $ordenesBase)
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso'])
            ->where('fecha_emision', '<', $hace7dias)
            ->count();

        $citasCanceladas = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->where('estado', 'cancelada')
            ->whereDate('updated_at', $hoy)
            ->count();

        $usuariosBloqueados = User::where('estado', 'inactivo')->count();

        $mecanicosBase = Mecanico::whereHas('empleado', fn($q)=>$sucursalId ? $q->where('sucursal_id',$sucursalId) : $q);
        $mecanicosDisponibles = (clone $mecanicosBase)->where('disponibilidad', 'disponible')->count();
        $mecanicosOcupados = (clone $mecanicosBase)->where('disponibilidad', 'ocupado')->count();

        $ordenesRecientes = (clone $ordenesBase)->with(['cliente','vehiculo'])->orderByDesc('fecha_emision')->limit(8)->get();

        $citasDelDia = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->with(['cliente','vehiculo','sucursal'])
            ->whereDate('fecha',$hoy)
            ->whereIn('estado',['pendiente','confirmada'])
            ->orderBy('hora')->limit(8)->get();

        $alertasInventario = Inventario::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->with(['repuesto','sucursal'])
            ->whereHas('repuesto', fn($q)=>$q->whereColumn('inventarios.cantidad_actual','<=','repuestos.stock_minimo'))
            ->orderBy('cantidad_actual')->limit(8)->get();

        $actividadReciente = Auditoria::with('usuario')->orderByDesc('fecha_accion')->limit(8)->get();

        $ordenesPorEstado = (clone $ordenesBase)->select('estado',DB::raw('COUNT(*) as total'))->groupBy('estado')->orderBy('estado')->pluck('total','estado');

        $ingresosMensuales = Pago::where('estado','confirmado')
            ->when($sucursalId, fn($q)=>$q->whereHas('ordenTrabajo',fn($oq)=>$oq->where('sucursal_id',$sucursalId)))
            ->where('fecha_pago','>=',now()->subMonths(6)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(fecha_pago,'%Y-%m') as mes"),DB::raw('SUM(monto) as total'))
            ->groupBy('mes')->orderBy('mes')->get();

        $citasProximas = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->whereBetween('fecha',[now()->startOfDay(),now()->addDays(6)->endOfDay()])
            ->whereNotIn('estado',['cancelada','no_asistio'])
            ->select('fecha',DB::raw('COUNT(*) as total'))
            ->groupBy('fecha')->orderBy('fecha')->get();

        $serviciosTop = \App\Models\Servicio::where('estado',true)
            ->withCount(['citas'=>fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId))])
            ->having('citas_count','>',0)->orderByDesc('citas_count')->limit(5)->get(['id','nombre']);

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

    private function gerenteDashboard($sucursalId, $hoy, $usuario): View
    {
        $enTaller = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereIn('estado',['recibida','diagnostico','en_proceso'])->count();
        $atrasadas = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereIn('estado',['recibida','diagnostico','en_proceso'])->where('fecha_emision','<',now()->subDays(7))->count();
        $citasPendientes = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereIn('estado',['solicitada','pendiente'])->whereDate('fecha','>=',$hoy)->count();
        $autorizacionesPendientes = \App\Models\Autorizacion::whereHas('ordenTrabajo',fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId)))->where('estado','pendiente')->count();
        $ingresosDia = Pago::where('estado','confirmado')->when($sucursalId, fn($q)=>$q->whereHas('ordenTrabajo',fn($oq)=>$oq->where('sucursal_id',$sucursalId)))->whereDate('fecha_pago',$hoy)->sum('monto');
        $ingresosMes = Pago::where('estado','confirmado')->when($sucursalId, fn($q)=>$q->whereHas('ordenTrabajo',fn($oq)=>$oq->where('sucursal_id',$sucursalId)))->whereMonth('fecha_pago',$hoy->month)->whereYear('fecha_pago',$hoy->year)->sum('monto');
        $stockBajo = Inventario::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereHas('repuesto',fn($q)=>$q->whereColumn('inventarios.cantidad_actual','<=','repuestos.stock_minimo'))->count();
        $comprasPendientes = \App\Models\SolicitudCompra::whereIn('estado',['pendiente','aprobada'])->count();
        $mecanicosDisponibles = Mecanico::whereHas('empleado',fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId)))->where('disponibilidad','disponible')->count();
        $mecanicosOcupados = Mecanico::whereHas('empleado',fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId)))->where('disponibilidad','ocupado')->count();

        $ingresosMensuales = Pago::where('estado','confirmado')->when($sucursalId, fn($q)=>$q->whereHas('ordenTrabajo',fn($oq)=>$oq->where('sucursal_id',$sucursalId)))->where('fecha_pago','>=',now()->subMonths(6)->startOfMonth())->select(DB::raw("DATE_FORMAT(fecha_pago,'%Y-%m') as mes"),DB::raw('SUM(monto) as total'))->groupBy('mes')->get();
        $serviciosTop = \App\Models\Servicio::where('estado',true)->withCount(['citas'=>fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId))])->having('citas_count','>',0)->orderByDesc('citas_count')->limit(5)->get();
        $ordenesPorEstado = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->select('estado',DB::raw('COUNT(*) as total'))->groupBy('estado')->pluck('total','estado');

        return view('admin.dashboard-gerente', compact(
            'usuario', 'enTaller', 'atrasadas', 'citasPendientes', 'autorizacionesPendientes',
            'ingresosDia', 'ingresosMes', 'stockBajo', 'comprasPendientes',
            'mecanicosDisponibles', 'mecanicosOcupados',
            'ingresosMensuales', 'serviciosTop', 'ordenesPorEstado'
        ));
    }

    private function recepcionistaDashboard($sucursalId, $hoy, $usuario): View
    {
        $citasSolicitadas = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereIn('estado',['solicitada','propuesta','pendiente'])->whereDate('fecha','>=',$hoy)->count();
        $citasConfirmadasHoy = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereDate('fecha',$hoy)->whereIn('estado',['confirmada','atendida'])->count();
        $ordenesEsperando = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->where('estado','recibida')->count();
        $vehiculosListos = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->where('estado','finalizada')->count();
        $autorizacionesPendientes = \App\Models\Autorizacion::whereHas('ordenTrabajo',fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId)))->where('estado','pendiente')->count();
        $pagosPendientes = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereNotIn('estado',['entregada','anulada'])->get()->filter(fn($o)=>(float)$o->total_general > $o->pagos()->where('estado','confirmado')->sum('monto'))->count();
        $entregasHoy = OrdenTrabajo::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))->whereIn('estado',['finalizada','entregada'])->whereDate('fecha_fin',$hoy)->count();

        $solicitudes = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->whereIn('estado',['solicitada','propuesta','pendiente'])
            ->whereDate('fecha','>=',$hoy)
            ->with(['cliente:id,nombre_completo','vehiculo:id,placa','servicio:id,nombre','sucursal:id,nombre'])
            ->orderBy('fecha')->orderBy('hora')
            ->get();

        $agenda = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->whereDate('fecha',$hoy)
            ->whereIn('estado',['confirmada','atendida'])
            ->with(['cliente:id,nombre_completo','vehiculo:id,placa','servicio:id,nombre','mecanico.empleado:id,nombre_completo'])
            ->orderBy('hora')->get();

        $proximasConfirmadas = Cita::when($sucursalId, fn($q)=>$q->where('sucursal_id',$sucursalId))
            ->whereDate('fecha','>=',$hoy)
            ->where('estado','confirmada')
            ->with(['cliente:id,nombre_completo','vehiculo:id,placa','servicio:id,nombre','mecanico.empleado:id,nombre_completo'])
            ->orderBy('fecha')->orderBy('hora')
            ->limit(10)
            ->get();

        $cotizacionesPendientes = \App\Models\Autorizacion::where('estado', 'pendiente')
            ->whereNotNull('cita_id')
            ->whereNull('orden_trabajo_id')
            ->whereHas('cita', fn($q) => $q->when($sucursalId, fn($sq) => $sq->where('sucursal_id', $sucursalId)))
            ->with(['cita.cliente:id,nombre_completo', 'cita.vehiculo:id,placa', 'cita.mecanico.empleado:id,nombre_completo'])
            ->orderByDesc('fecha_solicitud')
            ->limit(10)
            ->get();

        $mecanicos = Mecanico::with('empleado')
            ->whereHas('empleado',fn($q)=>$q->when($sucursalId,fn($sq)=>$sq->where('sucursal_id',$sucursalId)))
            ->withCount(['asignaciones as trabajos_activos'=>fn($q)=>$q->whereNull('fecha_finalizacion')])
            ->get()->map(fn($m)=>[
                'nombre'=>$m->empleado?->nombre_completo ?? '—',
                'especialidad'=>$m->especialidad?->nombre ?? '—',
                'activos'=>$m->trabajos_activos,
                'disponible'=>$m->disponibilidad === 'disponible',
            ]);

        return view('admin.dashboard-recepcionista', compact(
            'usuario', 'citasSolicitadas', 'citasConfirmadasHoy', 'ordenesEsperando',
            'vehiculosListos', 'autorizacionesPendientes', 'pagosPendientes', 'entregasHoy',
            'solicitudes', 'agenda', 'proximasConfirmadas', 'cotizacionesPendientes', 'mecanicos'
        ));
    }
}
