@extends('layouts.admin')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
    <x-admin.page-header
        title="Clientes"
        subtitle="{{ $clientes->total() }} cliente(s)"
        :button="['label' => 'Nuevo cliente', 'url' => route('admin.clientes.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success" dismissible>{{ session('success') }}</x-admin.alert>
    @endif

    <div class="filter-bar">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, CI, teléfono..." class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10 w-64">
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
            <a href="{{ route('admin.clientes.index') }}" class="h-[38px] inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm font-medium transition hover-surface" style="color: var(--color-muted);">Limpiar</a>
        </form>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--color-border);">
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Cliente</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">CI</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Teléfono</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Email</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Estado</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium" style="color: var(--color-muted);">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse ($clientes as $cliente)
                        <tr class="transition hover-surface">
                            <td class="px-4 py-2.5 font-medium" style="color: var(--color-text);">{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $cliente->ci }}</td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $cliente->telefono ?? '—' }}</td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $cliente->email ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                <x-admin.badge :type="$cliente->estado ? 'active' : 'inactive'">{{ $cliente->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <x-admin.dropdown>
                                    <x-admin.dropdown-link :href="route('admin.clientes.show', $cliente)">Ver detalle</x-admin.dropdown-link>
                                    <x-admin.dropdown-link :href="route('admin.clientes.edit', $cliente)">Editar</x-admin.dropdown-link>
                                    <form method="POST" action="{{ route('admin.clientes.estado', $cliente) }}" onsubmit="return confirm('¿{{ $cliente->estado ? 'Desactivar' : 'Activar' }}?')">
                                        @csrf @method('PATCH')
                                        <x-admin.dropdown-button :danger="!$cliente->estado">{{ $cliente->estado ? 'Desactivar' : 'Activar' }}</x-admin.dropdown-button>
                                    </form>
                                </x-admin.dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay clientes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($clientes->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--color-border);">{{ $clientes->links() }}</div>
        @endif
    </div>
@endsection
