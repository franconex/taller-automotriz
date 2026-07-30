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
        :description="($vehiculo->marca ?? '') . ' ' . ($vehiculo->modelo ?? '')">
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
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#0ea5e9;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-car-front"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos del vehículo</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Placa</dt><dd><strong>{{ $vehiculo->placa }}</strong></dd>
                    <dt>Marca</dt><dd>{{ $vehiculo->marca ?? '—' }}</dd>
                    <dt>Modelo</dt><dd>{{ $vehiculo->modelo ?? '—' }}</dd>
                    <dt>Año</dt><dd>{{ $vehiculo->anio ?? '—' }}</dd>
                    <dt>Color</dt>
                    <dd>
                        @if ($vehiculo->color)
                            <span class="badge rounded-pill" style="background:#e2e8f0;color:#475569;">{{ $vehiculo->color }}</span>
                        @else
                            —
                        @endif
                    </dd>
                    <dt>N° chasis</dt><dd class="font-monospace">{{ $vehiculo->numero_chasis ?? '—' }}</dd>
                    <dt>Kilometraje</dt><dd>{{ number_format((int) $vehiculo->kilometraje_actual, 0, ',', '.') }} km</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Propietario y estado</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Cliente</dt>
                    <dd>
                        @if ($vehiculo->cliente)
                            <a href="{{ route('admin.clientes.show', $vehiculo->cliente) }}" class="text-decoration-none fw-semibold">
                                <i class="bi bi-person-vcard me-1" style="color:#64748b;"></i>
                                {{ $vehiculo->cliente->nombre_completo ?? '—' }}
                            </a>
                        @else
                            —
                        @endif
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
                    <div class="mt-3 pt-3 border-top">
                        <div class="fw-semibold mb-1" style="font-size:0.85rem;">Observaciones</div>
                        <p class="cell-secondary small mb-0">{{ $vehiculo->observaciones }}</p>
                    </div>
                @endif
            </div>
            @if ($vehiculo->foto)
            <div class="admin-card-module mt-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-image"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Foto</h2>
                </div>
                <img src="{{ $vehiculo->foto }}" class="img-fluid rounded" style="max-height:200px;">
            </div>
            @endif
        </div>
    </div>
@endsection