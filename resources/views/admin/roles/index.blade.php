@extends('layouts.admin')

@section('title', 'Roles')

@section('page-title', 'Gestión de Roles')

@section('content')
    <x-admin.page-header
        title="Roles del sistema"
        subtitle="{{ $roles->total() }} rol(es) configurado(s)"
        :button="['label' => 'Nuevo rol', 'url' => route('admin.roles.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif
    @if (session('error'))
        <x-admin.alert type="error">{{ session('error') }}</x-admin.alert>
    @endif

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rol</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Usuarios</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Permisos</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($roles as $rol)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $rol->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $rol->descripcion ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $rol->users_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $rol->permisos_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($rol->estado)
                                <x-admin.badge type="active">Activo</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactivo</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.roles.show', $rol) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Ver</a>
                                <a href="{{ route('admin.roles.edit', $rol) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay roles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $roles->links() }}</div>
@endsection
