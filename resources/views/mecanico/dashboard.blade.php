@extends('layouts.admin')

@section('title', 'Mi Panel — Mecánico')
@section('navbar-title', 'Mi Panel')

@section('content')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="mb-1">Bienvenido, {{ Auth::user()->nombre }}</h4>
            <p class="text-muted mb-0">Panel de trabajo del mecánico</p>
        </div>
        <div class="text-end">
            <a href="{{ route('mecanico.ordenes.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-list-ul"></i> Mis órdenes
            </a>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="row g-2 mb-4">
        @php
            $tarjetas = [
                ['label' => 'Programadas', 'key' => 'programada', 'color' => '#6c757d'],
                ['label' => 'Recibidas',   'key' => 'recibida',   'color' => '#0dcaf0'],
                ['label' => 'Diagnóstico', 'key' => 'diagnostico','color' => '#ffc107'],
                ['label' => 'En proceso',  'key' => 'en_proceso', 'color' => '#fd7e14'],
                ['label' => 'Esp. repuesto','key' => 'esperando_repuesto', 'color' => '#6f42c1'],
                ['label' => 'Pend. autorización','key' => 'pendiente_autorizacion', 'color' => '#dc3545'],
            ];
        @endphp
        @foreach ($tarjetas as $t)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid {{ $t['color'] }} !important;">
                    <div class="h4 mb-0 fw-bold">{{ $counts[$t['key']] }}</div>
                    <small class="text-muted">{{ $t['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PRÓXIMAS CITAS CONFIRMADAS --}}
    @if ($citasSinOrden->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check"></i> Próximas citas confirmadas</h6>
                <span class="badge bg-primary">{{ $citasSinOrden->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Teléfono</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($citasSinOrden as $c)
                                <tr>
                                    <td class="fw-semibold">{{ $c->fecha?->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->hora)->format('H:i') }}</td>
                                    <td>{{ $c->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $c->vehiculo?->placa ?? '—' }} <small class="text-muted">{{ $c->vehiculo?->marca }} {{ $c->vehiculo?->modelo }}</small></td>
                                    <td>{{ $c->servicio?->nombre ?? $c->tipo ?? '—' }}</td>
                                    <td><small>{{ $c->cliente?->telefono ?? '—' }}</small></td>
                                    <td>
                                        <form method="POST" action="{{ route('mecanico.citas.iniciar', $c) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Iniciar trabajo? Se creará la orden y empezará el tiempo.');">
                                                <i class="bi bi-play-fill"></i> Iniciar trabajo
                                            </button>
                                        </form>
                                        <a href="{{ route('mecanico.cotizacion.create', $c) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-cash-coin"></i> Cotizar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-2 small text-muted">
                <i class="bi bi-info-circle"></i> Cuando el cliente llegue, presioná <strong>"Iniciar trabajo"</strong>. Si va a dejar el vehículo, presioná <strong>"Cotizar"</strong>.
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check"></i> Próximas citas</h6>
            </div>
            <div class="card-body p-4 text-center text-muted">
                <i class="bi bi-calendar2-week display-5 d-block mb-2 opacity-50"></i>
                <p class="mb-0">No tenés citas próximas.</p>
            </div>
        </div>
    @endif

    {{-- TRABAJOS ACTUALES ASIGNADOS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-wrench-adjustable"></i> Mis trabajos actuales</h6>
            <span class="badge bg-primary">{{ $misAsignaciones->count() }}</span>
        </div>
        <div class="card-body p-0">
            @if ($misAsignaciones->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">No tenés trabajos asignados en este momento.</p>
                    @if ($ordenesDisponibles->isNotEmpty())
                        <p class="small mt-2">Hay órdenes disponibles para tomar. Mirá abajo.</p>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Orden</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Placa</th>
                                <th>Estado</th>
                                <th>Servicios</th>
                                <th>Repuestos</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($misAsignaciones as $a)
                                @php $o = $a->ordenTrabajo; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                                    <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($o->estado) {
                                                'programada' => 'bg-secondary',
                                                'recibida' => 'bg-info',
                                                'diagnostico' => 'bg-warning text-dark',
                                                'en_proceso' => 'bg-primary',
                                                'esperando_repuesto' => 'bg-purple',
                                                'pausada' => 'bg-dark',
                                                'pendiente_autorizacion' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $o->estado)) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $o->serviciosMecanico->count() }}</td>
                                    <td class="small">{{ $o->repuestosMecanico->count() }}</td>
                                    <td>
                                        <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ÓRDENES DISPONIBLES (SIN MECÁNICO) --}}
    @if ($ordenesDisponibles->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-box-arrow-in-down"></i> Órdenes disponibles para tomar</h6>
                <span class="badge bg-success">{{ $ordenesDisponibles->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Orden</th><th>Cliente</th><th>Vehículo</th><th>Placa</th><th>Ingreso</th><th>Estado</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenesDisponibles as $o)
                                <tr>
                                    <td class="fw-semibold">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                                    <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                                    <td class="small">{{ $o->fecha_emision?->format('d/m H:i') }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $o->estado)) }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('mecanico.ordenes.show', $o) }}/tomar" class="d-inline" onsubmit="return confirm('¿Tomar esta orden?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-hand-index-thumb"></i> Tomar
                                            </button>
                                        </form>
                                        <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- TRABAJOS TERMINADOS --}}
    @if ($terminados->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-check-circle"></i> Trabajos terminados recientemente</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Orden</th><th>Cliente</th><th>Vehículo</th><th>Placa</th><th>Finalizado</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($terminados as $a)
                                @php $o = $a->ordenTrabajo; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                                    <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                                    <td class="small">{{ $a->fecha_finalizacion?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection
