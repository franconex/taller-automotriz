@props([
    'title' => '',
    'description' => null,
    'breadcrumb' => null,
])

<header class="admin-page-header">
    <div class="admin-page-header__main">
        <h1 class="admin-page-header__title">{{ $title }}</h1>
        @if ($description || trim($slot ?? '') !== '')
            <p class="admin-page-header__description">{{ $description ?? $slot }}</p>
        @endif
    </div>

    @if (! empty($actions) || trim($actions ?? '') !== '')
        <div class="admin-page-header__actions">
            {{ $actions ?? '' }}
        </div>
    @endif
</header>
