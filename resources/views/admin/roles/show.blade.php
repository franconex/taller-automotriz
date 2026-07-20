@extends('layouts.admin')

@section('title', 'Detalle de Rol')

@section('page-title', 'Detalle de Rol')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color: var(--color-text);">{{ $rol->nombre }}</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">{{ $rol->descripcion ?? 'Sin descripción' }}</p>
                </div>
                <x-admin.badge :type="$rol->estado ? 'active' : 'inactive'">{{ $rol->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
            <div class="mt-4 flex gap-4 text-sm">
                <span style="color: var(--color-muted);"><strong style="color: var(--color-text);">{{ $rol->users_count }}</strong> usuario(s)</span>
                <span style="color: var(--color-muted);"><strong style="color: var(--color-text);">{{ $rol->permisos_count }}</strong> permiso(s)</span>
            </div>
        </div>

        @if ($rol->permisos->isNotEmpty())
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Permisos asignados</h3>
                @foreach ($rol->permisos->groupBy('modulo') as $modulo => $permisos)
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--color-muted);">{{ $modulo }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($permisos as $permiso)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                    {{ $permiso->nombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.edit', $rol) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar rol</a>
            <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Volver</a>
        </div>
    </div>
@endsection
