@props([
    'type' => 'button',
    'variant' => 'icon',
    'tone' => 'neutral',
    'icon' => null,
    'label' => null,
    'href' => null,
    'permission' => null,
    'tooltip' => null,
])

@php
    $hasPermission = $permission ? Auth::user()->tienePermiso($permission) : true;
    $class = $variant === 'icon' ? 'btn-icon' : 'btn btn-sm';
    $toneClass = $variant === 'icon' ? 'btn-icon--' . $tone : 'btn-' . $tone;
@endphp

@if ($hasPermission)
    @if ($href)
        <a href="{{ $href }}"
           {{ $attributes->merge(['class' => $class . ' ' . $toneClass]) }}
           @if ($tooltip) title="{{ $tooltip }}" aria-label="{{ $tooltip }}" @endif>
            @if ($icon)<i class="bi {{ $icon }}" aria-hidden="true"></i>@endif
            @if ($label && $variant !== 'icon')<span>{{ $label }}</span>@endif
        </a>
    @else
        <button type="{{ $type }}"
                {{ $attributes->merge(['class' => $class . ' ' . $toneClass]) }}
                @if ($tooltip && $variant === 'icon') title="{{ $tooltip }}" aria-label="{{ $tooltip }}" @endif>
            @if ($icon)<i class="bi {{ $icon }}" aria-hidden="true"></i>@endif
            @if ($label && $variant !== 'icon')<span>{{ $label }}</span>@endif
        </button>
    @endif
@endif
