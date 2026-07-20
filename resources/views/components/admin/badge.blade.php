@props(['type' => 'default', 'size' => 'sm'])

@php
$colors = [
    'active' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10',
    'inactive' => 'bg-red-50 text-red-700 ring-1 ring-red-600/10',
    'role' => 'bg-gray-50 text-gray-600 ring-1 ring-gray-300',
    'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/10',
    'info' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/10',
    'default' => 'bg-gray-50 text-gray-600 ring-1 ring-gray-300',
][$type] ?? 'bg-gray-50 text-gray-600 ring-1 ring-gray-300';

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
][$size] ?? 'px-2 py-0.5 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md font-medium {$sizes} {$colors}"]) }}>
    {{ $slot }}
</span>
