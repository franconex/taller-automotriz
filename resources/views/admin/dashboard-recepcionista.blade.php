@extends('layouts.admin')

@section('title', 'Inicio — Recepción')
@section('navbar-title', 'Inicio')

@section('content')
    {{-- ACCIONES RÁPIDAS --}}
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.citas.index') }}" class="btn w-100 text-white d-flex flex-column align-items-center py-3" style="background:#E31E24;">
                <i class="bi bi-calendar-check fs-4 mb-1"></i>
                <span class="small fw-semibold">Atender con cita</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.citas.create') }}" class="btn w-100 d-flex flex-column align-items-center py-3 btn-outline-secondary">
                <i class="bi bi-person-plus fs-4 mb-1"></i>
                <span class="small fw-semibold">Nueva atención sin cita</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.clientes.create') }}" class="btn w-100 d-flex flex-column align-items-center py-3 btn-outline-secondary">
                <i class="bi bi-person-vcard fs-4 mb-1"></i>
                <span class="small fw-semibold">Registrar cliente</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.vehiculos.create') }}" class="btn w-100 d-flex flex-column align-items-center py-3 btn-outline-secondary">
                <i class="bi bi-car-front fs-4 mb-1"></i>
                <span class="small fw-semibold">Registrar vehículo</span>
            </a>
        </div>
    </div>

    {{-- TARJETAS --}}
    <section class="admin-stats mb-4">
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-warning);"><span class="admin-stats__label">Citas solicitadas</span><span class="admin-stats__value">{{ $citasSolicitadas }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-success);"><span class="admin-stats__label">Confirmadas hoy</span><span class="admin-stats__value">{{ $citasConfirmadasHoy }}</span></div>
        <div class="admin-stats__item"><span class="admin-stats__label">Órdenes esperando mec.</span><span class="admin-stats__value">{{ $ordenesEsperando }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-info);"><span class="admin-stats__label">Vehículos listos</span><span class="admin-stats__value">{{ $vehiculosListos }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-danger);"><span class="admin-stats__label">Autorizaciones pend.</span><span class="admin-stats__value">{{ $autorizacionesPendientes }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-warning);"><span class="admin-stats__label">Pagos pendientes</span><span class="admin-stats__value">{{ $pagosPendientes }}</span></div>
        <div class="admin-stats__item"><span class="admin-stats__label">Entregas hoy</span><span class="admin-stats__value">{{ $entregasHoy }}</span></div>
    </section>

    <div class="row g-3">
        {{-- AGENDA DEL DÍA --}}
        <div class="col-lg-8">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Agenda de hoy</h6>
                    <a href="{{ route('admin.citas.index') }}" class="small">Ver todas</a>
                </div>
                @if ($agenda->isEmpty())
                    <div class="p-4 text-center text-muted small">No hay citas para hoy</div>
                @else
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead><tr><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Mecánico</th><th>Estado</th></tr></thead>
                            <tbody>
                                @foreach ($agenda as $c)
                                    <tr>
                                        <td>{{ $c->hora ? \Carbon\Carbon::parse($c->hora)->format('H:i') : '—' }}</td>
                                        <td class="fw-semibold">{{ $c->cliente?->nombre_completo ?? '—' }}</td>
                                        <td>{{ $c->vehiculo?->placa ?? '—' }}</td>
                                        <td>{{ $c->servicio?->nombre ?? $c->tipo ?? '—' }}</td>
                                        <td>{{ $c->mecanico?->empleado?->nombre_completo ?? '—' }}</td>
                                        <td><span class="badge bg-{{ $c->estado === 'confirmada' ? 'success' : ($c->estado === 'cancelada' ? 'danger' : 'warning') }}">{{ $c->estado_label }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- MECÁNICOS DISPONIBLES --}}
        <div class="col-lg-4">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Mecánicos disponibles</h6>
                </div>
                @if (empty($mecanicos))
                    <div class="p-4 text-center text-muted small">Sin datos</div>
                @else
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead><tr><th>Mecánico</th><th>Activos</th><th>Estado</th></tr></thead>
                            <tbody>
                                @foreach ($mecanicos as $m)
                                    <tr>
                                        <td class="fw-semibold">{{ $m['nombre'] }}</td>
                                        <td>{{ $m['activos'] }}</td>
                                        <td><span class="badge bg-{{ $m['disponible'] ? 'success' : 'warning' }}">{{ $m['disponible'] ? 'Disponible' : 'Ocupado' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
