@php
    $ruta ??= 'admin.especialidades';
    $modelo ??= 'item';
    $items ??= collect();
    $badge_field ??= 'estado';
    $badge_activo_text ??= 'Activo';
    $badge_inactivo_text ??= 'Inactivo';
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($items as $item)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->{$fields[0]} }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $item->{$fields[1] ?? 'descripcion'} ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($item->{$badge_field})
                            <x-admin.badge type="active">{{ $badge_activo_text }}</x-admin.badge>
                        @else
                            <x-admin.badge type="inactive">{{ $badge_inactivo_text }}</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route("$ruta.edit", $item) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">Editar</a>
                            <form method="POST" action="{{ route("$ruta.estado", $item) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('¿{{ $item->{$badge_field} ? 'Desactivar' : 'Activar' }} {{ $item->{$fields[0]} }}?')"
                                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">
                                    {{ $item->{$badge_field} ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-10 text-center text-gray-400">No hay registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $items->links() }}</div>
