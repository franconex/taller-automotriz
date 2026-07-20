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

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dirección</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Teléfono</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Empleados</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sucursales as $sucursal)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $sucursal->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $sucursal->direccion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sucursal->telefono ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $sucursal->empleados_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($sucursal->estado)
                                <x-admin.badge type="active">Activa</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactiva</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.sucursales.show', $sucursal) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Ver</a>
                                <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay sucursales registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $sucursales->links() }}</div>
@endsection
