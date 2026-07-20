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

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Información personal</h3>
                <x-admin.form-input name="nombre" label="Nombre completo" :value="$usuario->nombre" :required="true" />
                <div class="mt-5">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :value="$usuario->email" :required="true" />
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Rol</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $usuario->rol->nombre }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Último acceso</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}</dd>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Cambiar contraseña</h3>
                <p class="text-sm text-gray-500 mb-4">Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>
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
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Volver al dashboard
                </a>
            </div>
        </form>
    </div>
@endsection
