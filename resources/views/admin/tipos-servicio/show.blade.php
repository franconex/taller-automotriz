@extends('layouts.admin')

@section('title', $tipo->nombre)
@section('navbar-title', $tipo->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.tipos-servicio.index') }}">Tipos de servicio</a></li>
    <li class="active" aria-current="page">{{ $tipo->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$tipo->nombre"
        :description="$tipo->descripcion ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.tipos-servicio.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (Auth::user()->tienePermiso('tipos-servicio.editar'))
            <a href="{{ route('admin.tipos-servicio.edit', $tipo) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-tags"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $tipo->nombre }}</dd>
                    <dt>Descripción</dt><dd>{{ $tipo->descripcion ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$tipo->estado ? 'success' : 'neutral'"
                            :icon="$tipo->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$tipo->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-gear"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Servicios asociados</h2>
                </div>
                @if ($tipo->servicios->isEmpty())
                    <p class="cell-secondary small mb-0">No hay servicios asociados a este tipo.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($tipo->servicios as $servicio)
                            <a href="{{ route('admin.servicios.show', $servicio) }}" class="list-group-item list-group-item-action px-3 py-2 d-flex justify-content-between align-items-center" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;">
                                <span>
                                    <i class="bi bi-gear me-1" style="color:#64748b;"></i>
                                    <strong>{{ $servicio->nombre }}</strong>
                                    <span class="cell-secondary small">— Bs. {{ number_format((float) $servicio->precio_base, 2, ',', '.') }}</span>
                                </span>
                                <i class="bi bi-chevron-right" style="color:#94a3b8;font-size:0.8rem;"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection