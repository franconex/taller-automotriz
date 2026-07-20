@props(['align' => 'right', 'width' => '48'])

@php
$alignClasses = ['right' => 'right-0 origin-top-right', 'left' => 'left-0 origin-top-left'][$align];
@endphp

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false" class="relative" {{ $attributes }}>
    <button type="button" @click="open = !open" class="rounded-lg p-1.5 transition hover-surface" aria-label="Acciones" aria-haspopup="true" :aria-expanded="open" style="color: var(--color-muted);">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 mt-1 min-w-[180px] rounded-lg border py-1 shadow-lg {{ $alignClasses }}" style="background-color: var(--color-surface); border-color: var(--color-border);" role="menu" @click="open = false">
        {{ $slot }}
    </div>
</div>
