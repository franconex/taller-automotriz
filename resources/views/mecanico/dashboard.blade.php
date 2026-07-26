@extends('layouts.admin')

@section('title', 'Panel de Mecánico')
@section('navbar-title', 'Panel de Mecánico')

@section('breadcrumb')
    <li class="active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-person-badge fs-5" aria-hidden="true"></i>
        <span>Bienvenido, <strong>{{ $usuario->nombre }}</strong>. Has iniciado sesión como <strong>Mecánico</strong>.</span>
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
                <div class="fs-1 fw-bold text-success">{{ $metricas['vehiculos_listos'] ?? '—' }}</div>
                <div class="text-muted small">Vehículos listos</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="admin-table-wrap p-3">
                <h3 class="h6 fw-bold mb-3"><i class="bi bi-clipboard-check" aria-hidden="true"></i> Órdenes de trabajo recientes</h3>
                @if (!empty($ordenesRecientes) && count($ordenesRecientes))
                    <table class="admin-table admin-table--compact">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenesRecientes as $o)
                                <tr>
                                    <td><a href="{{ route('admin.ordenes.show', $o) }}">{{ $o->numero_orden }}</a></td>
                                    <td>{{ $o->cliente->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo->placa ?? '—' }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :label="ucfirst($o->estado)"
                                            :tone="match($o->estado) {
                                                'recibida' => 'info',
                                                'diagnostico' => 'warning',
                                                'en_proceso' => 'primary',
                                                'finalizada' => 'success',
                                                'entregada' => 'success',
                                                'anulada' => 'danger',
                                                default => 'neutral',
                                            }" />
                                    </td>
                                    <td class="text-end">{{ number_format((float) $o->total_general, 2) }} Bs</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted small mb-0">No hay órdenes de trabajo.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
