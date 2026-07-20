@extends('layouts.admin')

@section('title', 'Detalle de Rol')

@section('page-title', 'Detalle de Rol')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $rol->nombre }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $rol->descripcion ?? 'Sin descripción' }}</p>
                </div>
                <x-admin.badge :type="$rol->estado ? 'active' : 'inactive'">{{ $rol->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
            <div class="mt-4 flex gap-4 text-sm">
                <span class="text-gray-500"><strong class="text-gray-900">{{ $rol->users_count }}</strong> usuario(s)</span>
                <span class="text-gray-500"><strong class="text-gray-900">{{ $rol->permisos_count }}</strong> permiso(s)</span>
            </div>
        </div>

        @if ($rol->permisos->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Permisos asignados</h3>
                @foreach ($rol->permisos->groupBy('modulo') as $modulo => $permisos)
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">{{ $modulo }}</h4>
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
            <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Volver</a>
        </div>
    </div>
@endsection
