@extends('layouts.admin')

@section('title', 'Usuarios')

@section('page-title', 'Gestión de Usuarios')

@section('content')
    <x-admin.page-header
        title="Usuarios del sistema"
        subtitle="{{ $usuarios->total() }} registro(s) encontrados"
        :button="['label' => 'Nuevo usuario', 'url' => route('admin.usuarios.create')]"
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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Username</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rol</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Último acceso</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($usuarios as $usuario)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $usuario->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->username }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                {{ $usuario->rol->nombre }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($usuario->estado)
                                <x-admin.badge type="active">Activo</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactivo</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $usuario->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.usuarios.show', $usuario) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">
                                    Ver
                                </a>
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">
                                    Editar
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $usuarios->links() }}
    </div>
@endsection
