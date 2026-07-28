@extends('layouts.cliente-sidebar')

@section('title', 'Seguimiento')
@section('navbar-title', 'Seguimiento')

@section('content')

<style>
  .track-header {
    background: #0B1D3A;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    color: #fff;
  }
  .track-header .order-number {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    opacity: 0.7;
  }
  .track-header .order-id {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
  }
  .track-header .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.85rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
  }
  .progress-track {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
  }
  .progress-bar-container {
    position: relative;
    height: 20px;
    background: #e5e7eb;
    border-radius: 100px;
    overflow: hidden;
  }
  .progress-bar-fill {
    height: 100%;
    border-radius: 100px;
    transition: width 0.6s ease;
    position: relative;
  }
  .progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
    animation: shimmer 2s ease-in-out infinite;
  }
  @keyframes shimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
  .progress-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
  }
  .progress-label {
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.05em;
    text-transform: uppercase;
  }
  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
  }
  .info-cell {
    background: #f9fafb;
    border-radius: 8px;
    padding: 0.75rem 1rem;
  }
  .info-cell .label {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6B7280;
    margin-bottom: 0.2rem;
  }
  .info-cell .value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0B1D3A;
  }
  .diagnosis-block {
    background: #f9fafb;
    border-left: 3px solid #D62828;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
  }
  .timeline {
    padding-left: 1.5rem;
    position: relative;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e5e7eb;
  }
  .timeline-item {
    position: relative;
    padding-left: 1.25rem;
    padding-bottom: 1.25rem;
  }
  .timeline-item:last-child { padding-bottom: 0; }
  .timeline-dot {
    position: absolute;
    left: -1.5rem;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #D62828;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #e5e7eb;
  }
  .timeline-dot.completed {
    background: #2B9348;
  }
  .timeline-title {
    font-weight: 600;
    font-size: 0.9rem;
    color: #0B1D3A;
  }
  .timeline-meta {
    font-size: 0.75rem;
    color: #6B7280;
    margin-top: 0.15rem;
  }
  .timeline-desc {
    font-size: 0.85rem;
    color: #4B5563;
    margin-top: 0.3rem;
  }
  .price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    font-size: 0.85rem;
  }
  .price-row.border-b { border-bottom: 1px solid #f3f4f6; }
  .price-total {
    color: #D62828;
    font-weight: 700;
    font-size: 1.1rem;
  }
  .note-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.85rem;
  }
  .note-item:last-child { border-bottom: none; }
  .note-time {
    font-size: 0.7rem;
    color: #9CA3AF;
    margin-bottom: 0.15rem;
  }
  .section-title {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6B7280;
    padding: 0.75rem 1rem 0.4rem;
  }
  .empty-state {
    text-align: center;
    padding: 4rem 1rem;
    color: #6B7280;
  }
  .empty-state svg {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    opacity: 0.25;
  }
</style>

{{-- COTIZACIONES PENDIENTES — SOLO SI HAY --}}
@if ($cotizacionesPendientes->isNotEmpty())
<div style="background:#FEF2F0;border:1px solid #FED7D4;border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
    <div style="display:flex;align-items:start;gap:0.75rem;">
        <div style="background:#D62828;color:#fff;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-file-earmark-text" style="font-size:1.1rem;"></i>
        </div>
        <div style="flex:1;">
            <p style="font-weight:700;color:#0B1D3A;margin-bottom:0.25rem;">
                {{ $cotizacionesPendientes->count() }} cotización(es) pendiente(s) de tu respuesta
            </p>
            <p style="font-size:0.8rem;color:#6B7280;margin-bottom:0.75rem;">
                Revisá el presupuesto y autorizalo para que el trabajo continúe.
            </p>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.8rem;background:#fff;border-radius:8px;overflow:hidden;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#6B7280;text-transform:uppercase;font-size:0.65rem;letter-spacing:0.05em;">Fecha</th>
                            <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#6B7280;text-transform:uppercase;font-size:0.65rem;letter-spacing:0.05em;">Detalle</th>
                            <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#6B7280;text-transform:uppercase;font-size:0.65rem;letter-spacing:0.05em;">Tiempo</th>
                            <th style="padding:0.5rem 0.75rem;text-align:right;font-weight:600;color:#6B7280;text-transform:uppercase;font-size:0.65rem;letter-spacing:0.05em;">Importe</th>
                            <th style="padding:0.5rem 0.75rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cotizacionesPendientes as $a)
                        <tr style="border-top:1px solid #f3f4f6;">
                            <td style="padding:0.5rem 0.75rem;white-space:nowrap;">{{ $a->fecha_solicitud?->format('d/m H:i') }}</td>
                            <td style="padding:0.5rem 0.75rem;">
                                <span style="font-weight:600;color:#0B1D3A;">{{ $a->titulo }}</span>
                                <span style="display:block;font-size:0.75rem;color:#6B7280;">{{ $a->cita?->vehiculo?->placa ?? ($a->ordenTrabajo?->vehiculo?->placa ?? '—') }}</span>
                            </td>
                            <td style="padding:0.5rem 0.75rem;text-align:center;color:#6B7280;white-space:nowrap;">{{ $a->tiempo_estimado_label }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:right;font-weight:700;color:#D62828;white-space:nowrap;">Bs {{ number_format($a->importe, 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:right;">
                                <a href="{{ route('cliente.autorizaciones') }}" style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.3rem 0.75rem;background:#D62828;color:#fff;border-radius:100px;text-decoration:none;font-size:0.75rem;font-weight:600;">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- SIN ORDEN ACTIVA --}}
@if (! $ordenActiva)
<div class="empty-state">
    <i class="bi bi-clipboard-data" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:0.25;"></i>
    <p style="font-weight:600;color:#0B1D3A;margin-bottom:0.25rem;">No tenés órdenes activas</p>
    <p style="font-size:0.85rem;">Cuando tu vehículo esté en taller, vas a ver su progreso acá.</p>
    @if ($cotizacionesPendientes->isEmpty())
    <p style="margin-top:1rem;font-size:0.8rem;">
        <a href="{{ route('cliente.citas.crear') }}" style="color:#D62828;font-weight:600;text-decoration:none;">Agendá una cita →</a>
    </p>
    @endif
</div>
@else

{{-- CON ORDEN ACTIVA --}}
<div style="max-width:1000px;margin:0 auto;">

    {{-- ORDER HEADER --}}
    <div class="track-header">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
            <div>
                <div class="order-number">Orden de trabajo</div>
                <div class="order-id">{{ $ordenActiva->numero_orden }}</div>
            </div>
            <div>
                @php
                $badge = match($ordenActiva->estado) {
                    'finalizada_mecanico','lista_entrega' => ['bg' => '#2B9348', 'label' => 'Completado', 'icon' => 'bi-check-circle-fill'],
                    'recibida' => ['bg' => '#2563EB', 'label' => 'Recibido', 'icon' => 'bi-inbox-fill'],
                    'diagnostico' => ['bg' => '#D97706', 'label' => 'En diagnóstico', 'icon' => 'bi-search-heart-fill'],
                    'en_proceso' => ['bg' => '#D62828', 'label' => 'En proceso', 'icon' => 'bi-gear-wide-connected'],
                    'pausada' => ['bg' => '#6B7280', 'label' => 'Pausada', 'icon' => 'bi-pause-circle-fill'],
                    default => ['bg' => '#6B7280', 'label' => ucfirst(str_replace('_',' ',$ordenActiva->estado)), 'icon' => 'bi-circle-fill'],
                };
                @endphp
                <span class="status-pill" style="background:{{ $badge['bg'] }};color:#fff;">
                    <i class="{{ $badge['icon'] }}"></i>
                    {{ $badge['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div class="info-cell">
            <div class="label">Vehículo</div>
            <div class="value">{{ $ordenActiva->vehiculo?->marca ?? '' }} {{ $ordenActiva->vehiculo?->modelo ?? '' }} · {{ $ordenActiva->vehiculo?->placa ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="label">Ingreso</div>
            <div class="value">{{ $ordenActiva->fecha_emision?->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-cell" style="grid-column:span 2;">
            <div class="label">Problema reportado</div>
            <div class="value" style="font-weight:500;">{{ $ordenActiva->descripcion_problema ?? '—' }}</div>
        </div>
    </div>

    {{-- PROGRESS --}}
    @php $pct = $asignacion?->porcentaje_avance ?? 0; @endphp
    @if (in_array($ordenActiva->estado, ['finalizada_mecanico', 'lista_entrega']))
    <div class="progress-track" style="border-left:4px solid #2B9348;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <div>
                <span class="progress-label" style="color:#2B9348;">Completado</span>
                <div style="font-weight:700;color:#0B1D3A;font-size:0.95rem;">Tu vehículo está listo</div>
            </div>
            <div class="progress-number" style="color:#2B9348;">100%</div>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width:100%;background:#2B9348;"></div>
        </div>
        <p style="margin-top:0.75rem;font-size:0.85rem;color:#6B7280;">Podés pasar por el taller a retirarlo cuando quieras.</p>
    </div>
    @else
    <div class="progress-track">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <div>
                <span class="progress-label">Progreso</span>
                <div style="font-weight:600;color:#0B1D3A;font-size:0.9rem;">{{ $badge['label'] }}</div>
            </div>
            <div class="progress-number" style="color:#D62828;">{{ $pct }}%</div>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#D62828,#EF4444);"></div>
        </div>
        @if ($estimacion)
        <p style="margin-top:0.75rem;font-size:0.8rem;color:#6B7280;">
            Tiempo estimado:
            <strong style="color:#0B1D3A;">{{ $estimacion->duracion_minima_minutos }}–{{ $estimacion->duracion_maxima_minutos }} min</strong>
            @if ($estimacion->observacion_cliente)
                <br><span style="font-style:italic;font-size:0.75rem;">{{ $estimacion->observacion_cliente }}</span>
            @endif
        </p>
        @endif
    </div>
    @endif

    {{-- TWO-COLUMN LAYOUT --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        {{-- LEFT: DIAGNÓSTICO + TIMELINE --}}
        <div>
            {{-- DIAGNÓSTICO --}}
            @if ($diagnostico)
            <div class="diagnosis-block">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                    <i class="bi bi-search-heart-fill" style="color:#D62828;"></i>
                    <span style="font-weight:700;font-size:0.85rem;color:#0B1D3A;text-transform:uppercase;letter-spacing:0.05em;">Diagnóstico</span>
                </div>
                @if ($diagnostico->problema_encontrado)
                <p style="font-size:0.85rem;margin-bottom:0.25rem;"><span style="font-weight:600;">Problema:</span> {{ $diagnostico->problema_encontrado }}</p>
                @endif
                @if ($diagnostico->causa_probable)
                <p style="font-size:0.85rem;margin-bottom:0.25rem;"><span style="font-weight:600;">Causa:</span> {{ $diagnostico->causa_probable }}</p>
                @endif
                @if ($diagnostico->recomendacion)
                <p style="font-size:0.85rem;margin-bottom:0;"><span style="font-weight:600;">Recomendación:</span> {{ $diagnostico->recomendacion }}</p>
                @endif
                @if ($diagnostico->observacion_cliente)
                <div style="margin-top:0.5rem;padding:0.5rem 0.75rem;background:#FEF2F0;border-radius:6px;font-size:0.8rem;">
                    <i class="bi bi-chat-quote" style="color:#D62828;margin-right:0.3rem;"></i>
                    {{ $diagnostico->observacion_cliente }}
                </div>
                @endif
            </div>
            @endif

            {{-- TIMELINE --}}
            @if ($avances->isNotEmpty())
            <div style="background:#fff;border-radius:10px;padding:1.25rem;margin-top:1rem;border:1px solid #e5e7eb;">
                <div style="font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;margin-bottom:1rem;">
                    <i class="bi bi-clock-history me-1"></i> Reporte del mecánico
                </div>
                <div class="timeline">
                    @foreach ($avances as $a)
                    <div class="timeline-item">
                        <div class="timeline-dot {{ in_array($ordenActiva->estado,['finalizada_mecanico','lista_entrega']) && $loop->last ? 'completed' : '' }}"></div>
                        <div class="timeline-title">{{ $a->titulo }}</div>
                        <div class="timeline-meta">{{ $a->created_at->format('d/m/Y H:i') }}</div>
                        @if ($a->descripcion)
                        <div class="timeline-desc">{{ $a->descripcion }}</div>
                        @endif
                        @if ($a->nota_cliente)
                        <div style="margin-top:0.4rem;padding:0.4rem 0.6rem;background:#FEF2F0;border-radius:6px;font-size:0.8rem;">
                            <i class="bi bi-chat-quote" style="color:#D62828;margin-right:0.25rem;"></i>
                            {{ $a->nota_cliente }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- NOTAS DEL TALLER --}}
            @if ($asignacion && $asignacion->notasVisiblesCliente->isNotEmpty())
            <div style="background:#fff;border-radius:10px;padding:1.25rem;margin-top:1rem;border:1px solid #e5e7eb;">
                <div style="font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;margin-bottom:0.75rem;">
                    <i class="bi bi-chat-dots me-1"></i> Notas del taller
                </div>
                @foreach ($asignacion->notasVisiblesCliente as $nota)
                <div class="note-item">
                    <div class="note-time">{{ $nota->created_at->format('d/m/Y H:i') }}</div>
                    <p style="margin:0;">{{ $nota->contenido }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- RIGHT: SERVICIOS + REPUESTOS + MECÁNICO --}}
        <div>

            {{-- SERVICIOS & REPUESTOS --}}
            @php
                $servItems = $ordenActiva->serviciosMecanico ?? collect();
                $repItems = $ordenActiva->repuestosMecanico ?? collect();
                $totalServ = $servItems->sum('precio_base');
                $totalRep = $repItems->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
                $totalGeneral = $totalServ + $totalRep;
            @endphp
            <div style="background:#fff;border-radius:10px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:1rem;">
                <div style="padding:1rem;border-bottom:1px solid #f3f4f6;">
                    <div style="font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;">
                        <i class="bi bi-receipt me-1"></i> Servicios y repuestos
                    </div>
                </div>

                @if ($servItems->isNotEmpty() || $repItems->isNotEmpty() || $ordenActiva->detalles->isNotEmpty())
                    @if ($servItems->isNotEmpty())
                        <div class="section-title">Servicios</div>
                        @foreach ($servItems as $s)
                        <div class="price-row border-b" style="padding-left:1rem;padding-right:1rem;">
                            <span>{{ $s->nombre_servicio }}</span>
                            <span style="font-weight:600;">Bs {{ number_format($s->precio_base, 2) }}</span>
                        </div>
                        @endforeach
                    @endif
                    @if ($repItems->isNotEmpty())
                        <div class="section-title">Repuestos</div>
                        @foreach ($repItems as $r)
                        <div class="price-row border-b" style="padding-left:1rem;padding-right:1rem;">
                            <span>{{ $r->repuesto?->nombre ?? 'Repuesto #'.$r->repuesto_id }} <span style="color:#9CA3AF;">x{{ $r->cantidad }}</span></span>
                            <span style="font-weight:600;">Bs {{ number_format($r->cantidad * $r->precio_unitario_snapshot, 2) }}</span>
                        </div>
                        @endforeach
                    @endif
                    @if ($totalGeneral > 0)
                        <div style="padding:0.75rem 1rem;background:#f9fafb;border-top:1px solid #e5e7eb;">
                            <div class="price-row" style="font-size:0.8rem;">
                                <span style="color:#6B7280;">Subtotal servicios</span>
                                <span>Bs {{ number_format($totalServ, 2) }}</span>
                            </div>
                            <div class="price-row" style="font-size:0.8rem;">
                                <span style="color:#6B7280;">Subtotal repuestos</span>
                                <span>Bs {{ number_format($totalRep, 2) }}</span>
                            </div>
                            <div class="price-row">
                                <span style="font-weight:700;color:#0B1D3A;">Total</span>
                                <span class="price-total">Bs {{ number_format($totalGeneral, 2) }}</span>
                            </div>
                        </div>
                    @endif
                @else
                    <div style="padding:2rem;text-align:center;color:#9CA3AF;font-size:0.85rem;">
                        <i class="bi bi-receipt" style="display:block;font-size:2rem;margin-bottom:0.5rem;opacity:0.3;"></i>
                        Sin servicios ni repuestos registrados
                    </div>
                @endif
            </div>

            {{-- MECÁNICO --}}
            @if ($asignacion?->mecanico)
            <div style="background:#fff;border-radius:10px;padding:1rem;border:1px solid #e5e7eb;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div style="width:40px;height:40px;border-radius:40px;background:#0B1D3A;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.9rem;font-weight:700;">
                        {{ substr($asignacion->mecanico->empleado?->nombre_completo ?? 'M', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-size:0.65rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6B7280;">Mecánico asignado</div>
                        <div style="font-weight:600;color:#0B1D3A;">{{ $asignacion->mecanico->empleado?->nombre_completo ?? '—' }}</div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endif
@endsection
