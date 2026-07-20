@extends('layouts.admin')

@section('title', 'Mi Perfil')

@section('page-title', 'Mi Perfil')

@section('content')
    <div class="max-w-xl">
        @if (session('success'))
            <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
        @endif

        <form method="POST" action="{{ route('admin.perfil.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información personal</h3>
                <x-admin.form-input name="nombre" label="Nombre completo" :value="$usuario->nombre" :required="true" />
                <div class="mt-5">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :value="$usuario->email" :required="true" />
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Rol</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $usuario->rol->nombre }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Último acceso</dt>
                        <dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}</dd>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Cambiar contraseña</h3>
                <p class="text-sm mb-4" style="color: var(--color-muted);">Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>
                <x-admin.form-input name="current_password" type="password" label="Contraseña actual" placeholder="Ingresa tu contraseña actual" />
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="password" type="password" label="Nueva contraseña" placeholder="Mín. 8 caracteres" />
                    <x-admin.form-input name="password_confirmation" type="password" label="Confirmar contraseña" placeholder="Repite la contraseña" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                    Actualizar perfil
                </button>
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                    Volver al dashboard
                </a>
            </div>
        </form>
    </div>
@endsection
