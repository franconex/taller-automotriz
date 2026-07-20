@props(['type' => 'success', 'dismissible' => false])

@php
$styles = [
    'success' => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/10',
    'error' => 'bg-red-50 text-red-800 ring-1 ring-red-600/10',
    'warning' => 'bg-amber-50 text-amber-800 ring-1 ring-amber-600/10',
    'info' => 'bg-blue-50 text-blue-800 ring-1 ring-blue-600/10',
];

$icons = [
    'success' => '<svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>',
    'error' => '<svg class="h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>',
    'warning' => '<svg class="h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
    'info' => '<svg class="h-5 w-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>',
];
@endphp

<div
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    x-transition
    {{ $attributes->merge(['class' => 'mb-5 flex items-start gap-3 rounded-lg px-4 py-3 text-sm ' . ($styles[$type] ?? $styles['info'])]) }}
>
    {!! $icons[$type] ?? '' !!}
    <div class="flex-1">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" @click="show = false" class="shrink-0 rounded p-1 transition hover:bg-black/5">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
