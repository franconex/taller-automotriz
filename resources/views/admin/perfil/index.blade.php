@extends('layouts.admin')

@section('title', 'Mi perfil')
@section('navbar-title', 'Mi perfil')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Mi perfil</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Mi perfil"
        description="Información personal y credenciales de acceso." />

    @php $empleado = $usuario->empleado; @endphp

    <form method="POST" action="{{ route('admin.perfil.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            {{-- Foto --}}
            <div class="col-12 col-lg-4">
                <div class="admin-table-wrap p-4 text-center">
                    <h2 class="h6 fw-bold mb-3">Foto de perfil</h2>
                    <div class="mb-3">
                        @if ($empleado && $empleado->foto)
                            <img src="{{ asset('storage/' . $empleado->foto) }}"
                                 alt="Foto de {{ $empleado->nombre_completo }}"
                                 class="rounded-circle"
                                 style="width:150px;height:150px;object-fit:cover;">
                        @else
                            <span class="admin-avatar d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white fw-bold"
                                  style="width:150px;height:150px;font-size:3rem;">
                                {{ $usuario->nombre ? mb_strtoupper(mb_substr($usuario->nombre, 0, 1)) : 'U' }}
                            </span>
                        @endif
                    </div>
                    <x-admin.form-field
                        name="foto"
                        type="file"
                        label="Cambiar foto"
                        accept="image/jpeg,image/png,image/webp" />
                    @if ($empleado && $empleado->foto)
                        <div class="mt-2">
                            <label class="small text-muted">
                                <input type="checkbox" name="eliminar_foto" value="1">
                                Eliminar foto actual
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Datos --}}
            <div class="col-12 col-lg-8">
                <div class="admin-table-wrap p-4 mb-3">
                    <h2 class="h6 fw-bold mb-3">
                        <i class="bi bi-person-badge me-1" aria-hidden="true"></i>
                        Datos personales
                        <span class="cell-muted small fw-normal ms-2">(heredados del empleado)</span>
                    </h2>
                    <dl class="admin-meta">
                        <dt>Nombre completo</dt>
                        <dd>{{ $empleado->nombre_completo ?? $usuario->nombre }} <span class="cell-muted small">— Empleado</span></dd>
                        @if ($empleado)
                            <dt>CI</dt><dd>{{ $empleado->ci ?? '—' }}</dd>
                            <dt>Teléfono</dt><dd>{{ $empleado->telefono ?? '—' }}</dd>
                            <dt>Correo</dt><dd>{{ $empleado->email ?? $usuario->email }}</dd>
                            <dt>Dirección</dt><dd>{{ $empleado->direccion ?? '—' }}</dd>
                            <dt>Rol</dt><dd>{{ $empleado->rol->nombre ?? $usuario->rol->nombre ?? '—' }}</dd>
                            @if ($empleado->cargo)
                                <dt>Cargo</dt><dd>{{ $empleado->cargo }}</dd>
                            @endif
                            <dt>Sucursal</dt><dd>{{ $empleado->sucursal->nombre ?? '—' }}</dd>
                        @endif
                    </dl>
                    <p class="cell-muted small mb-0">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        Para modificar estos datos, ve a <a href="{{ route('admin.empleados.index') }}">Empleados</a>.
                    </p>
                </div>

                <div class="admin-table-wrap p-4">
                    <h2 class="h6 fw-bold mb-3">
                        <i class="bi bi-key me-1" aria-hidden="true"></i>
                        Credenciales de acceso
                    </h2>
                    <x-admin.form-field
                        name="username"
                        label="Nombre de usuario"
                        :value="$usuario->username"
                        required
                        icon="bi-at" />

                    <hr class="my-3">
                    <h3 class="h6 fw-bold mb-2">Cambiar contraseña</h3>
                    <p class="cell-muted small mb-3">Deja los campos en blanco si no deseas cambiarla.</p>
                    <x-admin.form-field
                        name="password"
                        type="password"
                        label="Nueva contraseña"
                        icon="bi-lock"
                        autocomplete="new-password" />
                    <x-admin.form-field
                        name="password_confirmation"
                        type="password"
                        label="Confirmar contraseña"
                        icon="bi-lock-fill"
                        autocomplete="new-password" />

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
