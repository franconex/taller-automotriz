@extends('layouts.admin')

@section('title', 'Mi Panel')
@section('navbar-title', 'Panel de Trabajo')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            <h5 class="fw-bold mb-0 text-uppercase tracking-wide" style="letter-spacing:.5px;">
                <i class="bi bi-grid-1x2-fill me-2"></i>Bienvenido, {{ Auth::user()->nombre }}
            </h5>
            <a href="{{ route('mecanico.ordenes.index') }}" class="btn btn-sm ms-auto px-3" style="border:1px solid var(--tp-border);border-radius:4px;font-size:.8rem;">
                <i class="bi bi-journal-text me-1"></i> Ver todas
                @if ($counts['total_activas'] > 0)
                    <span class="badge bg-dark ms-1 rounded-0">{{ $counts['total_activas'] }}</span>
                @endif
            </a>
        </div>
    </div>

    @php
        $tarjetas = [
            ['label' => 'EN PROCESO',  'key' => 'en_proceso',  'bg' => '#e87a1a', 'icon' => 'bi-gear-wide-connected'],
            ['label' => 'DIAGNÓSTICO','key' => 'diagnostico', 'bg' => '#d4a017', 'icon' => 'bi-search-heart'],
            ['label' => 'ESP. REPUESTO','key' => 'esperando_repuesto', 'bg' => '#5a3d8a', 'icon' => 'bi-box-seam'],
            ['label' => 'PAUSADAS',    'key' => 'pausada',     'bg' => '#4a4a4a', 'icon' => 'bi-pause-circle'],
            ['label' => 'PEND. AUTORIZACIÓN', 'key' => 'pendiente_autorizacion', 'bg' => '#b33a3a', 'icon' => 'bi-file-earmark-text'],
            ['label' => 'FINALIZADAS HOY', 'key' => 'finalizadas_hoy', 'bg' => '#2b7a4a', 'icon' => 'bi-check-circle'],
        ];
    @endphp
    @foreach ($tarjetas as $t)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="d-flex flex-column justify-content-center p-3 h-100" style="border:1px solid #dee2e6;border-radius:4px;background:#fff;">
                <div class="fw-bold mb-1" style="font-size:2rem;color:{{ $t['bg'] }};line-height:1;">{{ $counts[$t['key']] ?? 0 }}</div>
                <div class="d-flex align-items-center gap-1 small text-uppercase tracking-wide" style="color:#6c757d;letter-spacing:.3px;font-size:.65rem;">
                    <i class="{{ $t['icon'] }}" style="font-size:.7rem;"></i>
                    {{ $t['label'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-2 mb-2 px-1">
            <i class="bi bi-tools" style="font-size:.9rem;"></i>
            <span class="fw-semibold text-uppercase" style="font-size:.8rem;letter-spacing:.3px;">Trabajos activos</span>
            <span class="ms-auto small text-muted">{{ $activas->count() }} asignados</span>
        </div>

        @if ($activas->isEmpty())
            <div class="d-flex flex-column align-items-center justify-content-center p-5" style="border:1px solid #dee2e6;border-radius:4px;background:#fafafa;">
                <i class="bi bi-inbox" style="font-size:2.5rem;color:#ccc;"></i>
                <p class="fw-semibold mt-3 mb-0">Sin trabajos activos</p>
                <p class="small text-muted mb-0">No tenés órdenes asignadas en este momento.</p>
                @if ($ordenesDisponibles->isNotEmpty())
                    <p class="small text-success mt-2 mb-0"><i class="bi bi-arrow-down me-1"></i>Hay {{ $ordenesDisponibles->count() }} órdenes disponibles abajo.</p>
                @endif
            </div>
        @else
            @foreach ($activas as $a)
                @php $o = $a->ordenTrabajo; @endphp
                <div class="d-flex align-items-stretch mb-2" style="border:1px solid #dee2e6;border-radius:4px;background:#fff;">
                    <div style="width:5px;flex-shrink:0;background:{{ match($o->estado) { 'en_proceso'=>'#e87a1a', 'diagnostico'=>'#d4a017', 'esperando_repuesto'=>'#5a3d8a', 'pausada'=>'#4a4a4a', 'pendiente_autorizacion'=>'#b33a3a', 'recibida'=>'#1a7ab3', default=>'#6c757d' } }};"></div>
                    <div class="flex-fill p-3">
                        <div class="d-flex align-items-start gap-2">
                            <div class="flex-fill min-w-0">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold" style="font-size:.9rem;">{{ $o->numero_orden }}</span>
                                    <span class="small text-muted">·</span>
                                    <span class="small text-muted text-truncate">{{ $o->cliente?->nombre_completo ?? '—' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="small text-muted"><i class="bi bi-car-front me-1" style="font-size:.65rem;"></i>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</span>
                                    <span class="small text-muted">·</span>
                                    <span class="small fw-semibold" style="background:#f0f0f0;padding:1px 6px;font-size:.7rem;border-radius:2px;">{{ $o->vehiculo?->placa ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="small fw-semibold px-2 py-1 text-uppercase tracking-wide" style="font-size:.6rem;letter-spacing:.5px;background:{{ match($o->estado) { 'en_proceso'=>'#fff0e0', 'diagnostico'=>'#fff6d9', 'esperando_repuesto'=>'#efe6ff', 'pausada'=>'#f0f0f0', 'pendiente_autorizacion'=>'#ffe6e6', default=>'#f0f0f0' } }};color:{{ match($o->estado) { 'en_proceso'=>'#e87a1a', 'diagnostico'=>'#b8860b', 'esperando_repuesto'=>'#5a3d8a', 'pausada'=>'#4a4a4a', 'pendiente_autorizacion'=>'#b33a3a', default=>'#6c757d' } }};border-radius:2px;">
                                    {{ str_replace('_', ' ', $o->estado) }}
                                </span>
                                <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm px-2" style="border:1px solid #dee2e6;border-radius:3px;font-size:.75rem;">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="col-lg-5">
        @if ($ordenesDisponibles->isNotEmpty())
            <div class="d-flex align-items-center gap-2 mb-2 px-1">
                <i class="bi bi-box-arrow-in-down text-success" style="font-size:.9rem;"></i>
                <span class="fw-semibold text-uppercase" style="font-size:.8rem;letter-spacing:.3px;">Disponibles</span>
                <span class="ms-auto badge rounded-0 px-2 py-1 small" style="background:#2b7a4a;font-size:.65rem;">{{ $ordenesDisponibles->count() }}</span>
            </div>
            @foreach ($ordenesDisponibles->take(5) as $o)
                <div class="d-flex align-items-center p-2 mb-1" style="border:1px solid #dee2e6;border-radius:3px;background:#fafafa;">
                    <div class="flex-fill min-w-0">
                        <div class="fw-semibold small">{{ $o->numero_orden }} <span class="text-muted fw-normal">· {{ $o->cliente?->nombre_completo ?? '—' }}</span></div>
                        <div class="small text-muted" style="font-size:.7rem;">{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }} <span class="fw-semibold">· {{ $o->vehiculo?->placa ?? '—' }}</span></div>
                    </div>
                    <form method="POST" action="{{ route('mecanico.ordenes.tomar', $o) }}" class="d-inline ms-2" onsubmit="return confirm('¿Tomar esta orden?');">
                        @csrf
                        <button type="submit" class="btn btn-sm px-2" style="border:1px solid #2b7a4a;border-radius:3px;color:#2b7a4a;font-size:.75rem;">
                            <i class="bi bi-hand-index-thumb"></i>
                        </button>
                    </form>
                </div>
            @endforeach
            @if ($ordenesDisponibles->count() > 5)
                <div class="text-center small text-muted mt-1">+{{ $ordenesDisponibles->count() - 5 }} más</div>
            @endif
        @endif

        @php
            $hoy = now()->format('Y-m-d');
            $citasHoy = \App\Models\Cita::where('mecanico_id', $mecanicoId??0)
                ->whereDate('fecha', $hoy)
                ->whereIn('estado', ['confirmada', 'pendiente'])
                ->with(['cliente:id,nombre_completo,telefono', 'vehiculo:id,placa,marca,modelo'])
                ->orderBy('hora')
                ->get();
        @endphp

        @if ($citasHoy->isNotEmpty())
            <div class="d-flex align-items-center gap-2 mt-3 mb-2 px-1">
                <i class="bi bi-calendar-check text-primary" style="font-size:.9rem;"></i>
                <span class="fw-semibold text-uppercase" style="font-size:.8rem;letter-spacing:.3px;">Citas de hoy</span>
                <span class="ms-auto badge rounded-0 px-2 py-1 small" style="background:#1a7ab3;font-size:.65rem;">{{ $citasHoy->count() }}</span>
            </div>
            @foreach ($citasHoy as $c)
                <div class="d-flex align-items-center p-2 mb-1" style="border:1px solid #dee2e6;border-radius:3px;background:#fff;">
                    <div style="width:40px;flex-shrink:0;text-align:center;">
                        <div class="fw-bold" style="font-size:.85rem;">{{ \Carbon\Carbon::parse($c->hora)->format('H:i') }}</div>
                    </div>
                    <div class="flex-fill min-w-0 px-2">
                        <div class="fw-semibold small">{{ $c->cliente?->nombre_completo ?? '—' }}</div>
                        <div class="small text-muted" style="font-size:.7rem;">{{ $c->vehiculo?->marca ?? '' }} {{ $c->vehiculo?->modelo ?? '' }} · {{ $c->vehiculo?->placa ?? '—' }}</div>
                    </div>
                    <a href="tel:{{ $c->cliente?->telefono }}" class="btn btn-sm px-2" style="border:1px solid #dee2e6;border-radius:3px;font-size:.75rem;">
                        <i class="bi bi-telephone"></i>
                    </a>
                </div>
            @endforeach
        @endif

        @if ($finalizadasHoy->isNotEmpty())
            <div class="d-flex align-items-center gap-2 mt-3 mb-2 px-1">
                <i class="bi bi-check-circle text-success" style="font-size:.9rem;"></i>
                <span class="fw-semibold text-uppercase" style="font-size:.8rem;letter-spacing:.3px;">Finalizados hoy</span>
                <span class="ms-auto badge rounded-0 px-2 py-1 small" style="background:#2b7a4a;font-size:.65rem;">{{ $finalizadasHoy->count() }}</span>
            </div>
            @foreach ($finalizadasHoy as $a)
                @php $o = $a->ordenTrabajo; @endphp
                <div class="d-flex align-items-center p-2 mb-1" style="border:1px solid #dee2e6;border-radius:3px;background:#f9fdf9;">
                    <div class="flex-fill min-w-0">
                        <span class="fw-semibold small">{{ $o->numero_orden }}</span>
                        <span class="text-muted small"> · {{ $o->cliente?->nombre_completo ?? '—' }}</span>
                        <div class="small text-muted" style="font-size:.7rem;">{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }} · {{ $o->vehiculo?->placa ?? '—' }}</div>
                    </div>
                    <span class="small text-muted" style="font-size:.65rem;">{{ $a->fecha_finalizacion?->format('H:i') }}</span>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
