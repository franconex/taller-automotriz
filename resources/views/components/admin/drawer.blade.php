@props(['name' => 'drawer', 'title' => '', 'show' => false])

<div x-data="{ open: {{ $show ? 'true' : 'false' }} }" x-show="open" x-cloak @keydown.window.escape="open = false" x-trap.noscroll="open" role="dialog" aria-modal="true" aria-label="{{ $title }}" {{ $attributes }}>
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-gray-950/40" @click="open = false"></div>
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 z-50 w-full max-w-lg" style="background-color: var(--color-surface); box-shadow: -10px 0 30px rgba(0,0,0,.1);">
        <div class="flex h-16 items-center justify-between px-5" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="text-base font-semibold" style="color: var(--color-text);">{{ $title }}</h3>
            <button type="button" @click="open = false" class="rounded-lg p-1.5 transition hover-surface" aria-label="Cerrar" style="color: var(--color-muted);">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="overflow-y-auto h-[calc(100%-4rem)] px-5 py-4" style="color: var(--color-text);">
            {{ $slot }}
        </div>
    </div>
</div>
