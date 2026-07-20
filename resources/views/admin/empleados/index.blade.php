@extends('layouts.admin')

@section('title', 'Empleados')
@section('page-title', 'Empleados')

@section('content')
    <x-admin.page-header
        title="Empleados"
        subtitle="{{ $empleados->total() }} registro(s)"
        :button="['label' => 'Nuevo empleado', 'url' => route('admin.empleados.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success" dismissible>{{ session('success') }}</x-admin.alert>
    @endif

    <div class="filter-bar">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, CI..." class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10 w-56">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Sucursal</label>
                <select name="sucursal_id" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($sucursales as $s)
                        <option value="{{ $s->id }}" @selected(request('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Estado</label>
                <select name="estado" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todos</option>
                    <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                    <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                </select>
            </div>
            <x-admin.button type="primary" tag="button" class="h-[38px]!">Filtrar</x-admin.button>
            <a href="{{ route('admin.empleados.index') }}" class="h-[38px] inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm font-medium transition hover-surface" style="color: var(--color-muted);">Limpiar</a>
        </form>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--color-border);">
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Nombre</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">CI</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Sucursal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Rol / Cuenta</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Estado</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium" style="color: var(--color-muted);">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse ($empleados as $empleado)
                        <tr class="transition hover-surface">
                            <td class="px-4 py-2.5 font-medium" style="color: var(--color-text);">{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $empleado->ci }}</td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $empleado->sucursal?->nombre ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                @if ($empleado->user)
                                    <div class="flex items-center gap-2">
                                        <x-admin.badge type="role">{{ $empleado->user->rol->nombre ?? 'Sin rol' }}</x-admin.badge>
                                        <span class="text-xs" style="color: var(--color-muted);">{{ $empleado->user->email }}</span>
                                    </div>
                                @else
                                    <span class="text-xs" style="color: var(--color-muted);">Sin cuenta</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <x-admin.badge :type="$empleado->estado ? 'active' : 'inactive'">{{ $empleado->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <x-admin.dropdown>
                                    <x-admin.dropdown-link :href="route('admin.empleados.show', $empleado)">Ver detalle</x-admin.dropdown-link>
                                    <x-admin.dropdown-link :href="route('admin.empleados.edit', $empleado)">Editar</x-admin.dropdown-link>
                                    <form method="POST" action="{{ route('admin.empleados.estado', $empleado) }}" onsubmit="return confirm('¿{{ $empleado->estado ? 'Desactivar' : 'Activar' }} a {{ $empleado->nombre }}?')">
                                        @csrf @method('PATCH')
                                        <x-admin.dropdown-button :danger="!$empleado->estado">{{ $empleado->estado ? 'Desactivar' : 'Activar' }}</x-admin.dropdown-button>
                                    </form>
                                </x-admin.dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay empleados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($empleados->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--color-border);">{{ $empleados->links() }}</div>
        @endif
    </div>
@endsection
