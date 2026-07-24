@props([
    'label' => '',
    'icon' => 'bi-folder',
    'id' => null,
    'items' => [],
])

@php
    $id = $id ?: 'sg-' . \Illuminate\Support\Str::slug($label) . '-' . uniqid();

    $hasAnyActive = false;
    $hasAnyVisible = false;

    foreach ($items as $item) {
        $routeExists = \Illuminate\Support\Facades\Route::has($item['route']);
        $hasPermission = empty($item['permission']) || Auth::user()->tienePermiso($item['permission']);
        if ($routeExists && $hasPermission) {
            $hasAnyVisible = true;
        }
        if ($routeExists && $hasPermission && request()->routeIs($item['route'])) {
            $hasAnyActive = true;
        }
    }

    $openByDefault = $hasAnyActive;
@endphp

@if ($hasAnyVisible)
    <div class="admin-sidebar__group {{ $openByDefault ? 'is-open' : '' }} {{ $hasAnyActive ? 'is-active' : '' }}">
        <button type="button"
                class="admin-sidebar__link admin-sidebar__group-toggle"
                data-tp-sidebar-group="{{ $id }}"
                aria-expanded="{{ $openByDefault ? 'true' : 'false' }}"
                aria-controls="{{ $id }}">
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
            <span class="admin-sidebar__text">{{ $label }}</span>
            <i class="bi bi-chevron-down admin-sidebar__group-chevron" aria-hidden="true"></i>
        </button>

        <ul class="admin-sidebar__submenu" id="{{ $id }}" role="group" aria-label="{{ $label }}">
            @foreach ($items as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route'] ?? null"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon'] ?? 'bi-circle'"
                    :label="$item['label']" />
            @endforeach
        </ul>
    </div>
@endif
