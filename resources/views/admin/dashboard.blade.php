@extends('layouts.admin')

@section('title', 'Dashboard')
@section('navbar-title', 'Dashboard')

@section('breadcrumb')
    <li class="active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    @php
        $saludo = match (true) {
            now()->hour < 12 => 'Buenos días',
            now()->hour < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };
    @endphp

    <x-admin.page-header
        :title="$saludo . ', ' . ($usuario->nombre ?? 'Administrador')"
        :description="now()->translatedFormat('l d \\de F, Y') . ' — ' . ($usuario->sucursal->nombre ?? 'Sin sucursal') . ' · ' . ($usuario->rol->nombre ?? 'Sin rol')">
        <x-slot:actions>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm" aria-label="Actualizar">
                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                <span class="d-none d-sm-inline">Actualizar</span>
            </a>
        </x-slot:actions>
    </x-admin.page-header>

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
                                        <a href="{{ route('admin.ordenes.show', $o) }}" class="cell-strong">{{ $o->numero_orden }}</a>
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
        {{-- ALERTAS DE INVENTARIO --}}
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Alertas de inventario</h2>
                    <a href="{{ route('admin.inventario.index') }}" class="cell-muted small">Ver inventario</a>
                </div>
                @if ($alertasInventario->isEmpty())
                    <x-admin.empty-state icon="bi-box-seam" title="Sin alertas" message="No hay repuestos con stock bajo." />
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th class="d-none d-md-table-cell">Sucursal</th>
                                <th class="text-end">Stock</th>
                                <th class="text-end d-none d-md-table-cell">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alertasInventario as $it)
                                <tr>
                                    <td>
                                        <div class="cell-strong">{{ $it->repuesto->nombre ?? '—' }}</div>
                                        <div class="cell-muted small">{{ $it->repuesto->codigo ?? '' }}</div>
                                    </td>
                                    <td class="d-none d-md-table-cell cell-muted">{{ $it->sucursal->nombre ?? '—' }}</td>
                                    <td class="text-end">
                                        <x-admin.status-badge
                                            :tone="$it->cantidad_actual <= 0 ? 'danger' : 'warning'"
                                            :icon="$it->cantidad_actual <= 0 ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill'"
                                            :label="$it->cantidad_actual" />
                                    </td>
                                    <td class="text-end cell-muted d-none d-md-table-cell">{{ $it->repuesto->stock_minimo ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- ACTIVIDAD RECIENTE --}}
        <div class="col-12 col-xl-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Actividad reciente</h2>
                    <a href="{{ route('admin.auditoria.index') }}" class="cell-muted small">Ver auditoría</a>
                </div>
                @if ($actividadReciente->isEmpty())
                    <x-admin.empty-state icon="bi-journal-text" title="Sin actividad" message="Aún no se registran acciones en el sistema." />
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th class="d-none d-md-table-cell">Acción</th>
                                <th class="d-none d-md-table-cell">Módulo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($actividadReciente as $a)
                                <tr>
                                    <td class="cell-muted small">{{ $a->fecha_accion?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="cell-strong">{{ $a->usuario->nombre ?? '—' }}</td>
                                    <td class="d-none d-md-table-cell">{{ ucfirst($a->accion) }}</td>
                                    <td class="d-none d-md-table-cell cell-muted">{{ ucfirst($a->modulo) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
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
