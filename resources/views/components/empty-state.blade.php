@props([
    'icon' => 'bi-inbox',
    'title' => 'Sin datos',
    'message' => null,
])

<div class="admin-empty text-center py-5">
    <i class="bi {{ $icon }} admin-empty__icon" aria-hidden="true"></i>
    <h3 class="h5 admin-empty__title">{{ $title }}</h3>
    @if ($message)
        <p class="text-muted mb-0">{{ $message }}</p>
    @endif
    {{ $slot ?? '' }}
</div>
