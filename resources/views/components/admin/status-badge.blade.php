@props([
    'tone' => 'neutral',
    'icon' => null,
    'label' => '',
])

<span {{ $attributes->merge(['class' => 'admin-status admin-status--' . $tone]) }}>
    @if ($icon)
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
    @endif
    <span>{{ $label }}</span>
</span>
