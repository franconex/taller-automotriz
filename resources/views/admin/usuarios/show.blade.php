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
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos de la cuenta</h2>
                </div>
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
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person-lines-fill"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Empleado vinculado</h2>
                </div>
                @if ($usuario->empleado)
                    <dl class="admin-meta">
                        <dt>Nombre</dt>
                        <dd>
                            <a href="{{ route('admin.empleados.show', $usuario->empleado) }}" class="text-decoration-none fw-semibold">
                                {{ $usuario->empleado->nombre_completo }}
                            </a>
                        </dd>
                        <dt>Correo</dt><dd>{{ $usuario->empleado->email ?? '—' }}</dd>
                        <dt>Teléfono</dt><dd>{{ $usuario->empleado->telefono ?? '—' }}</dd>
                        <dt>CI</dt><dd>{{ $usuario->empleado->ci ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="cell-muted small mb-0">Este usuario no tiene un empleado vinculado.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-card-module mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                <i class="bi bi-key"></i>
            </span>
            <h2 class="fw-bold mb-0" style="font-size:1rem;">Restablecer contraseña</h2>
        </div>
        <form method="POST" action="{{ route('admin.usuarios.restablecer-password', $usuario) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-auto" style="min-width:220px;">
                <label for="field-password" class="form-label fw-medium small">Nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-lock" style="color:#64748b;"></i>
                    </span>
                    <input id="field-password" type="password" name="password" required
                           class="form-control" style="border-left:0;" autocomplete="new-password">
                </div>
            </div>
            <div class="col-auto" style="min-width:220px;">
                <label for="field-password_confirmation" class="form-label fw-medium small">Confirmar contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-lock-fill" style="color:#64748b;"></i>
                    </span>
                    <input id="field-password_confirmation" type="password" name="password_confirmation" required
                           class="form-control" style="border-left:0;" autocomplete="new-password">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-warning btn-sm px-3">
                    <i class="bi bi-key" aria-hidden="true"></i>
                    Restablecer
                </button>
            </div>
        </form>
    </div>
@endsection