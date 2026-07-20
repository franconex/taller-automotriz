@extends('layouts.admin')

@section('title', 'Sucursales')

@section('page-title', 'Gestión de Sucursales')

@section('content')
    <x-admin.page-header
        title="Sucursales"
        subtitle="{{ $sucursales->total() }} sucursal(es) registrada(s)"
        :button="['label' => 'Nueva sucursal', 'url' => route('admin.sucursales.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    <div class="table-wrap">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="border-color: var(--color-border); background-color: var(--color-bg);">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Dirección</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Teléfono</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Empleados</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-custom">
                @forelse ($sucursales as $sucursal)
                    <tr class="transition hover-surface">
                        <td class="px-4 py-3 font-medium" style="color: var(--color-text);">{{ $sucursal->nombre }}</td>
                        <td class="px-4 py-3 max-w-xs truncate" style="color: var(--color-muted);">{{ $sucursal->direccion }}</td>
                        <td class="px-4 py-3" style="color: var(--color-muted);">{{ $sucursal->telefono ?? '—' }}</td>
                        <td class="px-4 py-3 text-center" style="color: var(--color-muted);">{{ $sucursal->empleados_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($sucursal->estado)
                                <x-admin.badge type="active">Activa</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactiva</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.sucursales.show', $sucursal) }}" class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition hover-surface" style="border-color: var(--color-border); color: var(--color-muted);">Ver</a>
                                <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition hover-surface" style="border-color: var(--color-border); color: var(--color-muted);">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center" style="color: var(--color-muted);">No hay sucursales registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $sucursales->links() }}</div>
@endsection
