@php
    use Illuminate\Support\Facades\Route;

    $items = [
        ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ];

    $atencion = [
        ['route' => 'admin.clientes.index',  'permission' => 'clientes.ver',  'icon' => 'bi-person-vcard',   'label' => 'Clientes'],
        ['route' => 'admin.vehiculos.index', 'permission' => 'vehiculos.ver', 'icon' => 'bi-car-front',      'label' => 'Vehículos'],
        ['route' => 'admin.citas.index',     'permission' => 'citas.ver',     'icon' => 'bi-calendar-check', 'label' => 'Citas'],
        ['route' => 'admin.ordenes.index',   'permission' => 'ordenes.ver',   'icon' => 'bi-clipboard-check','label' => 'Órdenes de trabajo'],
    ];

    $inventario = [
        ['route' => 'admin.inventario.index',             'permission' => 'inventario.ver', 'icon' => 'bi-boxes',         'label' => 'Inventario por sucursal'],
        ['route' => 'admin.movimientos-inventario.index', 'permission' => 'inventario.ver', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos de inventario'],
    ];

    $finanzas = [
        ['route' => 'admin.pagos.index',    'permission' => 'pagos.ver',    'icon' => 'bi-cash-coin',   'label' => 'Pagos'],
        ['route' => 'admin.reportes.index', 'permission' => 'reportes.ver', 'icon' => 'bi-graph-up',    'label' => 'Reportes'],
        ['route' => 'admin.auditoria.index','permission' => 'auditoria.ver','icon' => 'bi-journal-text','label' => 'Auditoría'],
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

<nav class="admin-sidebar__nav" aria-label="Menú del gerente">
    <div class="admin-sidebar__section">Principal</div>
    <ul class="list-unstyled m-0">
        <x-admin.sidebar-item
            routeName="admin.dashboard"
            icon="bi-speedometer2"
            label="Dashboard" />
    </ul>

    @if ($hasVisible($atencion))
        <div class="admin-sidebar__section">Atención y operación</div>
        <ul class="list-unstyled m-0">
            @foreach ($atencion as $item)
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

    @if ($hasVisible($finanzas))
        <div class="admin-sidebar__section">Finanzas y control</div>
        <ul class="list-unstyled m-0">
            @foreach ($finanzas as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif
</nav>
