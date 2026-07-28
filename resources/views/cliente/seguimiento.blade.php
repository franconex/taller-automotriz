@extends('layouts.cliente-sidebar')

@section('title', 'Seguimiento')
@section('navbar-title', 'Seguimiento')

@section('content')
    @if (! $ordenActiva)
        <div class="text-center text-muted py-5">
            <i class="bi bi-clipboard-data display-4 d-block mb-3"></i>
            <p>No tienes órdenes activas en este momento.</p>
            <p class="small">Cuando tu vehículo esté en taller podrás ver el progreso aquí.</p>
        </div>
    @else
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $ordenActiva->numero_orden }}</h5>
                        <span class="badge bg-info fs-6">{{ ucfirst(str_replace('_', ' ', $ordenActiva->estado)) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted small text-uppercase">Vehículo</h6>
                                <p class="fw-semibold">{{ $ordenActiva->vehiculo?->placa ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted small text-uppercase">Fecha de ingreso</h6>
                                <p>{{ $ordenActiva->fecha_emision?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted small text-uppercase">Problema reportado</h6>
                                <p>{{ $ordenActiva->descripcion_problema ?? '—' }}</p>
                            </div>
                            @if ($ordenActiva->diagnostico_general)
                                <div class="col-12">
                                    <h6 class="text-muted small text-uppercase">Diagnóstico</h6>
                                    <p>{{ $ordenActiva->diagnostico_general }}</p>
                                </div>
                            @endif
                        </div>

                        <h6 class="text-muted small text-uppercase mb-2">Avance</h6>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="progress flex-grow-1" style="height:12px;">
                                <div class="progress-bar" style="width:{{ $asignacion?->porcentaje_avance ?? 0 }}%;background:#E31E24;" role="progressbar"></div>
                            </div>
                            <span class="fw-bold">{{ $asignacion?->porcentaje_avance ?? 0 }}%</span>
                        </div>

                        @if ($asignacion?->proximo_paso)
                            <p class="mb-0"><strong>Próximo paso:</strong> {{ $asignacion->proximo_paso }}</p>
                        @endif
                    </div>
                </div>

                {{-- NOTAS VISIBLES --}}
                @if ($asignacion && $asignacion->notasVisiblesCliente->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0">Notas del taller</h6>
                        </div>
                        <div class="card-body p-0">
                            @foreach ($asignacion->notasVisiblesCliente as $nota)
                                <div class="p-3 border-bottom">
                                    <div class="small text-muted mb-1">{{ $nota->created_at->format('d/m/Y H:i') }}</div>
                                    <p class="mb-0">{{ $nota->contenido }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                {{-- SERVICIOS Y REPUESTOS --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">Servicios y repuestos</h6>
                    </div>
                    <div class="card-body p-0">
                        @if ($ordenActiva->detalles->isNotEmpty())
                            @foreach ($ordenActiva->detalles as $d)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold small">{{ $d->servicio?->nombre ?? $d->repuesto?->nombre ?? $d->descripcion }}</span>
                                        <span class="small text-muted">{{ $d->cantidad ?? 1 }}x</span>
                                    </div>
                                    <small class="text-muted">{{ ucfirst($d->tipo) }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted small">Sin detalles registrados</div>
                        @endif
                    </div>
                </div>

                {{-- MECÁNICO ASIGNADO --}}
                @if ($asignacion?->mecanico)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0">Mecánico asignado</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 fw-semibold">{{ $asignacion->mecanico->empleado?->nombre_completo ?? '—' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
