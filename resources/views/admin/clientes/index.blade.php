@extends('layouts.admin')

@section('title', 'Clientes')

@section('page-title', 'Gestión de Clientes')

@section('content')
    <x-admin.page-header
        title="Clientes"
        subtitle="{{ $clientes->total() }} cliente(s) registrado(s)"
        :button="['label' => 'Nuevo cliente', 'url' => route('admin.clientes.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, CI, teléfono..." class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10 w-64">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
                <select name="estado" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todos</option>
                    <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                    <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-brand-red px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-red-dark">Filtrar</button>
            <a href="{{ route('admin.clientes.index') }}" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Limpiar</a>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">CI</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Teléfono</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->ci }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->telefono ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($cliente->estado)
                                <x-admin.badge type="active">Activo</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactivo</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.clientes.show', $cliente) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Ver</a>
                                <a href="{{ route('admin.clientes.edit', $cliente) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay clientes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $clientes->links() }}</div>
@endsection
