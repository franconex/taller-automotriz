@extends('layouts.cliente-sidebar')

@section('title', $vehiculo->placa ?? 'Vehículo')
@section('navbar-title', $vehiculo->placa ?? 'Vehículo')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.vehiculos') }}" class="text-decoration-none small">&larr; Volver a mis vehículos</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">{{ $vehiculo->placa ?? 'Sin placa' }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Marca</h6>
                    <p class="fw-semibold">{{ $vehiculo->marca ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Modelo</h6>
                    <p class="fw-semibold">{{ $vehiculo->modelo ?? ($vehiculo->modelo?->nombre ?? '—') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Año</h6>
                    <p>{{ $vehiculo->anio ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Color</h6>
                    <p>{{ $vehiculo->color ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Kilometraje</h6>
                    <p>{{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Chasis</h6>
                    <p>{{ $vehiculo->numero_chasis ?? '—' }}</p>
                </div>
                @if ($vehiculo->observaciones)
                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase">Observaciones</h6>
                        <p>{{ $vehiculo->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
