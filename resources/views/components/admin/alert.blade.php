@props(['type' => 'success'])

@php
$styles = [
    'success' => 'border-green-200 bg-green-50 text-green-700',
    'error' => 'border-red-200 bg-red-50 text-red-700',
    'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
    'info' => 'border-blue-200 bg-blue-50 text-blue-700',
];
@endphp

<div role="alert" class="mb-6 rounded-xl border {{ $styles[$type] ?? $styles['info'] }} px-4 py-3 text-sm">
    {{ $slot }}
</div>
