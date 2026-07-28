@extends('layouts.admin')

@section('title', 'Mi Panel — Mecánico')
@section('navbar-title', 'Mi Panel')

@section('content')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-grid-1x2-fill text-primary me-2"></i>Bienvenido, {{ Auth::user()->nombre }}
            </h4>
            <p class="text-muted mb-0 small"><i class="bi bi-geo-alt me-1"></i> Panel de trabajo del mecánico</p>
        </div>
        <div>
            <a href="{{ route('mecanico.ordenes.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-journal-text me-1"></i> Mis órdenes
                @if ($misAsignaciones->count() > 0)
                    <span class="badge bg-primary ms-1">{{ $misAsignaciones->count() }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- TARJETAS DE ESTADOS --}}
    <div class="row g-2 mb-4">
        @php
            $tarjetas = [
                ['label' => 'Programadas',  'key' => 'programada',  'color' => '#6c757d', 'icon' => 'bi-calendar2'],
                ['label' => 'Recibidas',    'key' => 'recibida',    'color' => '#0dcaf0', 'icon' => 'bi-inbox'],
                ['label' => 'Diagnóstico',  'key' => 'diagnostico', 'color' => '#ffc107', 'icon' => 'bi-search-heart'],
                ['label' => 'En proceso',   'key' => 'en_proceso',  'color' => '#fd7e14', 'icon' => 'bi-gear-wide-connected'],
                ['label' => 'Esp. repuesto','key' => 'esperando_repuesto', 'color' => '#6f42c1', 'icon' => 'bi-box-seam'],
                ['label' => 'Pend. autorización', 'key' => 'pendiente_autorizacion', 'color' => '#dc3545', 'icon' => 'bi-file-earmark-text'],
            ];
        @endphp
        @foreach ($tarjetas as $t)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:3px solid {{ $t['color'] }} !important;">
                    <div class="card-body text-center p-2">
                        <div class="h4 mb-0 fw-bold" style="color:{{ $t['color'] }}">{{ $counts[$t['key']] }}</div>
                        <small class="text-muted d-flex align-items-center justify-content-center gap-1 mt-1">
                            <i class="{{ $t['icon'] }}" style="font-size:.7rem;"></i>
                            {{ $t['label'] }}
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PRÓXIMAS CITAS CONFIRMADAS --}}
    @if ($citasSinOrden->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill d-flex align-items-center justify-content-center" style="width:26px;height:26px;font-size:.75rem;"><i class="bi bi-calendar-check"></i></span>
                    Próximas citas confirmadas
                </h6>
                <span class="badge bg-primary rounded-pill fs-6">{{ $citasSinOrden->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light small">
                            <tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Contacto</th><th class="text-end">Acciones</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($citasSinOrden as $c)
                                <tr>
                                    <td class="fw-semibold">{{ $c->fecha?->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->hora)->format('H:i') }}</td>
                                    <td><span class="fw-semibold">{{ $c->cliente?->nombre_completo ?? '—' }}</span></td>
                                    <td>{{ $c->vehiculo?->placa ?? '—' }} <small class="text-muted">{{ $c->vehiculo?->marca }} {{ $c->vehiculo?->modelo }}</small></td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info">{{ $c->servicio?->nombre ?? $c->tipo ?? '—' }}</span></td>
                                    <td><small class="text-muted"><i class="bi bi-telephone me-1" style="font-size:.65rem;"></i>{{ $c->cliente?->telefono ?? '—' }}</small></td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <form method="POST" action="{{ route('mecanico.citas.iniciar', $c) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('¿Iniciar trabajo? Se creará la orden.');">
                                                    <i class="bi bi-play-fill"></i> Iniciar
                                                </button>
                                            </form>
                                            <a href="{{ route('mecanico.cotizacion.create', $c) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-file-earmark-text"></i> Cotizar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light bg-opacity-50 py-2 small text-muted d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                <span>Cliente presente → <strong>"Iniciar"</strong> · Va a dejar el vehículo → <strong>"Cotizar"</strong></span>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check text-primary"></i> Próximas citas
                </h6>
            </div>
            <div class="card-body p-4 text-center text-muted">
                <i class="bi bi-calendar2-week display-5 d-block mb-2 opacity-25 text-primary"></i>
                <p class="mb-0 fw-semibold">No tenés citas próximas</p>
                <small>Cuando te asignen una cita, aparecerá aquí.</small>
            </div>
        </div>
    @endif

    {{-- TRABAJOS ACTUALES ASIGNADOS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-tools text-primary"></i> Mis trabajos actuales
            </h6>
            <span class="badge bg-primary rounded-pill">{{ $misAsignaciones->count() }}</span>
        </div>
        <div class="card-body p-0">
            @if ($misAsignaciones->isEmpty())
                <div class="p-5 text-center">
                    <i class="bi bi-inbox display-4 d-block mb-3 opacity-25 text-primary"></i>
                    <p class="fw-semibold mb-1">No tenés trabajos asignados</p>
                    <p class="small text-muted mb-0">Cuando te asignen una orden, aparecerá aquí.</p>
                    @if ($ordenesDisponibles->isNotEmpty())
                        <p class="small mt-2 text-success"><i class="bi bi-arrow-down"></i> Hay órdenes disponibles para tomar más abajo.</p>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th>Orden</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Placa</th>
                                <th>Estado</th>
                                <th class="text-center">Serv.</th>
                                <th class="text-center">Rep.</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($misAsignaciones as $a)
                                @php $o = $a->ordenTrabajo; @endphp
                                <tr>
                                    <td class="fw-semibold small">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $o->vehiculo?->placa ?? '—' }}</span></td>
                                    <td>
                                        @php
                                            $badgeClass = match($o->estado) {
                                                'programada' => 'bg-secondary',
                                                'recibida' => 'bg-info',
                                                'diagnostico' => 'bg-warning text-dark',
                                                'en_proceso' => 'bg-primary',
                                                'esperando_repuesto' => 'bg-secondary',
                                                'pausada' => 'bg-dark',
                                                'pendiente_autorizacion' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                            $iconEstado = match($o->estado) {
                                                'programada' => 'bi-calendar2',
                                                'recibida' => 'bi-inbox',
                                                'diagnostico' => 'bi-search-heart',
                                                'en_proceso' => 'bi-gear-wide-connected',
                                                'esperando_repuesto' => 'bi-box-seam',
                                                'pausada' => 'bi-pause-circle',
                                                'pendiente_autorizacion' => 'bi-file-earmark-text',
                                                default => 'bi-question-circle',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} d-flex align-items-center gap-1" style="max-width:fit-content;">
                                            <i class="{{ $iconEstado }}" style="font-size:.6rem;"></i>
                                            {{ ucfirst(str_replace('_', ' ', $o->estado)) }}
                                        </span>
                                    </td>
                                    <td class="text-center small fw-semibold">{{ $o->serviciosMecanico->count() }}</td>
                                    <td class="text-center small fw-semibold">{{ $o->repuestosMecanico->count() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Ver
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
