@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('content')
    <x-admin.page-header
        title="Roles del sistema"
        subtitle="{{ $roles->total() }} rol(es)"
        :button="['label' => 'Nuevo rol', 'url' => route('admin.roles.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success" dismissible>{{ session('success') }}</x-admin.alert>
    @endif

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--color-border);">
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Rol</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Descripción</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium" style="color: var(--color-muted);">Usuarios</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium" style="color: var(--color-muted);">Permisos</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Estado</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium" style="color: var(--color-muted);">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse ($roles as $rol)
                        <tr class="transition hover-surface">
                            <td class="px-4 py-2.5 font-medium" style="color: var(--color-text);">{{ $rol->nombre }}</td>
                            <td class="px-4 py-2.5 max-w-xs truncate" style="color: var(--color-muted);">{{ $rol->descripcion ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-center" style="color: var(--color-muted);">{{ $rol->users_count }}</td>
                            <td class="px-4 py-2.5 text-center" style="color: var(--color-muted);">{{ $rol->permisos_count }}</td>
                            <td class="px-4 py-2.5">
                                <x-admin.badge :type="$rol->estado ? 'active' : 'inactive'">{{ $rol->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <x-admin.dropdown>
                                    <x-admin.dropdown-link :href="route('admin.roles.show', $rol)">Ver detalle</x-admin.dropdown-link>
                                    <x-admin.dropdown-link :href="route('admin.roles.edit', $rol)">Editar</x-admin.dropdown-link>
                                    <form method="POST" action="{{ route('admin.roles.estado', $rol) }}" onsubmit="return confirm('¿{{ $rol->estado ? 'Desactivar' : 'Activar' }} el rol {{ $rol->nombre }}?')">
                                        @csrf @method('PATCH')
                                        <x-admin.dropdown-button :danger="!$rol->estado">{{ $rol->estado ? 'Desactivar' : 'Activar' }}</x-admin.dropdown-button>
                                    </form>
                                </x-admin.dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay roles registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--color-border);">{{ $roles->links() }}</div>
        @endif
    </div>
@endsection
