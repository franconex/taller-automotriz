@extends('layouts.admin')

@section('title', 'Citas Asignadas')
@section('navbar-title', 'Citas Asignadas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing:.5px;">
        <i class="bi bi-calendar-check me-2"></i>Mis Citas Asignadas
    </h5>
    <span class="badge rounded-0 px-3 py-1" style="background:#0B1D3A;font-size:.75rem;">{{ $citas->total() }} citas</span>
</div>

@if ($citas->isEmpty())
    <div class="d-flex flex-column align-items-center justify-content-center p-5" style="border:1px solid #dee2e6;background:#fafafa;">
        <i class="bi bi-calendar2-week" style="font-size:2.5rem;color:#ccc;"></i>
        <p class="fw-semibold mt-3 mb-0">Sin citas asignadas</p>
        <p class="small text-muted mb-0">Cuando te asignen una cita, aparecerá aquí.</p>
    </div>
@else
    <div style="border:1px solid #dee2e6;">
        @foreach ($citas as $c)
            @php
                $estadoColor = match($c->estado) {
                    'confirmada' => '#16A34A',
                    'atendida' => '#0891B2',
                    'cancelada' => '#9CA3AF',
                    'no_asistio' => '#B91C1C',
                    default => '#6B7280',
                };
                $estadoBg = match($c->estado) {
                    'confirmada' => '#e6f7ed',
                    'atendida' => '#e6f3fa',
                    'cancelada' => '#f0f0f0',
                    default => '#f5f5f5',
                };
            @endphp
            <div class="d-flex align-items-stretch" style="border-bottom:1px solid #eee;">
                <div style="width:5px;flex-shrink:0;background:{{ $estadoColor }};"></div>
                <div class="flex-fill p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width:70px;flex-shrink:0;text-align:center;">
                            <div class="fw-bold" style="font-size:1.1rem;">{{ \Carbon\Carbon::parse($c->fecha)->format('d/m') }}</div>
                            <div style="font-size:.65rem;color:#6c757d;">{{ \Carbon\Carbon::parse($c->hora)->format('H:i') }}</div>
                        </div>
                        <div class="flex-fill min-w-0">
                            <div class="fw-semibold">{{ $c->cliente?->nombre_completo ?? '—' }}</div>
                            <div class="small text-muted">
                                {{ $c->vehiculo?->marca ?? '' }} {{ $c->vehiculo?->modelo ?? '' }} 
                                <span style="background:#f0f0f0;padding:0 4px;">{{ $c->vehiculo?->placa ?? '—' }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-wrench me-1" style="font-size:.65rem;"></i>{{ $c->servicio?->nombre ?? $c->tipo ?? '—' }}
                                @if ($c->descripcion_problema)
                                    &middot; <span class="text-truncate d-inline-block" style="max-width:200px;vertical-align:middle;">{{ $c->descripcion_problema }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                            <span class="small fw-semibold px-2 py-1 text-uppercase" style="font-size:.6rem;letter-spacing:.5px;background:{{ $estadoBg }};color:{{ $estadoColor }};border-radius:2px;">
                                {{ $c->estado_label }}
                            </span>
                            <div class="d-flex gap-1 mt-1">
                                @if ($c->estado === 'confirmada')
                                    <a href="{{ route('mecanico.cotizacion.create', $c) }}" class="btn btn-sm px-2" style="border:1px solid #d4a017;border-radius:3px;color:#d4a017;font-size:.7rem;">
                                        <i class="bi bi-search-heart"></i> Diagnosticar
                                    </a>
                                    @if ($c->autorizaciones_count > 0)
                                        <form method="POST" action="{{ route('mecanico.citas.iniciar', $c) }}" class="d-inline" onsubmit="return confirm('¿Iniciar trabajo? Se creará la orden.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm px-2" style="border:1px solid #16A34A;border-radius:3px;color:#16A34A;font-size:.7rem;">
                                                <i class="bi bi-play-fill"></i> Iniciar
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if ($c->ordenTrabajo)
                                    <a href="{{ route('mecanico.ordenes.show', $c->ordenTrabajo) }}" class="btn btn-sm px-2" style="border:1px solid #0B1D3A;border-radius:3px;color:#0B1D3A;font-size:.7rem;">
                                        <i class="bi bi-eye"></i> Orden
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $citas->links() }}
    </div>
@endif
@endsection
