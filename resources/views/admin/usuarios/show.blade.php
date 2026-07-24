@extends('layouts.admin')

@section('title', $usuario->nombre)
@section('navbar-title', $usuario->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
    <li class="active" aria-current="page">{{ $usuario->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$usuario->nombre"
        :description="$usuario->email">
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
                    <dt>Nombre</dt><dd>{{ $usuario->nombre }}</dd>
                    <dt>Username</dt><dd>{{ $usuario->username }}</dd>
                    <dt>Email</dt><dd>{{ $usuario->email }}</dd>
                    <dt>Rol</dt><dd>{{ $usuario->rol->nombre ?? '—' }}</dd>
                    <dt>Sucursal</dt><dd>{{ $usuario->sucursal->nombre ?? '—' }}</dd>
                    <dt>Empleado</dt>
                    <dd>
                        @if ($usuario->empleado)
                            <a href="{{ route('admin.empleados.show', $usuario->empleado) }}">{{ $usuario->empleado->nombre_completo }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dt>Último acceso</dt>
                    <dd>{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$usuario->estado === 'activo' ? 'success' : 'neutral'"
                            :icon="$usuario->estado === 'activo' ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="ucfirst($usuario->estado)" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Restablecer contraseña</h2>
                <form method="POST" action="{{ route('admin.usuarios.restablecer-password', $usuario) }}">
                    @csrf
                    <x-admin.form-field
                        name="password"
                        type="password"
                        label="Nueva contraseña"
                        required
                        icon="bi-lock"
                        autocomplete="new-password" />
                    <x-admin.form-field
                        name="password_confirmation"
                        type="password"
                        label="Confirmar contraseña"
                        required
                        icon="bi-lock-fill"
                        autocomplete="new-password" />
                    <div class="d-flex justify-content-end">
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
