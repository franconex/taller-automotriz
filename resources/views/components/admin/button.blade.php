@props(['type' => 'primary', 'tag' => 'button', 'href' => null])

@php
$classes = [
    'primary' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-red px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-red-dark focus-visible:outline-2 focus-visible:outline-brand-red focus-visible:outline-offset-2',
    'secondary' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-gray-400 focus-visible:outline-offset-2',
    'ghost' => 'inline-flex items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-gray-400 focus-visible:outline-offset-2',
    'danger' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-red-400 focus-visible:outline-offset-2',
][$type] ?? '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
