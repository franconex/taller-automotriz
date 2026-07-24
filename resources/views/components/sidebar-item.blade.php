@props([
    'routeName' => null,
    'permission' => null,
    'icon' => 'bi-circle',
    'label' => '',
])

@php
    $hasRoute = $routeName ? \Illuminate\Support\Facades\Route::has($routeName) : false;
    $hasPermission = $permission ? Auth::user()->tienePermiso($permission) : true;
    $active = $routeName && request()->routeIs($routeName);
@endphp

<li>
    @if ($hasRoute && $hasPermission)
        <a href="{{ route($routeName) }}"
           class="admin-sidebar__link {{ $active ? 'active' : '' }}"
           @if ($active) aria-current="page" @endif>
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
            <span class="admin-sidebar__text">{{ $label }}</span>
        </a>
    @else
        <span class="admin-sidebar__link disabled" aria-disabled="true">
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
            <span class="admin-sidebar__text">{{ $label }}</span>
            <span class="badge text-bg-secondary ms-auto">Próximamente</span>
        </span>
    @endif
</li>
