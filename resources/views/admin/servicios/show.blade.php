@extends('layouts.admin')

@section('title', $servicio->nombre)
@section('navbar-title', $servicio->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.servicios.index') }}">Servicios</a></li>
    <li class="active" aria-current="page">{{ $servicio->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$servicio->nombre"
        :description="$servicio->tipoServicio->nombre ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (Auth::user()->tienePermiso('servicios.editar'))
            <a href="{{ route('admin.servicios.edit', $servicio) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <h2 class="h6 fw-bold mb-3">Datos</h2>
        <dl class="admin-meta">
            <dt>Nombre</dt><dd>{{ $servicio->nombre }}</dd>
            <dt>Tipo</dt><dd>{{ $servicio->tipoServicio->nombre ?? '—' }}</dd>
            <dt>Descripción</dt><dd>{{ $servicio->descripcion ?? '—' }}</dd>
            <dt>Precio base</dt><dd>{{ number_format((float) $servicio->precio_base, 2, ',', '.') }}</dd>
            <dt>Duración estimada</dt><dd>{{ $servicio->duracion_estimada_minutos ? $servicio->duracion_estimada_minutos . ' minutos' : '—' }}</dd>
            <dt>Estado</dt>
            <dd>
                <x-admin.status-badge
                    :tone="$servicio->estado ? 'success' : 'neutral'"
                    :icon="$servicio->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                    :label="$servicio->estado ? 'Activo' : 'Inactivo'" />
            </dd>
        </dl>
    </div>
@endsection
