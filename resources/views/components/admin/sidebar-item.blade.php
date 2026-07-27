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

@if ($hasRoute && $hasPermission)
<li>
    <a href="{{ route($routeName) }}"
       class="admin-sidebar__link {{ $active ? 'active' : '' }}"
       data-tp-label="{{ $label }}"
       @if ($active) aria-current="page" @endif>
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
        <span class="admin-sidebar__text">{{ $label }}</span>
    </a>
</li>
@endif
