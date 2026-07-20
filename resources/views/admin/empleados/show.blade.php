@extends('layouts.admin')

@section('title', 'Detalle de Empleado')

@section('page-title', 'Detalle de Empleado')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color: var(--color-text);">{{ $empleado->nombre }} {{ $empleado->apellido }}</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">{{ $empleado->cargo ?? 'Sin cargo' }}</p>
                </div>
                <x-admin.badge :type="$empleado->estado ? 'active' : 'inactive'">{{ $empleado->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información general</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">CI</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->ci }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Teléfono</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->telefono ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Sucursal</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->sucursal?->nombre ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Fecha ingreso</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->fecha_ingreso?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Dirección</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->direccion ?? '—' }}</dd></div>
            </dl>
        </div>

        @if ($empleado->user)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Cuenta de acceso</h3>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Usuario</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->user->username }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Email</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->user->email }}</dd></div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Rol</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                {{ $empleado->user->rol->nombre ?? 'Sin rol' }}
                            </span>
                        </dd>
                    </div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Estado</dt>
                        <dd class="mt-1">
                            <x-admin.badge :type="$empleado->user->estado ? 'active' : 'inactive'">{{ $empleado->user->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
                        </dd>
                    </div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Último acceso</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $empleado->user->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}</dd></div>
                </dl>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.empleados.edit', $empleado) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar empleado</a>
            <a href="{{ route('admin.empleados.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Volver</a>
        </div>
    </div>
@endsection
