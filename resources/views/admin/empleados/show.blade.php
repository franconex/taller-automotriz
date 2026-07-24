@extends('layouts.admin')

@section('title', $empleado->nombre_completo)
@section('navbar-title', $empleado->nombre_completo)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.empleados.index') }}">Empleados</a></li>
    <li class="active" aria-current="page">{{ $empleado->nombre_completo }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$empleado->nombre_completo"
        :description="$empleado->cargo . ' — ' . ($empleado->sucursal->nombre ?? 'Sin sucursal')">
        <x-slot:actions>
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos personales</h2>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $empleado->nombre_completo }}</dd>
                    <dt>CI</dt><dd>{{ $empleado->ci }}</dd>
                    <dt>Teléfono</dt><dd>{{ $empleado->telefono }}</dd>
                    <dt>Email</dt><dd>{{ $empleado->email ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $empleado->direccion ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos laborales</h2>
                <dl class="admin-meta">
                    <dt>Cargo</dt><dd>{{ $empleado->cargo }}</dd>
                    <dt>Sucursal</dt><dd>{{ $empleado->sucursal->nombre ?? '—' }}</dd>
                    <dt>Fecha contratación</dt><dd>{{ $empleado->fecha_contratacion?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$empleado->estado ? 'success' : 'neutral'"
                            :icon="$empleado->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$empleado->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                    <dt>Cuenta de acceso</dt>
                    <dd>
                        @if ($empleado->user)
                            <span class="cell-strong">{{ $empleado->user->username }}</span>
                            <span class="d-block cell-muted small">{{ $empleado->user->rol->nombre ?? '—' }}</span>
                        @else
                            <span class="cell-muted">Sin cuenta</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
