@extends('layouts.admin')

@section('title', $cliente->nombre_completo)
@section('navbar-title', $cliente->nombre_completo)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
    <li class="active" aria-current="page">{{ $cliente->nombre_completo }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$cliente->nombre_completo"
        :description="$cliente->telefono">
        <x-slot:actions>
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de contacto</h2>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $cliente->nombre_completo }}</dd>
                    <dt>CI</dt><dd>{{ $cliente->ci ?? '—' }}</dd>
                    <dt>Teléfono</dt><dd>{{ $cliente->telefono }}</dd>
                    <dt>Email</dt><dd>{{ $cliente->email ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $cliente->direccion ?? '—' }}</dd>
                    <dt>Fecha de registro</dt><dd>{{ $cliente->fecha_registro?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$cliente->estado ? 'success' : 'neutral'"
                            :icon="$cliente->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$cliente->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Resumen</h2>
                <dl class="admin-meta">
                    <dt>Vehículos</dt><dd>{{ $cliente->vehiculos->count() }}</dd>
                    <dt>Citas</dt><dd>{{ $cliente->citas->count() }}</dd>
                    <dt>Órdenes de trabajo</dt><dd>{{ $cliente->ordenesTrabajo->count() }}</dd>
                </dl>
                @if ($cliente->vehiculos->isNotEmpty())
                    <h3 class="h6 fw-bold mt-3 mb-2">Vehículos</h3>
                    <ul class="list-unstyled mb-0">
                        @foreach ($cliente->vehiculos as $vehiculo)
                            <li>
                                <a href="{{ route('admin.vehiculos.show', $vehiculo) }}">{{ $vehiculo->placa }}</a>
                                <span class="cell-muted small">
                                    — {{ optional($vehiculo->modelo->marca)->nombre }} {{ $vehiculo->modelo->nombre ?? '' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
