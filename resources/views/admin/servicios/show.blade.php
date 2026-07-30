@extends('layouts.admin')

@section('title', $servicio->nombre)
@section('navbar-title', $servicio->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.servicios.index') }}">Servicios</a></li>
    <li class="active" aria-current="page">{{ $servicio->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header :title="$servicio->nombre" :description="$servicio->tipoServicio->nombre ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
            @if (Auth::user()->tienePermiso('servicios.editar'))
            <a href="{{ route('admin.servicios.edit', $servicio) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Editar</a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#0ea5e9;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-gear"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos del servicio</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $servicio->nombre }}</dd>
                    <dt>Tipo</dt><dd><span class="badge rounded-pill" style="background:#e8f4fd;color:#2563eb;font-weight:500;">{{ $servicio->tipoServicio->nombre ?? '—' }}</span></dd>
                    <dt>Descripción</dt><dd>{{ $servicio->descripcion ?? '—' }}</dd>
                    <dt>Precio base</dt><dd class="fw-bold" style="font-size:1.1rem;">Bs. {{ number_format((float) $servicio->precio_base, 2, ',', '.') }}</dd>
                    <dt>Duración estimada</dt><dd>{{ $servicio->duracion_estimada_minutos ? $servicio->duracion_estimada_minutos . ' minutos' : '—' }}</dd>
                    <dt>Estado</dt>
                    <dd><x-admin.status-badge :tone="$servicio->estado ? 'success' : 'neutral'" :icon="$servicio->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'" :label="$servicio->estado ? 'Activo' : 'Inactivo'" /></dd>
                </dl>
            </div>
        </div>
    </div>
@endsection