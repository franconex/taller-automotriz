@extends('layouts.cliente-sidebar')

@section('title', 'Seguimiento')
@section('navbar-title', 'Seguimiento')

@section('content')

<style>
  * { box-sizing: border-box; }
  .seg-box { border: 1px solid #d1d5db; background: #fff; margin-bottom: 1rem; }
  .seg-box-dark { background: #0B1D3A; color: #fff; margin-bottom: 1rem; border: 1px solid #0B1D3A; }
  .seg-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #6B7280; }
  .seg-value { font-size: 0.9rem; font-weight: 600; color: #111827; }
  .seg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
  .seg-grid > div { padding: 0.75rem 1rem; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
  .seg-grid > div:nth-child(2n) { border-right: 0; }
  .seg-grid > div:nth-last-child(-n+2) { border-bottom: 0; }
  .seg-pb-wrap { height: 24px; background: #e5e7eb; position: relative; }
  .seg-pb-fill { height: 100%; background: #D62828; transition: width .4s; }
  .seg-pb-fill.done { background: #2B9348; }
  .seg-pct { font-size: 1.75rem; font-weight: 800; line-height: 1; }
  .seg-tl { padding-left: 1.25rem; }
  .seg-tl::before { content: ''; position: absolute; left: 5px; top: 8px; bottom: 8px; width: 1px; background: #d1d5db; }
  .seg-tl-item { position: relative; padding: 0 0 1rem 1rem; }
  .seg-tl-item:last-child { padding-bottom: 0; }
  .seg-tl-dot { position: absolute; left: -1.25rem; top: 4px; width: 11px; height: 11px; background: #D62828; border: 2px solid #fff; outline: 1px solid #d1d5db; }
  .seg-tl-dot.done { background: #2B9348; }
  .seg-price { padding: 0.5rem 1rem; display: flex; justify-content: space-between; font-size: 0.85rem; border-bottom: 1px solid #f3f4f6; }
  .seg-price:last-child { border-bottom: 0; }
  .seg-section-title { padding: 0.5rem 1rem; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #6B7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
  .seg-empty { padding: 3rem 1rem; text-align: center; color: #9CA3AF; font-size: 0.85rem; }
  .seg-alert { border: 1px solid #FECACA; background: #FEF2F2; padding: 1rem; margin-bottom: 1rem; }
</style>

{{-- COTIZACIONES PENDIENTES --}}
@if ($cotizacionesPendientes->isNotEmpty())
<div class="seg-alert">
  <div style="display:flex;align-items:start;gap:0.75rem;">
    <div style="background:#D62828;color:#fff;width:36px;height:36px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;">
      <i class="bi bi-file-earmark-text"></i>
    </div>
    <div style="flex:1;min-width:0;">
      <p style="font-weight:700;color:#0B1D3A;margin:0 0 0.25rem;font-size:0.9rem;">{{ $cotizacionesPendientes->count() }} cotización(es) pendiente(s)</p>
      <p style="font-size:0.8rem;color:#6B7280;margin:0 0 0.75rem;">Revisá y autorizá para que el trabajo continúe.</p>
      <table style="width:100%;border-collapse:collapse;font-size:0.78rem;background:#fff;border:1px solid #FECACA;">
        <thead>
          <tr style="background:#f9fafb;">
            <th style="padding:0.4rem 0.6rem;text-align:left;font-weight:600;color:#6B7280;font-size:0.6rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e5e7eb;">Fecha</th>
            <th style="padding:0.4rem 0.6rem;text-align:left;font-weight:600;color:#6B7280;font-size:0.6rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e5e7eb;">Detalle</th>
            <th style="padding:0.4rem 0.6rem;text-align:center;font-weight:600;color:#6B7280;font-size:0.6rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e5e7eb;">Tiempo</th>
            <th style="padding:0.4rem 0.6rem;text-align:right;font-weight:600;color:#6B7280;font-size:0.6rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e5e7eb;">Importe</th>
            <th style="padding:0.4rem 0.6rem;border-bottom:1px solid #e5e7eb;"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($cotizacionesPendientes as $a)
          <tr style="border-top:1px solid #f3f4f6;">
            <td style="padding:0.4rem 0.6rem;white-space:nowrap;">{{ $a->fecha_solicitud?->format('d/m H:i') }}</td>
            <td style="padding:0.4rem 0.6rem;">
              <span style="font-weight:600;color:#0B1D3A;">{{ $a->titulo }}</span>
              <span style="display:block;font-size:0.7rem;color:#6B7280;">{{ $a->cita?->vehiculo?->placa ?? ($a->ordenTrabajo?->vehiculo?->placa ?? '—') }}</span>
            </td>
            <td style="padding:0.4rem 0.6rem;text-align:center;color:#6B7280;">{{ $a->tiempo_estimado_label }}</td>
            <td style="padding:0.4rem 0.6rem;text-align:right;font-weight:700;color:#D62828;">Bs {{ number_format($a->importe, 2) }}</td>
            <td style="padding:0.4rem 0.6rem;text-align:right;">
              <a href="{{ route('cliente.autorizaciones') }}" style="display:inline-block;padding:0.2rem 0.6rem;background:#D62828;color:#fff;text-decoration:none;font-size:0.7rem;font-weight:600;">Revisar</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- SIN ORDEN --}}
@if (! $ordenActiva)
<div class="seg-empty">
  <i class="bi bi-clipboard-data" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>
  <p style="font-weight:600;color:#0B1D3A;margin:0 0 0.25rem;">No tenés órdenes activas</p>
  <p style="margin:0 0 0.5rem;">Cuando tu vehículo esté en taller, ves su progreso acá.</p>
  @if ($cotizacionesPendientes->isEmpty())
  <p style="margin:0;"><a href="{{ route('cliente.citas.crear') }}" style="color:#D62828;font-weight:600;text-decoration:none;">Agendá una cita →</a></p>
  @endif
</div>
@else

<div style="max-width:1000px;margin:0 auto;">

  {{-- HEADER --}}
  <div class="seg-box-dark" style="padding:1.25rem 1.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
      <div>
        <div style="font-size:0.7rem;font-weight:600;opacity:0.6;text-transform:uppercase;letter-spacing:0.04em;">Orden de trabajo</div>
        <div style="font-size:1.5rem;font-weight:800;">{{ $ordenActiva->numero_orden }}</div>
      </div>
      @php
      $badge = match($ordenActiva->estado) {
        'finalizada_mecanico','lista_entrega' => ['bg' => '#2B9348', 'label' => 'Completado'],
        'recibida' => ['bg' => '#2563EB', 'label' => 'Recibido'],
        'diagnostico' => ['bg' => '#D97706', 'label' => 'En diagnóstico'],
        'en_proceso' => ['bg' => '#D62828', 'label' => 'En proceso'],
        'pausada' => ['bg' => '#6B7280', 'label' => 'Pausada'],
        default => ['bg' => '#6B7280', 'label' => ucfirst(str_replace('_',' ',$ordenActiva->estado))],
      };
      @endphp
      <span style="padding:0.25rem 0.75rem;background:{{ $badge['bg'] }};color:#fff;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.03em;">{{ $badge['label'] }}</span>
    </div>
  </div>

  {{-- INFO GRID --}}
  <div class="seg-box" style="border-top:0;">
    <div class="seg-grid">
      <div>
        <div class="seg-label">Vehículo</div>
        <div class="seg-value">{{ $ordenActiva->vehiculo?->marca ?? '' }} {{ $ordenActiva->vehiculo?->modelo ?? '' }} · {{ $ordenActiva->vehiculo?->placa ?? '—' }}</div>
      </div>
      <div>
        <div class="seg-label">Ingreso</div>
        <div class="seg-value">{{ $ordenActiva->fecha_emision?->format('d/m/Y H:i') }}</div>
      </div>
      <div style="grid-column:span 2;border-bottom:0;border-right:0;">
        <div class="seg-label">Problema reportado</div>
        <div class="seg-value" style="font-weight:500;">{{ $ordenActiva->descripcion_problema ?? '—' }}</div>
      </div>
    </div>
  </div>

  {{-- PROGRESS BAR --}}
  @php $pct = $asignacion?->porcentaje_avance ?? 0; @endphp
  @if (in_array($ordenActiva->estado, ['finalizada_mecanico', 'lista_entrega']))
  <div class="seg-box">
    <div style="padding:1rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
        <div><span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#2B9348;letter-spacing:0.04em;">Completado</span>
        <div style="font-weight:700;font-size:0.9rem;color:#0B1D3A;">Listo para retirar</div></div>
        <div class="seg-pct" style="color:#2B9348;">100%</div>
      </div>
      <div class="seg-pb-wrap"><div class="seg-pb-fill done" style="width:100%;"></div></div>
    </div>
  </div>
  @else
  <div class="seg-box">
    <div style="padding:1rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
        <div><span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:0.04em;">Progreso</span>
        <div style="font-weight:600;font-size:0.88rem;color:#0B1D3A;">{{ $badge['label'] }}</div></div>
        <div class="seg-pct" style="color:#D62828;">{{ $pct }}%</div>
      </div>
      <div class="seg-pb-wrap"><div class="seg-pb-fill" style="width:{{ $pct }}%;"></div></div>
      @if ($estimacion)
      <p style="margin:0.75rem 0 0;font-size:0.8rem;color:#6B7280;">
        Tiempo: <strong style="color:#0B1D3A;">{{ $estimacion->duracion_minima_minutos }}–{{ $estimacion->duracion_maxima_minutos }} min</strong>
        @if ($estimacion->observacion_cliente)<br><span style="font-style:italic;">{{ $estimacion->observacion_cliente }}</span>@endif
      </p>
      @endif
    </div>
  </div>
  @endif

  {{-- TWO COLUMNS --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

    {{-- LEFT COL --}}
    <div>

      {{-- DIAGNÓSTICO --}}
      @if ($diagnostico)
      <div class="seg-box">
        <div class="seg-section-title"><i class="bi bi-search-heart me-1"></i> Diagnóstico</div>
        <div style="padding:0.75rem 1rem;font-size:0.85rem;">
          @if ($diagnostico->problema_encontrado)
          <p style="margin:0 0 0.3rem;"><strong style="color:#0B1D3A;">Problema:</strong> {{ $diagnostico->problema_encontrado }}</p>
          @endif
          @if ($diagnostico->causa_probable)
          <p style="margin:0 0 0.3rem;"><strong style="color:#0B1D3A;">Causa:</strong> {{ $diagnostico->causa_probable }}</p>
          @endif
          @if ($diagnostico->recomendacion)
          <p style="margin:0 0 0.3rem;"><strong style="color:#0B1D3A;">Recomendación:</strong> {{ $diagnostico->recomendacion }}</p>
          @endif
          @if ($diagnostico->observacion_cliente)
          <div style="margin-top:0.5rem;padding:0.4rem 0.6rem;background:#FEF2F2;font-size:0.8rem;border:1px solid #FECACA;">
            <i class="bi bi-chat-quote" style="color:#D62828;margin-right:0.25rem;"></i>{{ $diagnostico->observacion_cliente }}
          </div>
          @endif
        </div>
      </div>
      @endif

      {{-- TIMELINE --}}
      @if ($avances->isNotEmpty())
      <div class="seg-box">
        <div class="seg-section-title"><i class="bi bi-clock-history me-1"></i> Reporte del mecánico</div>
        <div style="padding:1rem 1rem 0.5rem;">
          <div class="seg-tl" style="position:relative;">
            @foreach ($avances as $a)
            <div class="seg-tl-item">
              <div class="seg-tl-dot {{ in_array($ordenActiva->estado,['finalizada_mecanico','lista_entrega']) && $loop->last ? 'done' : '' }}"></div>
              <div style="font-weight:600;font-size:0.88rem;color:#0B1D3A;">{{ $a->titulo }}</div>
              <div style="font-size:0.7rem;color:#6B7280;margin-top:0.1rem;">{{ $a->created_at->format('d/m/Y H:i') }}</div>
              @if ($a->descripcion)
              <div style="font-size:0.82rem;color:#4B5563;margin-top:0.3rem;">{{ $a->descripcion }}</div>
              @endif
              @if ($a->nota_cliente)
              <div style="margin-top:0.3rem;padding:0.35rem 0.5rem;background:#FEF2F2;font-size:0.78rem;border:1px solid #FECACA;">
                <i class="bi bi-chat-quote" style="color:#D62828;margin-right:0.25rem;"></i>{{ $a->nota_cliente }}
              </div>
              @endif
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      {{-- NOTAS --}}
      @if ($asignacion && $asignacion->notasVisiblesCliente->isNotEmpty())
      <div class="seg-box">
        <div class="seg-section-title"><i class="bi bi-chat-dots me-1"></i> Notas del taller</div>
        @foreach ($asignacion->notasVisiblesCliente as $nota)
        <div style="padding:0.6rem 1rem;border-bottom:1px solid #f3f4f6;font-size:0.85rem;">
          <div style="font-size:0.65rem;color:#9CA3AF;margin-bottom:0.1rem;">{{ $nota->created_at->format('d/m/Y H:i') }}</div>
          {{ $nota->contenido }}
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- RIGHT COL --}}
    <div>

      {{-- SERVICIOS & REPUESTOS --}}
      @php
        $servItems = $ordenActiva->serviciosMecanico ?? collect();
        $repItems = $ordenActiva->repuestosMecanico ?? collect();
        $totalServ = $servItems->sum('precio_base');
        $totalRep = $repItems->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $totalGeneral = $totalServ + $totalRep;
      @endphp
      <div class="seg-box">
        <div class="seg-section-title"><i class="bi bi-receipt me-1"></i> Servicios y repuestos</div>
        @if ($servItems->isNotEmpty() || $repItems->isNotEmpty())
          @if ($servItems->isNotEmpty())
          <div style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#6B7280;padding:0.4rem 1rem 0.2rem;background:#f9fafb;">Servicios</div>
            @foreach ($servItems as $s)
            <div class="seg-price"><span>{{ $s->nombre_servicio }}</span><span style="font-weight:600;">Bs {{ number_format($s->precio_base, 2) }}</span></div>
            @endforeach
          @endif
          @if ($repItems->isNotEmpty())
          <div style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#6B7280;padding:0.4rem 1rem 0.2rem;background:#f9fafb;">Repuestos</div>
            @foreach ($repItems as $r)
            <div class="seg-price"><span>{{ $r->repuesto?->nombre ?? '#' . $r->repuesto_id }} <span style="color:#9CA3AF;">x{{ $r->cantidad }}</span></span><span style="font-weight:600;">Bs {{ number_format($r->cantidad * $r->precio_unitario_snapshot, 2) }}</span></div>
            @endforeach
          @endif
        @else
          <div style="padding:1.5rem;text-align:center;color:#9CA3AF;font-size:0.82rem;">
            <i class="bi bi-receipt" style="display:block;font-size:1.5rem;margin-bottom:0.5rem;opacity:0.3;"></i>
            Sin servicios ni repuestos
          </div>
        @endif

        {{-- TOTAL --}}
        @if ($totalGeneral > 0)
        <div style="border-top:2px solid #0B1D3A;padding:0.75rem 1rem;background:#f9fafb;">
          <div style="display:flex;justify-content:space-between;font-size:0.78rem;margin-bottom:0.25rem;">
            <span style="color:#6B7280;">Subtotal servicios</span><span>Bs {{ number_format($totalServ, 2) }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:0.78rem;margin-bottom:0.4rem;">
            <span style="color:#6B7280;">Subtotal repuestos</span><span>Bs {{ number_format($totalRep, 2) }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;">
            <span style="color:#0B1D3A;">Total</span><span style="color:#D62828;">Bs {{ number_format($totalGeneral, 2) }}</span>
          </div>
        </div>
        @endif
      </div>

      {{-- MECÁNICO --}}
      @if ($asignacion?->mecanico)
      <div class="seg-box">
        <div style="padding:0.75rem 1rem;display:flex;align-items:center;gap:0.75rem;">
          <div style="width:36px;height:36px;background:#0B1D3A;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;">{{ substr($asignacion->mecanico->empleado?->nombre_completo ?? 'M', 0, 1) }}</div>
          <div>
            <div class="seg-label">Mecánico asignado</div>
            <div style="font-weight:600;color:#0B1D3A;font-size:0.9rem;">{{ $asignacion->mecanico->empleado?->nombre_completo ?? '—' }}</div>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endif
@endsection
