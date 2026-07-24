@props([
    'title' => '',
    'description' => null,
])

<header class="admin-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="admin-page-header__title">{{ $title }}</h1>
            @if ($description)
                <p class="admin-page-header__description">{{ $description }}</p>
            @endif
        </div>
        @isset($action)
            <div class="admin-page-header__action">{{ $action }}</div>
        @endisset
    </div>

    @isset($breadcrumb)
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb mb-0 small">
                {{ $breadcrumb }}
            </ol>
        </nav>
    @endisset
</header>
