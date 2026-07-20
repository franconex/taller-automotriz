@extends('layouts.admin')

@section('title', 'Empleados')

@section('page-title', 'Gestión de Empleados')

@section('content')
    <x-admin.page-header
        title="Empleados"
        subtitle="{{ $empleados->total() }} registro(s) encontrados"
        :button="['label' => 'Nuevo empleado', 'url' => route('admin.empleados.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, CI..." class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10 w-56">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sucursal</label>
                <select name="sucursal_id" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($sucursales as $s)
                        <option value="{{ $s->id }}" @selected(request('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
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
            <a href="{{ route('admin.empleados.index') }}" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Limpiar</a>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">CI</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Cargo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Sucursal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rol / Usuario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($empleados as $empleado)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $empleado->ci }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $empleado->cargo ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $empleado->sucursal?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($empleado->user)
                                <div class="flex flex-col gap-0.5">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 w-fit">
                                        {{ $empleado->user->rol->nombre ?? 'Sin rol' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $empleado->user->email }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Sin cuenta</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($empleado->estado)
                                <x-admin.badge type="active">Activo</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">Inactivo</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.empleados.show', $empleado) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Ver</a>
                                <a href="{{ route('admin.empleados.edit', $empleado) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">No hay empleados registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $empleados->links() }}</div>
@endsection
