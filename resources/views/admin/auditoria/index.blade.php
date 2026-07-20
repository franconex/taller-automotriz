@extends('layouts.admin')

@section('title', 'Auditoría')

@section('page-title', 'Auditoría del Sistema')

@section('content')
    <x-admin.page-header title="Registro de actividad" subtitle="{{ $registros->total() }} registro(s)" />

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Acción</label>
                <select name="accion" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($acciones as $a)
                        <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Entidad</label>
                <select name="entidad_afectada" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($entidades as $e)
                        <option value="{{ $e }}" @selected(request('entidad_afectada') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
            </div>
            <button type="submit" class="rounded-xl bg-brand-red px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-red-dark">Filtrar</button>
            <a href="{{ route('admin.auditoria.index') }}" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Limpiar</a>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Usuario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Acción</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Entidad</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Detalle</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($registros as $log)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900">{{ $log->usuario?->nombre ?? 'Sistema' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">{{ $log->accion }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->entidad_afectada }} @if($log->entidad_id) #{{ $log->entidad_id }} @endif</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $log->detalle ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $log->direccion_ip ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay registros de auditoría.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $registros->links() }}</div>
@endsection
