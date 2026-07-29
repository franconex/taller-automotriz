@extends('layouts.admin')

@section('title', $usuario->empleado->nombre_completo ?? $usuario->nombre)
@section('navbar-title', $usuario->empleado->nombre_completo ?? $usuario->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
    <li class="active" aria-current="page">{{ $usuario->empleado->nombre_completo ?? $usuario->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$usuario->empleado->nombre_completo ?? $usuario->nombre"
        :description="$usuario->username . ' — ' . ($usuario->rol->nombre ?? '—')">
        <x-slot:actions>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la cuenta</h2>
                <dl class="admin-meta">
                    <dt>Usuario</dt><dd>{{ $usuario->username }}</dd>
                    <dt>Rol</dt><dd>{{ $usuario->rol->nombre ?? '—' }}</dd>
                    <dt>Sucursal</dt><dd>{{ $usuario->sucursal->nombre ?? '—' }}</dd>
                    <dt>Último acceso</dt>
                    <dd>{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($usuario->estado) {
                                'activo' => 'success',
                                'vacaciones' => 'warning',
                                default => 'neutral',
                            }"
                            :icon="match($usuario->estado) {
                                'activo' => 'bi-check-circle-fill',
                                'vacaciones' => 'bi-sun-fill',
                                default => 'bi-pause-circle-fill',
                            }"
                            :label="match($usuario->estado) {
                                'activo' => 'Activo',
                                'vacaciones' => 'Vacaciones',
                                default => 'Inactivo',
                            }" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Empleado vinculado</h2>
                @if ($usuario->empleado)
                    <dl class="admin-meta">
                        <dt>Nombre</dt>
                        <dd>
                            <a href="{{ route('admin.empleados.show', $usuario->empleado) }}">
                                {{ $usuario->empleado->nombre_completo }}
                            </a>
                        </dd>
                        <dt>Email</dt><dd>{{ $usuario->empleado->email ?? '—' }}</dd>
                        <dt>Teléfono</dt><dd>{{ $usuario->empleado->telefono ?? '—' }}</dd>
                        <dt>CI</dt><dd>{{ $usuario->empleado->ci ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="cell-muted small mb-0">Este usuario no tiene un empleado vinculado.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Restablecer contraseña</h2>
                <form method="POST" action="{{ route('admin.usuarios.restablecer-password', $usuario) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto" style="min-width:220px;">
                        <x-admin.form-field
                            name="password"
                            type="password"
                            label="Nueva contraseña"
                            required
                            icon="bi-lock"
                            autocomplete="new-password" />
                    </div>
                    <div class="col-auto" style="min-width:220px;">
                        <x-admin.form-field
                            name="password_confirmation"
                            type="password"
                            label="Confirmar contraseña"
                            required
                            icon="bi-lock-fill"
                            autocomplete="new-password" />
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-key" aria-hidden="true"></i>
                            Restablecer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
