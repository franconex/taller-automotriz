@props(['title' => '', 'subtitle' => '', 'button' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl font-bold text-gray-900">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($button)
        @if (is_array($button) && isset($button['url'], $button['label']))
            <a href="{{ $button['url'] }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-red px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                {{ $button['label'] }}
            </a>
        @else
            {{ $button }}
        @endif
    @endif
</div>
