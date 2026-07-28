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

                            @if ($diagnostico)
                                <div class="col-12">
                                    <h6 class="text-muted small text-uppercase">Diagnóstico del mecánico</h6>
                                    @if ($diagnostico->problema_encontrado)
                                        <p><strong>Problema encontrado:</strong> {{ $diagnostico->problema_encontrado }}</p>
                                    @endif
                                    @if ($diagnostico->causa_probable)
                                        <p><strong>Causa probable:</strong> {{ $diagnostico->causa_probable }}</p>
                                    @endif
                                    @if ($diagnostico->recomendacion)
                                        <p><strong>Recomendación:</strong> {{ $diagnostico->recomendacion }}</p>
                                    @endif
                                    @if ($diagnostico->observacion_cliente)
                                        <div class="p-2 rounded" style="background:#fef2f2;">
                                            <small><i class="bi bi-chat-quote text-danger"></i> {{ $diagnostico->observacion_cliente }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <h6 class="text-muted small text-uppercase mb-2">Avance del trabajo</h6>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="progress flex-grow-1" style="height:14px;">
                                <div class="progress-bar" style="width:{{ $asignacion?->porcentaje_avance ?? 0 }}%;background:#E31E24;" role="progressbar"></div>
                            </div>
                            <span class="fw-bold fs-5">{{ $asignacion?->porcentaje_avance ?? 0 }}%</span>
                        </div>

                        {{-- ESTIMACIÓN DE TIEMPO --}}
                        @if ($estimacion)
                            <div class="p-3 rounded mb-3" style="background:#f0f9ff;border:1px solid #bae6fd;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-clock-history text-primary"></i>
                                    <strong>Tiempo estimado de entrega</strong>
                                </div>
                                <div class="small">
                                    El mecánico estima que el trabajo tomará entre
                                    <strong>{{ $estimacion->duracion_minima_minutos }} y {{ $estimacion->duracion_maxima_minutos }} minutos</strong>
                                    @if ($estimacion->observacion_cliente)
                                        <br><i class="bi bi-chat-quote text-muted"></i> {{ $estimacion->observacion_cliente }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- LÍNEA DE TIEMPO DE AVANCES --}}
                        @if ($avances->isNotEmpty())
                            <h6 class="text-muted small text-uppercase mb-3">Reporte del mecánico</h6>
                            <div class="position-relative" style="padding-left:24px;">
                                <div style="position:absolute;left:8px;top:8px;bottom:8px;width:2px;background:#e2e8f0;"></div>
                                @foreach ($avances as $a)
                                    <div class="position-relative mb-3" style="padding-left:16px;">
                                        <div style="position:absolute;left:-20px;top:4px;width:12px;height:12px;border-radius:50%;background:#E31E24;border:2px solid #fff;"></div>
                                        <div class="small">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $a->titulo }}</strong>
                                                <span class="text-muted">{{ $a->created_at->format('d/m H:i') }}</span>
                                            </div>
                                            @if ($a->descripcion)
                                                <div class="text-muted">{{ $a->descripcion }}</div>
                                            @endif
                                            @if ($a->nota_cliente)
                                                <div class="mt-1 p-2 rounded" style="background:#fef2f2;font-size:.8rem;">
                                                    <i class="bi bi-chat-quote text-danger"></i> {{ $a->nota_cliente }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
