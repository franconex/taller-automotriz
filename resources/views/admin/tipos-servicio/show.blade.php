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
            <a href="{{ route('admin.tipos-servicio.edit', $tipo) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos</h2>
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
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Servicios asociados</h2>
                @if ($tipo->servicios->isEmpty())
                    <p class="cell-muted small mb-0">No hay servicios asociados a este tipo.</p>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach ($tipo->servicios as $servicio)
                            <li class="py-1">
                                <a href="{{ route('admin.servicios.show', $servicio) }}">{{ $servicio->nombre }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
