@extends('layouts.admin')

@section('title', 'Permisos')

@section('page-title', 'Gestión de Permisos')

@section('content')
    <x-admin.page-header
        title="Permisos del sistema"
        subtitle="{{ $permisos->total() }} permiso(s) registrado(s)"
    />

    <div class="table-wrap">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="border-color: var(--color-border); background-color: var(--color-bg);">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Módulo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Descripción</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Roles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-custom">
                @forelse ($permisos as $permiso)
                    <tr class="transition hover-surface">
                        <td class="px-4 py-3 font-medium" style="color: var(--color-text);">{{ $permiso->nombre }}</td>
                        <td class="px-4 py-3"><code class="rounded px-2 py-0.5 text-xs" style="background-color: var(--color-border); color: var(--color-muted);">{{ $permiso->codigo }}</code></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $permiso->modulo }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-xs truncate" style="color: var(--color-muted);">{{ $permiso->descripcion ?? '—' }}</td>
                        <td class="px-4 py-3 text-center" style="color: var(--color-muted);">{{ $permiso->roles_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center" style="color: var(--color-muted);">No hay permisos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $permisos->links() }}</div>
@endsection
