@extends('layouts.cliente-sidebar')

@section('title', 'Mi Portal')

@section('content')
    @php
        $cliente = Auth::user()->cliente;
    @endphp

    <div class="row mb-4">
        <div class="col">
            <h1 class="h4 mb-1">Hola, {{ $cliente?->nombre_completo ?? Auth::user()->nombre }}</h1>
            <p class="text-muted mb-0">Panel de seguimiento de tus servicios automotrices.</p>
        </div>
    </div>

    {{-- TARJETA PRINCIPAL: VEHÍCULO EN TALLER --}}
    @if ($ordenActiva)
        <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #E31E24 !important;">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <h5 class="fw-bold mb-1">{{ $ordenActiva->vehiculo?->marca ?? '' }} {{ $ordenActiva->vehiculo?->modelo ?? '' }} — {{ $ordenActiva->vehiculo?->placa ?? '' }}</h5>
                        <p class="text-muted small mb-2">Estado: <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $ordenActiva->estado)) }}</span></p>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="small text-muted">Avance:</span>
                            <div class="progress flex-grow-1" style="height:10px;">
                                <div class="progress-bar" style="width:{{ $avance }}%;background:#E31E24;" role="progressbar"></div>
                            </div>
                            <span class="fw-bold small">{{ $avance }}%</span>
                        </div>
                        @if ($ultimaActualizacion)
                            <small class="text-muted">Última actualización: {{ $ultimaActualizacion->format('d/m/Y H:i') }}</small>
                        @endif
                    </div>
                    <div class="col-md-5 text-md-end">
                        <a href="{{ route('cliente.seguimiento') }}" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-eye me-1"></i>Ver seguimiento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TARJETAS SECUNDARIAS --}}
    <div class="row g-3 mb-4">
        @if ($proximaCita)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase mb-2">Próxima cita</h6>
                        <p class="fw-semibold mb-1">{{ \Carbon\Carbon::parse($proximaCita->fecha)->format('d/m/Y') }} a las {{ $proximaCita->hora ? \Carbon\Carbon::parse($proximaCita->hora)->format('H:i') : '—' }}</p>
                        <small class="text-muted">{{ $proximaCita->vehiculo?->placa }} · {{ $proximaCita->sucursal?->nombre }}</small>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="h4 mb-0 fw-bold">{{ $vehiculos->count() }}</div>
                <small class="text-muted">Vehículos</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="h4 mb-0 fw-bold">{{ $enTaller }}</div>
                <small class="text-muted">En taller</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="h4 mb-0 fw-bold">
                    @if ($saldoPendiente > 0) ${{ number_format($saldoPendiente, 2) }} @else $0.00 @endif
                </div>
                <small class="text-muted">Saldo pendiente</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('cliente.autorizaciones') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="h4 mb-0 fw-bold" style="color:{{ $autorizacionesPendientes > 0 ? '#E31E24' : 'inherit' }}">{{ $autorizacionesPendientes }}</div>
                    <small class="text-muted">Autorizaciones {!! $autorizacionesPendientes > 0 ? '<span class="badge bg-danger ms-1">pendientes</span>' : '' !!}</small>
                </div>
            </a>
        </div>
    </div>

    @if ($vehiculos->isEmpty() && ! $ordenActiva && ! $proximaCita)
        <div class="text-center text-muted py-5">
            <i class="bi bi-car-front display-4 d-block mb-3" style="color:#E31E24;"></i>
            <p class="mb-1">Aún no tienes vehículos registrados.</p>
            <p class="small">Contacta al taller para registrar tu primer vehículo.</p>
        </div>
    @endif
@endsection
