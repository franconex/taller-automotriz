@extends('layouts.cliente-sidebar')

@section('title', 'Mis vehículos')
@section('navbar-title', 'Mis vehículos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Vehículos registrados</p>
        <a href="{{ route('cliente.vehiculos.crear') }}" class="btn btn-sm text-white" style="background:#E31E24;">
            <i class="bi bi-plus-lg me-1"></i>Registrar vehículo
        </a>
    </div>

    @if ($vehiculos->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-car-front display-4 d-block mb-3"></i>
            <p>No tienes vehículos registrados.</p>
            <a href="{{ route('cliente.vehiculos.crear') }}" class="btn text-white" style="background:#E31E24;">Registrar tu primer vehículo</a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($vehiculos as $v)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $v->placa ?? '—' }}</h5>
                                <span class="badge bg-secondary">{{ $v->anio ?? '' }}</span>
                            </div>
                            <p class="card-text small text-muted mb-1">
                                {{ $v->marca ?? '' }} {{ $v->modelo ?? '' }}
                                @if ($v->color)
                                    · {{ $v->color }}
                                @endif
                            </p>
                            @if ($v->kilometraje_actual)
                                <p class="small mb-0"><strong>Kilometraje:</strong> {{ number_format($v->kilometraje_actual) }} km</p>
                            @endif
                            @if ($v->numero_chasis)
                                <p class="small mb-0"><strong>Chasis:</strong> {{ $v->numero_chasis }}</p>
                            @endif
                            @if ($v->observaciones)
                                <p class="small text-muted mt-2 mb-0">{{ $v->observaciones }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            @can('view', $v)
                            <a href="{{ route('cliente.vehiculo-show', $v) }}" class="btn btn-sm text-white" style="background:#E31E24;">
                                <i class="bi bi-eye me-1"></i>Ver detalle
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
