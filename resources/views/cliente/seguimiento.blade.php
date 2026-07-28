@extends('layouts.cliente-sidebar')

@section('title', 'Seguimiento')
@section('navbar-title', 'Seguimiento')

@section('content')

    {{-- COTIZACIONES PENDIENTES --}}
    @if ($cotizacionesPendientes->isNotEmpty())
        <div class="alert alert-warning d-flex align-items-start mb-3">
            <i class="bi bi-file-earmark-text me-2 mt-1" style="font-size:1.2rem;"></i>
            <div class="w-100">
                <strong>Tienes {{ $cotizacionesPendientes->count() }} cotización(es) pendiente(s) de tu respuesta</strong>
                <p class="mb-2 small">El mecánico envió un presupuesto. Revisalo y autorizalo para que comiencen el trabajo.</p>
                <div class="table-responsive">
                    <table class="table table-sm table-warning table-bordered mb-0 bg-white">
                        <thead><tr><th>Fecha</th><th>Descripción</th><th>Tiempo est.</th><th>Importe</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($cotizacionesPendientes as $a)
                                @php $v = $a->cita?->vehiculo; @endphp
                                <tr>
                                    <td class="small">{{ $a->fecha_solicitud?->format('d/m H:i') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $a->titulo }}</span>
                                        <small class="d-block text-muted">{{ $v?->placa ?? ($a->ordenTrabajo?->vehiculo?->placa ?? '—') }}</small>
                                    </td>
                                    <td class="small">{{ $a->tiempo_estimado_label }}</td>
                                    <td class="fw-semibold" style="color:#E31E24;">Bs {{ number_format($a->importe, 2) }}</td>
                                    <td>
                                        <a href="{{ route('cliente.autorizaciones') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Ver
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

    @if (! $ordenActiva)
        <div class="text-center text-muted py-5">
            <i class="bi bi-clipboard-data display-4 d-block mb-3"></i>
            <p>No tienes órdenes activas en este momento.</p>
            <p class="small">Cuando tu vehículo esté en taller podrás ver el progreso aquí.</p>
            @if ($cotizacionesPendientes->isEmpty())
                <p class="small">Tampoco tienes cotizaciones pendientes. Si necesitas un servicio, <a href="{{ route('cliente.citas.crear') }}">agendá una cita</a>.</p>
            @endif
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

                        @php $pct = $asignacion?->porcentaje_avance ?? 0; @endphp
                        <h6 class="text-muted small text-uppercase mb-2">Avance del trabajo</h6>
                        @if (in_array($ordenActiva->estado, ['finalizada_mecanico', 'lista_entrega']))
                            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <div>
                                    <strong class="d-block">¡Trabajo completado!</strong>
                                    <small>Tu vehículo está listo para retirar. Pasá por el taller cuando quieras.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="progress flex-grow-1" style="height:14px;">
                                    <div class="progress-bar bg-success" style="width:100%" role="progressbar"></div>
                                </div>
                                <span class="fw-bold fs-5 text-success">100%</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="progress flex-grow-1" style="height:14px;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:#E31E24;" role="progressbar"></div>
                                </div>
                                <span class="fw-bold fs-5">{{ $pct }}%</span>
                            </div>
                        @endif

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
                        @php
                            $servItems = $ordenActiva->serviciosMecanico ?? collect();
                            $repItems = $ordenActiva->repuestosMecanico ?? collect();
                            $tieneItems = $servItems->isNotEmpty() || $repItems->isNotEmpty() || $ordenActiva->detalles->isNotEmpty();
                        @endphp
                        @if ($tieneItems)
                            @if ($servItems->isNotEmpty())
                                <div class="px-3 pt-2 pb-1 small text-muted text-uppercase fw-semibold">Servicios</div>
                                @foreach ($servItems as $s)
                                    <div class="px-3 py-1 border-bottom d-flex justify-content-between small">
                                        <span>{{ $s->nombre_servicio }}</span>
                                        <span class="fw-semibold">Bs {{ number_format($s->precio_base, 2) }}</span>
                                    </div>
                                @endforeach
                            @endif
                            @if ($repItems->isNotEmpty())
                                <div class="px-3 pt-2 pb-1 small text-muted text-uppercase fw-semibold">Repuestos</div>
                                @foreach ($repItems as $r)
                                    <div class="px-3 py-1 border-bottom d-flex justify-content-between small">
                                        <span>{{ $r->repuesto?->nombre ?? 'Repuesto #'.$r->repuesto_id }} x{{ $r->cantidad }}</span>
                                        <span class="fw-semibold">Bs {{ number_format($r->cantidad * $r->precio_unitario_snapshot, 2) }}</span>
                                    </div>
                                @endforeach
                            @endif
                            @if ($ordenActiva->detalles->isNotEmpty())
                                <div class="px-3 pt-2 pb-1 small text-muted text-uppercase fw-semibold">Detalles adicionales</div>
                                @foreach ($ordenActiva->detalles as $d)
                                    <div class="px-3 py-1 border-bottom small">
                                        <span>{{ $d->servicio?->nombre ?? $d->repuesto?->nombre ?? $d->descripcion }}</span>
                                        <span class="text-muted ms-2">{{ ucfirst($d->tipo) }}</span>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <div class="p-3 text-center text-muted small">Sin servicios ni repuestos registrados</div>
                        @endif
                        @php
                            $totalServ = $servItems->sum('precio_base');
                            $totalRep = $repItems->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
                            $totalGeneral = $totalServ + $totalRep;
                        @endphp
                        @if ($totalGeneral > 0)
                            <div class="p-3 border-top" style="background:#f8f9fa;">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Subtotal servicios:</span>
                                    <span>Bs {{ number_format($totalServ, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Subtotal repuestos:</span>
                                    <span>Bs {{ number_format($totalRep, 2) }}</span>
                                </div>
                                <hr class="my-1">
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span style="color:#E31E24;">Bs {{ number_format($totalGeneral, 2) }}</span>
                                </div>
                            </div>
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
