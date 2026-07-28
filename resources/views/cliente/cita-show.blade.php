@extends('layouts.cliente-sidebar')

@section('title', 'Cita')
@section('navbar-title', 'Detalle de cita')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.citas') }}" class="text-decoration-none small">&larr; Volver a mis citas</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Cita — {{ $cita->fecha?->format('d/m/Y') }}</h5>
            <span class="badge fs-6 bg-{{ $cita->estado === 'confirmada' ? 'success' : ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                {{ ucfirst(str_replace('_', ' ', $cita->estado)) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Hora</h6>
                    <p>{{ $cita->hora?->format('H:i') }} — {{ $cita->hora_fin?->format('H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Vehículo</h6>
                    <p>{{ $cita->vehiculo?->placa ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Servicio</h6>
                    <p>{{ $cita->servicio?->nombre ?? $cita->tipo ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Sucursal</h6>
                    <p>{{ $cita->sucursal?->nombre ?? '—' }}</p>
                </div>
                @if ($cita->descripcion_problema)
                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase">Problema reportado</h6>
                        <p>{{ $cita->descripcion_problema }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
