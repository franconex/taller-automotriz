@extends('layouts.admin')

@section('title', 'Dashboard')
@section('navbar-title', 'Dashboard')

@section('content')
    {{-- INDICADORES PRINCIPALES --}}
    <section class="admin-stats" aria-label="Indicadores principales">
        <a href="{{ route('admin.ordenes.index') }}" class="admin-stats__item admin-stats__item--link text-decoration-none">
            <span class="admin-stats__label">Órdenes activas</span>
            <span class="admin-stats__value">{{ $metricas['ordenes_activas'] }}</span>
            <span class="admin-stats__hint">recibida · diagnóstico · en proceso</span>
        </a>
        <a href="{{ route('admin.citas.index', ['fecha' => now()->format('Y-m-d')]) }}" class="admin-stats__item admin-stats__item--link text-decoration-none">
            <span class="admin-stats__label">Citas de hoy</span>
            <span class="admin-stats__value">{{ $metricas['citas_hoy'] }}</span>
            <span class="admin-stats__hint">pendientes y confirmadas</span>
        </a>
        <a href="{{ route('admin.ordenes.index', ['estado' => 'finalizada']) }}" class="admin-stats__item admin-stats__item--link text-decoration-none">
            <span class="admin-stats__label">Vehículos listos</span>
            <span class="admin-stats__value">{{ $metricas['vehiculos_listos'] }}</span>
            <span class="admin-stats__hint">para entregar</span>
        </a>
        <a href="{{ route('admin.pagos.index') }}" class="admin-stats__item admin-stats__item--link text-decoration-none">
            <span class="admin-stats__label">Pagos pendientes</span>
            <span class="admin-stats__value">{{ $metricas['pagos_pendientes'] }}</span>
            <span class="admin-stats__hint">órdenes con saldo</span>
        </a>
    </section>

    {{-- ALERTAS --}}
    <section class="mb-4">
        <h2 class="h6 text-uppercase fw-bold text-secondary mb-3" style="letter-spacing:.6px; font-size:.75rem;">
            Alertas
        </h2>
        <div class="row g-3">
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.inventario.index') }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-warning);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill" style="color:var(--tp-warning);" aria-hidden="true"></i>
                        <span class="cell-muted small">Stock bajo</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['stock_bajo'] }}</div>
                </a>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.ordenes.index') }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-danger);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-clock-history" style="color:var(--tp-danger);" aria-hidden="true"></i>
                        <span class="cell-muted small">Atrasadas +7d</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['ordenes_atrasadas'] }}</div>
                </a>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.citas.index', ['estado' => 'cancelada']) }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-info);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-x-circle-fill" style="color:var(--tp-info);" aria-hidden="true"></i>
                        <span class="cell-muted small">Citas canceladas</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['citas_canceladas'] }}</div>
                </a>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.usuarios.index', ['estado' => 'inactivo']) }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-text-secondary);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-slash-circle" style="color:var(--tp-text-secondary);" aria-hidden="true"></i>
                        <span class="cell-muted small">Usuarios bloqueados</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['usuarios_bloqueados'] }}</div>
                </a>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.mecanicos.index', ['disponibilidad' => 'disponible']) }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-success);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-check-circle-fill" style="color:var(--tp-success);" aria-hidden="true"></i>
                        <span class="cell-muted small">Mecánicos libres</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['mecanicos_disponibles'] }}</div>
                </a>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.mecanicos.index', ['disponibilidad' => 'ocupado']) }}" class="d-block p-3 admin-table-wrap hover-lift text-decoration-none" style="border-left:3px solid var(--tp-warning);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-hourglass-split" style="color:var(--tp-warning);" aria-hidden="true"></i>
                        <span class="cell-muted small">Mecánicos ocupados</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.4rem;">{{ $alertas['mecanicos_ocupados'] }}</div>
                </a>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        {{-- ÓRDENES RECIENTES --}}
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Órdenes recientes</h2>
                    <a href="{{ route('admin.ordenes.index') }}" class="cell-muted small">Ver todas</a>
                </div>
                @if ($ordenesRecientes->isEmpty())
                    <x-admin.empty-state icon="bi-clipboard-check" title="Sin órdenes" message="Aún no se han emitido órdenes." />
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenesRecientes as $o)
                                <tr>
                                    <td>
                                        @can('view', $o)
                                        <a href="{{ route('admin.ordenes.show', $o) }}" class="cell-strong">{{ $o->numero_orden }}</a>
                                        @else
                                        <span class="cell-strong">{{ $o->numero_orden }}</span>
                                        @endcan
                                        <div class="cell-muted small">{{ $o->fecha_emision?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="cell-strong">{{ $o->cliente->nombre_completo ?? '—' }}</div>
                                        <div class="cell-muted small">{{ $o->vehiculo->placa ?? '' }}</div>
                                    </td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="match($o->estado) {
                                                'recibida' => 'info',
                                                'diagnostico' => 'warning',
                                                'en_proceso' => 'warning',
                                                'finalizada' => 'success',
                                                'entregada' => 'success',
                                                'anulada' => 'danger',
                                                default => 'neutral',
                                            }"
                                            :label="ucfirst(str_replace('_', ' ', $o->estado))" />
                                    </td>
                                    <td class="text-end cell-strong">{{ number_format((float) $o->total_general, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- CITAS DEL DÍA --}}
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Citas del día</h2>
                    <a href="{{ route('admin.citas.index') }}" class="cell-muted small">Ver agenda</a>
                </div>
                @if ($citasDelDia->isEmpty())
                    <x-admin.empty-state icon="bi-calendar-check" title="Sin citas" message="No hay citas para hoy." />
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($citasDelDia as $c)
                                <tr>
                                    <td class="cell-strong">{{ $c->hora }}</td>
                                    <td>
                                        <div class="cell-strong">{{ $c->cliente->nombre_completo ?? '—' }}</div>
                                        <div class="cell-muted small">{{ $c->vehiculo->placa ?? '' }}</div>
                                    </td>
                                    <td>{{ ucfirst($c->tipo) }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="match($c->estado) {
                                                'confirmada' => 'info',
                                                'atendida' => 'success',
                                                'cancelada' => 'danger',
                                                default => 'warning',
                                            }"
                                            :label="ucfirst($c->estado)" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Órdenes por estado</h2>
                </div>
                <div class="p-3" style="height:220px;"><canvas id="chart-ordenes-estado"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Ingresos últimos 6 meses</h2>
                </div>
                <div class="p-3" style="height:220px;"><canvas id="chart-ingresos"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Citas próximos 7 días</h2>
                </div>
                <div class="p-3" style="height:220px;"><canvas id="chart-citas"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Servicios más solicitados</h2>
                </div>
                <div class="p-3" style="height:220px;"><canvas id="chart-servicios"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <section>
        <h2 class="h6 text-uppercase fw-bold text-secondary mb-3" style="letter-spacing:.6px; font-size:.75rem;">
            Accesos rápidos
        </h2>
        <div class="row g-3">
            @foreach ($accesos as $acceso)
                @if (Route::has($acceso['route']) && Auth::user()->tienePermiso($acceso['perm']))
                    <div class="col-12 col-md-6 col-xl-4">
                        <a href="{{ route($acceso['route']) }}" class="d-flex align-items-start gap-3 p-3 admin-table-wrap hover-lift text-decoration-none" style="height:100%;">
                            <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;border-radius:8px;background:var(--tp-red-soft);color:var(--tp-red);">
                                <i class="bi {{ $acceso['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <span>
                                <span class="d-block fw-semibold" style="color:var(--tp-text);">{{ $acceso['titulo'] }}</span>
                                <span class="d-block" style="color:var(--tp-text-secondary); font-size:.85rem;">{{ $acceso['desc'] }}</span>
                            </span>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
@endsection

@push('styles')
<style>
#chart-ordenes-estado, #chart-ingresos, #chart-citas, #chart-servicios {
    max-width: 100%;
    max-height: 100%;
}
</style>
@endpush

@push('scripts')
<script type="application/json" id="dashboard-chart-data">
{
    "ordenes_por_estado": [
        @foreach ($graficos['ordenesPorEstado'] as $estado => $total)
            {"estado": "{{ $estado }}", "total": {{ $total }}}
            @if (! $loop->last),@endif
        @endforeach
    ],
    "ingresos_mensuales": [
        @foreach ($graficos['ingresosMensuales'] as $item)
            {"mes": "{{ $item->mes }}", "total": {{ $item->total }}}
            @if (! $loop->last),@endif
        @endforeach
    ],
    "citas_proximas": [
        @foreach ($graficos['citasProximas'] as $item)
            {"fecha": "{{ $item->fecha }}", "total": {{ $item->total }}}
            @if (! $loop->last),@endif
        @endforeach
    ],
    "servicios_top": [
        @foreach ($graficos['serviciosTop'] as $item)
            {"nombre": "{{ addslashes($item->nombre) }}", "citas_count": {{ $item->citas_count }}}
            @if (! $loop->last),@endif
        @endforeach
    ]
}
</script>
@vite(['resources/js/admin/dashboard-charts.js'])
@endpush
