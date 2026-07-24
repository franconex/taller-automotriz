@extends('layouts.admin')

@section('title', $vehiculo->placa)
@section('navbar-title', $vehiculo->placa)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.vehiculos.index') }}">Vehículos</a></li>
    <li class="active" aria-current="page">{{ $vehiculo->placa }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$vehiculo->placa"
        :description="$vehiculo->modelo ? (optional($vehiculo->modelo->marca)->nombre ?? '') . ' ' . $vehiculo->modelo->nombre : ''">
        <x-slot:actions>
            <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.vehiculos.edit', $vehiculo) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos del vehículo</h2>
                <dl class="admin-meta">
                    <dt>Placa</dt><dd>{{ $vehiculo->placa }}</dd>
                    <dt>Marca</dt><dd>{{ optional(optional($vehiculo->modelo)->marca)->nombre ?? '—' }}</dd>
                    <dt>Modelo</dt><dd>{{ $vehiculo->modelo->nombre ?? '—' }}</dd>
                    <dt>Año</dt><dd>{{ $vehiculo->anio ?? '—' }}</dd>
                    <dt>Color</dt><dd>{{ $vehiculo->color ?? '—' }}</dd>
                    <dt>N° chasis</dt><dd>{{ $vehiculo->numero_chasis ?? '—' }}</dd>
                    <dt>Kilometraje</dt><dd>{{ number_format((int) $vehiculo->kilometraje_actual, 0, ',', '.') }} km</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Propietario y estado</h2>
                <dl class="admin-meta">
                    <dt>Cliente</dt>
                    <dd>
                        <a href="{{ route('admin.clientes.show', $vehiculo->cliente) }}">{{ $vehiculo->cliente->nombre_completo ?? '—' }}</a>
                    </dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$vehiculo->estado ? 'success' : 'neutral'"
                            :icon="$vehiculo->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$vehiculo->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                </dl>
                @if ($vehiculo->observaciones)
                    <h3 class="h6 fw-bold mt-3 mb-2">Observaciones</h3>
                    <p class="cell-muted small mb-0">{{ $vehiculo->observaciones }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
