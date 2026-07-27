@extends('layouts.admin')

@section('title', 'Panel de Recepcionista')
@section('navbar-title', 'Panel de Recepcionista')

@section('breadcrumb')
    <li class="active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-person-badge fs-5" aria-hidden="true"></i>
        <span>Bienvenido, <strong>{{ $usuario->nombre }}</strong>. Has iniciado sesión como <strong>Recepcionista</strong>.</span>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="admin-table-wrap p-3 text-center">
                <div class="fs-1 fw-bold text-primary">{{ $metricas['ordenes_activas'] ?? '—' }}</div>
                <div class="text-muted small">Órdenes activas</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="admin-table-wrap p-3 text-center">
                <div class="fs-1 fw-bold text-success">{{ $metricas['citas_hoy'] ?? '—' }}</div>
                <div class="text-muted small">Citas hoy</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="admin-table-wrap p-3 text-center">
                <div class="fs-1 fw-bold text-warning">{{ $metricas['vehiculos_listos'] ?? '—' }}</div>
                <div class="text-muted small">Vehículos listos</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="admin-table-wrap p-3 text-center">
                <div class="fs-1 fw-bold text-danger">{{ $metricas['pagos_pendientes'] ?? '—' }}</div>
                <div class="text-muted small">Pagos pendientes</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-3">
                <h3 class="h6 fw-bold mb-3"><i class="bi bi-calendar-check" aria-hidden="true"></i> Citas del día</h3>
                @if (!empty($citasDelDia) && count($citasDelDia))
                    <table class="admin-table admin-table--compact">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($citasDelDia as $c)
                                <tr>
                                    <td>{{ $c->hora ? \Carbon\Carbon::parse($c->hora)->format('H:i') : '—' }}</td>
                                    <td>{{ $c->cliente->nombre_completo ?? '—' }}</td>
                                    <td>{{ $c->servicio->nombre ?? '—' }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :label="ucfirst($c->estado)"
                                            :tone="match($c->estado) {
                                                'pendiente' => 'info',
                                                'confirmada' => 'success',
                                                'en_curso' => 'primary',
                                                'cancelada' => 'danger',
                                                'no_asistio' => 'warning',
                                                default => 'neutral',
                                            }" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted small mb-0">No hay citas programadas para hoy.</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-3">
                <h3 class="h6 fw-bold mb-3"><i class="bi bi-lightning" aria-hidden="true"></i> Acceso rápido</h3>
                <div class="d-flex flex-wrap gap-2">
                    @if (Route::has('admin.clientes.create') && Auth::user()->tienePermiso('clientes.crear'))
                        <a href="{{ route('admin.clientes.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-plus" aria-hidden="true"></i> Nuevo cliente
                        </a>
                    @endif
                    @if (Route::has('admin.vehiculos.create') && Auth::user()->tienePermiso('vehiculos.crear'))
                        <a href="{{ route('admin.vehiculos.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-car-front" aria-hidden="true"></i> Nuevo vehículo
                        </a>
                    @endif
                    @if (Route::has('admin.ordenes.create') && Auth::user()->tienePermiso('ordenes.crear'))
                        <a href="{{ route('admin.ordenes.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-clipboard-plus" aria-hidden="true"></i> Nueva orden
                        </a>
                    @endif
                    @if (Route::has('admin.pagos.create') && Auth::user()->tienePermiso('pagos.registrar'))
                        <a href="{{ route('admin.pagos.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-cash" aria-hidden="true"></i> Nuevo pago
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
