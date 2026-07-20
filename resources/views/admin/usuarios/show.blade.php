@extends('layouts.admin')

@section('title', 'Detalle de Usuario')

@section('page-title', 'Detalle de Usuario')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color: var(--color-text);">{{ $usuario->nombre }}</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">{{ $usuario->email }}</p>
                </div>
                <x-admin.badge :type="$usuario->estado ? 'active' : 'inactive'">{{ $usuario->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información general</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Username</dt>
                    <dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $usuario->username }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Rol</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                            {{ $usuario->rol->nombre }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Último acceso</dt>
                    <dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Registrado</dt>
                    <dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $usuario->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($usuario->empleado)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Empleado vinculado</dt>
                        <dd class="mt-1">
                            <a href="{{ route('admin.empleados.show', $usuario->empleado) }}" class="text-sm text-brand-red hover:underline">
                                {{ $usuario->empleado->nombre }} {{ $usuario->empleado->apellido }}
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                Editar usuario
            </a>
            <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}" onsubmit="return confirm('¿{{ $usuario->estado ? 'Desactivar' : 'Activar' }} a {{ $usuario->nombre }}?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                    {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <a href="{{ route('admin.usuarios.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                Volver
            </a>
        </div>
    </div>
@endsection
