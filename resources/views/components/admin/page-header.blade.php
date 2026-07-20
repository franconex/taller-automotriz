@props(['title' => '', 'subtitle' => '', 'button' => null])

<div class="mb-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-900 truncate">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($button)
            @if (is_array($button) && isset($button['url'], $button['label']))
                <x-admin.button :href="$button['url']" type="primary" class="shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                    {{ $button['label'] }}
                </x-admin.button>
            @else
                {{ $button }}
            @endif
        @endif
    </div>
    {{ $slot ?? '' }}
</div>
