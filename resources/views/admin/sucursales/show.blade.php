@extends('layouts.admin')

@section('title', $sucursal->nombre)
@section('navbar-title', $sucursal->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.sucursales.index') }}">Sucursales</a></li>
    <li class="active" aria-current="page">{{ $sucursal->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$sucursal->nombre"
        :description="$sucursal->direccion">
        <x-slot:actions>
            <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos generales</h2>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $sucursal->nombre }}</dd>
                    <dt>Dirección</dt><dd>{{ $sucursal->direccion }}</dd>
                    <dt>Teléfono</dt><dd>{{ $sucursal->telefono }}</dd>
                    <dt>Horario</dt><dd>{{ $sucursal->horario_atencion ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$sucursal->estado ? 'success' : 'neutral'"
                            :icon="$sucursal->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$sucursal->estado ? 'Activa' : 'Inactiva'" />
                    </dd>
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Resumen</h2>
                <dl class="admin-meta">
                    <dt>Empleados</dt><dd>{{ $sucursal->empleados->count() }}</dd>
                    <dt>Repuestos en inventario</dt><dd>{{ $sucursal->inventarios->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
