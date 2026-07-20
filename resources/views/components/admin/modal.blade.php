@props(['name' => 'modal', 'title' => '', 'size' => 'md', 'show' => false])

@php
$widths = ['sm' => 'max-w-md', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl'];
@endphp

<div x-data="{ open: {{ $show ? 'true' : 'false' }} }" x-show="open" x-cloak @keydown.window.escape="open = false" x-trap.noscroll="open" role="dialog" aria-modal="true" aria-label="{{ $title }}" {{ $attributes }}>
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-gray-950/40" @click="open = false"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="w-full {{ $widths[$size] ?? 'max-w-lg' }}" style="border-radius: .75rem; background-color: var(--color-surface); box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);">
            @if ($title)
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--color-border);">
                    <h3 class="text-base font-semibold" style="color: var(--color-text);">{{ $title }}</h3>
                    <button type="button" @click="open = false" class="rounded-lg p-1.5 transition hover-surface" aria-label="Cerrar" style="color: var(--color-muted);">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            <div class="px-5 py-4" style="color: var(--color-text);">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
