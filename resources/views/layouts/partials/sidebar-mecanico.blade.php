@php
    use Illuminate\Support\Facades\Route;

    $items = [
        ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ];

    $ordenes = [
        ['route' => 'admin.ordenes.index', 'permission' => 'ordenes.ver', 'icon' => 'bi-clipboard-check', 'label' => 'Órdenes de trabajo'],
    ];

    $inventario = [
        ['route' => 'admin.inventario.index', 'permission' => 'inventario.ver', 'icon' => 'bi-boxes', 'label' => 'Inventario'],
    ];

    $hasVisible = function (array $list): bool {
        foreach ($list as $item) {
            $routeExists = Route::has($item['route']);
            $hasPermission = empty($item['permission']) || Auth::user()->tienePermiso($item['permission']);
            if ($routeExists && $hasPermission) {
                return true;
            }
        }
        return false;
    };
@endphp

<nav class="admin-sidebar__nav" aria-label="Menú del mecánico">
    <div class="admin-sidebar__section">Principal</div>
    <ul class="list-unstyled m-0">
        <x-admin.sidebar-item
            routeName="admin.dashboard"
            icon="bi-speedometer2"
            label="Dashboard" />
    </ul>

    @if ($hasVisible($ordenes))
        <div class="admin-sidebar__section">Órdenes</div>
        <ul class="list-unstyled m-0">
            @foreach ($ordenes as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif

    @if ($hasVisible($inventario))
        <div class="admin-sidebar__section">Inventario</div>
        <ul class="list-unstyled m-0">
            @foreach ($inventario as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif
</nav>
