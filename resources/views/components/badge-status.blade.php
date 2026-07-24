@props([
    'active' => true,
    'activeLabel' => 'Activo',
    'inactiveLabel' => 'Inactivo',
])

@if ($active)
    <span class="badge text-bg-success-subtle text-success-emphasis admin-badge">
        <i class="bi bi-check-circle" aria-hidden="true"></i> {{ $activeLabel }}
    </span>
@else
    <span class="badge text-bg-secondary admin-badge">
        <i class="bi bi-x-circle" aria-hidden="true"></i> {{ $inactiveLabel }}
    </span>
@endif
