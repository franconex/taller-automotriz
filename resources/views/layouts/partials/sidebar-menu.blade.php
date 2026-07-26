@php
    use Illuminate\Support\Facades\Route;

    $items = [
        ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ];

    $mecanicoOp = [
        ['route' => 'mecanico.dashboard',   'permission' => null, 'icon' => 'bi-speedometer2', 'label' => 'Panel Mecánico'],
        ['route' => 'mecanico.mis_ordenes', 'permission' => null, 'icon' => 'bi-tools',        'label' => 'Mis Órdenes'],
    ];

    $organizacion = [
        ['route' => 'admin.sucursales.index', 'permission' => 'sucursales.ver', 'icon' => 'bi-building',     'label' => 'Sucursales'],
        ['route' => 'admin.empleados.index',  'permission' => 'usuarios.ver',   'icon' => 'bi-people',       'label' => 'Empleados'],
        ['route' => 'admin.usuarios.index',   'permission' => 'usuarios.ver',   'icon' => 'bi-person-badge', 'label' => 'Usuarios'],
        ['route' => 'admin.roles.index',      'permission' => 'roles.ver',      'icon' => 'bi-shield-lock',  'label' => 'Roles y permisos'],
    ];

    $atencion = [
        ['route' => 'admin.clientes.index',  'permission' => 'clientes.ver',  'icon' => 'bi-person-vcard',   'label' => 'Clientes'],
        ['route' => 'admin.vehiculos.index', 'permission' => 'vehiculos.ver', 'icon' => 'bi-car-front',      'label' => 'Vehículos'],
        ['route' => 'admin.citas.index',     'permission' => 'citas.ver',     'icon' => 'bi-calendar-check', 'label' => 'Citas'],
        ['route' => 'admin.ordenes.index',   'permission' => 'ordenes.ver',   'icon' => 'bi-clipboard-check','label' => 'Órdenes de trabajo'],
        ['route' => 'admin.mecanicos.index', 'permission' => 'usuarios.ver',  'icon' => 'bi-tools',         'label' => 'Mecánicos'],
    ];

    $serviciosInventario = [
        ['route' => 'admin.tipos-servicio.index', 'permission' => 'dashboard.ver', 'icon' => 'bi-tags',  'label' => 'Tipos de servicio'],
        ['route' => 'admin.servicios.index',      'permission' => 'dashboard.ver', 'icon' => 'bi-gear',  'label' => 'Servicios'],
        ['route' => 'admin.proveedores.index',    'permission' => 'inventario.ver','icon' => 'bi-truck', 'label' => 'Proveedores'],
        ['route' => 'admin.repuestos.index',      'permission' => 'inventario.ver','icon' => 'bi-box-seam','label' => 'Repuestos'],
        ['route' => 'admin.inventario.index',     'permission' => 'inventario.ver','icon' => 'bi-boxes', 'label' => 'Inventario por sucursal'],
        ['route' => 'admin.movimientos-inventario.index', 'permission' => 'inventario.ver', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos de inventario'],
    ];

    $finanzas = [
        ['route' => 'admin.metodos-pago.index',   'permission' => 'pagos.ver',     'icon' => 'bi-credit-card',   'label' => 'Métodos de pago'],
        ['route' => 'admin.pagos.index',          'permission' => 'pagos.ver',     'icon' => 'bi-cash-coin',     'label' => 'Pagos'],
        ['route' => 'admin.comprobantes.index',   'permission' => 'pagos.ver',     'icon' => 'bi-receipt',       'label' => 'Comprobantes'],
        ['route' => 'admin.reportes.index',       'permission' => 'reportes.ver',  'icon' => 'bi-graph-up',      'label' => 'Reportes'],
        ['route' => 'admin.auditoria.index',      'permission' => 'auditoria.ver', 'icon' => 'bi-journal-text',  'label' => 'Auditoría'],
    ];

    $sistema = [
        ['route' => 'admin.configuracion.index', 'permission' => 'dashboard.ver', 'icon' => 'bi-sliders', 'label' => 'Configuración'],
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

<nav class="admin-sidebar__nav" aria-label="Menú principal">
    {{-- PRINCIPAL --}}
    <div class="admin-sidebar__section">Principal</div>
    <ul class="list-unstyled m-0">
        <x-admin.sidebar-item
            routeName="admin.dashboard"
            icon="bi-speedometer2"
            label="Dashboard" />
    </ul>

    {{-- MÓDULO MECÁNICO (Se muestra si existen las rutas de mecánico) --}}
    @if (Route::has('mecanico.dashboard'))
        <div class="admin-sidebar__section">MECÁNICO</div>
        <ul class="list-unstyled m-0">
            @foreach ($mecanicoOp as $item)
                @if(Route::has($item['route']))
                    <x-admin.sidebar-item
                        :routeName="$item['route']"
                        :permission="$item['permission']"
                        :icon="$item['icon']"
                        :label="$item['label']" />
                @endif
            @endforeach
        </ul>
    @endif

    {{-- ORGANIZACIÓN --}}
    @if ($hasVisible($organizacion))
        <div class="admin-sidebar__section">Organización</div>
        <ul class="list-unstyled m-0">
            @foreach ($organizacion as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif

    {{-- ATENCIÓN Y OPERACIÓN --}}
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

    {{-- SERVICIOS E INVENTARIO --}}
    @if ($hasVisible($serviciosInventario))
        <div class="admin-sidebar__section">Servicios e inventario</div>
        <ul class="list-unstyled m-0">
            @foreach ($serviciosInventario as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif

    {{-- FINANZAS Y CONTROL --}}
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

    {{-- SISTEMA --}}
    @if ($hasVisible($sistema))
        <div class="admin-sidebar__section">Sistema</div>
        <ul class="list-unstyled m-0">
            @foreach ($sistema as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif
</nav>