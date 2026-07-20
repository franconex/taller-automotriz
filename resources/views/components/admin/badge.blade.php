@props(['type' => 'active'])

@php
$styles = [
    'active' => 'bg-green-100 text-green-700',
    'inactive' => 'bg-red-100 text-red-700',
    'warning' => 'bg-yellow-100 text-yellow-700',
    'info' => 'bg-blue-100 text-blue-700',
    'gray' => 'bg-gray-100 text-gray-600',
];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $styles[$type] ?? $styles['gray'] }}">
    {{ $slot }}
</span>
