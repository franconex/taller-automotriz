@php
    $ruta ??= 'admin.especialidades';
    $badge_field ??= 'estado';
    $badge_activo_text ??= 'Activo';
    $badge_inactivo_text ??= 'Inactivo';
@endphp

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="border-color: var(--color-border);">
                    @foreach ($headers as $header)
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-custom">
                @forelse ($items as $item)
                    <tr class="transition hover-surface">
                        <td class="px-4 py-2.5 font-medium" style="color: var(--color-text);">{{ $item->{$fields[0]} }}</td>
                        <td class="px-4 py-2.5 max-w-xs truncate" style="color: var(--color-muted);">{{ $item->{$fields[1] ?? 'descripcion'} ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            @if ($item->{$badge_field})
                                <x-admin.badge type="active">{{ $badge_activo_text }}</x-admin.badge>
                            @else
                                <x-admin.badge type="inactive">{{ $badge_inactivo_text }}</x-admin.badge>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <x-admin.dropdown>
                                <x-admin.dropdown-link :href="route($ruta . '.edit', $item)">Editar</x-admin.dropdown-link>
                                <form method="POST" action="{{ route($ruta . '.estado', $item) }}" onsubmit="return confirm('¿{{ $item->{$badge_field} ? 'Desactivar' : 'Activar' }} {{ $item->{$fields[0]} }}?')">
                                    @csrf @method('PATCH')
                                    <x-admin.dropdown-button :danger="!$item->{$badge_field}">{{ $item->{$badge_field} ? 'Desactivar' : 'Activar' }}</x-admin.dropdown-button>
                                </form>
                            </x-admin.dropdown>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headers) }}" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($items->hasPages())
        <div class="border-t px-4 py-3" style="border-color: var(--color-border);">{{ $items->links() }}</div>
    @endif
</div>
