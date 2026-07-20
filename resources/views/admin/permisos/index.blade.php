@extends('layouts.admin')

@section('title', 'Permisos')

@section('page-title', 'Gestión de Permisos')

@section('content')
    <x-admin.page-header
        title="Permisos del sistema"
        subtitle="{{ $permisos->total() }} permiso(s) registrado(s)"
    />

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Módulo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Roles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($permisos as $permiso)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $permiso->nombre }}</td>
                        <td class="px-4 py-3"><code class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $permiso->codigo }}</code></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $permiso->modulo }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $permiso->descripcion ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $permiso->roles_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay permisos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $permisos->links() }}</div>
@endsection
