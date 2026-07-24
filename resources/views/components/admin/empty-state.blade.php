@props([
    'icon' => 'bi-inbox',
    'title' => 'Sin datos para mostrar',
    'message' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="admin-empty">
    <div class="admin-empty__icon">
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
    </div>
    <h3 class="admin-empty__title">{{ $title }}</h3>
    @if ($message || trim($slot ?? '') !== '')
        <p class="admin-empty__message">{{ $message ?? $slot }}</p>
    @endif
    @if ($actionLabel && $actionHref)
        <div class="admin-empty__action">
            <a href="{{ $actionHref }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                {{ $actionLabel }}
            </a>
        </div>
    @endif
    @if (! empty($action) && trim($action ?? '') !== '')
        <div class="admin-empty__action">{{ $action }}</div>
    @endif
</div>
