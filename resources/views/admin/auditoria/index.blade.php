@extends('layouts.admin')

@section('title', 'Auditoría')
@section('page-title', 'Auditoría')

@section('content')
    <x-admin.page-header title="Registro de actividad" subtitle="{{ $registros->total() }} registro(s)" />

    <div class="filter-bar">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Acción</label>
                <select name="accion" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($acciones as $a)
                        <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Entidad</label>
                <select name="entidad_afectada" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                    <option value="">Todas</option>
                    @foreach ($entidades as $e)
                        <option value="{{ $e }}" @selected(request('entidad_afectada') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--color-muted);">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
            </div>
            <x-admin.button type="primary" tag="button" class="h-[38px]!">Filtrar</x-admin.button>
            <a href="{{ route('admin.auditoria.index') }}" class="h-[38px] inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm font-medium transition hover-surface" style="color: var(--color-muted);">Limpiar</a>
        </form>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--color-border);">
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Fecha</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Usuario</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Acción</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Entidad</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse ($registros as $log)
                        <tr class="transition hover-surface">
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs" style="color: var(--color-muted);">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2.5 font-medium" style="color: var(--color-text);">{{ $log->usuario?->nombre ?? 'Sistema' }}</td>
                            <td class="px-4 py-2.5"><x-admin.badge type="info">{{ $log->accion }}</x-admin.badge></td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $log->entidad_afectada }} @if($log->entidad_id)#{{ $log->entidad_id }}@endif</td>
                            <td class="px-4 py-2.5 max-w-xs truncate" style="color: var(--color-muted);">{{ $log->detalle ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay registros de auditoría.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($registros->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--color-border);">{{ $registros->links() }}</div>
        @endif
    </div>
@endsection
