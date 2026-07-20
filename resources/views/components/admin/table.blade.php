@props(['headers' => [], 'rows' => [], 'empty' => 'No se encontraron registros.'])

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($rows as $row)
                <tr class="transition hover:bg-gray-50">
                    {{ $row }}
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-10 text-center text-gray-400">
                        {{ $empty }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
