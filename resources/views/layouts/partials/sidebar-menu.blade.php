@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $rolActual = $user?->rol?->nombre ?? '';

    $esMecanico = ($rolActual === 'Mecánico');
    $esGerente  = ($rolActual === 'Gerente');
    $esAdmin    = ($rolActual === 'Administrador');

    // Función para verificar si un ítem individual es accesible por el usuario actual
    $canSeeItem = function (array $item) use ($user): bool {
        // 1. Validar que la ruta exista
        if (!Route::has($item['route'])) {
            return false;
        }

        // 2. Validar permiso específico si el ítem lo requiere
        if (!empty($item['permission'])) {
            return $user && $user->tienePermiso($item['permission']);
        }

        return true;
    };

    // Función para verificar si una sección completa tiene al menos un ítem visible
    $hasVisibleItems = function (array $list) use ($canSeeItem): bool {
        foreach ($list as $item) {
            if ($canSeeItem($item)) {
                return true;
            }
        }
        return false;
    };

    // Estructuras de menús
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
        ['route' => 'admin.mecanicos.index', 'permission' => 'usuarios.ver',  'icon' => 'bi-tools',          'label' => 'Mecánicos'],
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
@endphp

<nav class="admin-sidebar__nav" aria-label="Menú principal">
    
    {{-- PRINCIPAL --}}
    @if (!$esMecanico)
        <div class="admin-sidebar__section">Principal</div>
        <ul class="list-unstyled m-0">
            <x-admin.sidebar-item
                :routeName="($esGerente && Route::has('gerente.dashboard')) ? 'gerente.dashboard' : 'admin.dashboard'"
                icon="bi-speedometer2"
                label="Dashboard" />
        </ul>
    @endif

    {{-- MÓDULO MECÁNICO --}}
    @if ($esMecanico && Route::has('mecanico.dashboard'))
        <div class="admin-sidebar__section">MECÁNICO</div>
        <ul class="list-unstyled m-0">
            @foreach ($mecanicoOp as $item)
                @if ($canSeeItem($item))
                    <x-admin.sidebar-item
                        :routeName="$item['route']"
                        :permission="$item['permission']"
                        :icon="$item['icon']"
                        :label="$item['label']" />
                @endif
            @endforeach
        </ul>
    @endif

    {{-- RESTO DEL MENÚ --}}
    @if (!$esMecanico)
        
        {{-- ORGANIZACIÓN (Exclusivo Administrador) --}}
        @if ($esAdmin && $hasVisibleItems($organizacion))
            <div class="admin-sidebar__section">Organización</div>
            <ul class="list-unstyled m-0">
                @foreach ($organizacion as $item)
                    @if ($canSeeItem($item))
                        <x-admin.sidebar-item
                            :routeName="$item['route']"
                            :permission="$item['permission'] ?? null"
                            :icon="$item['icon']"
                            :label="$item['label']" />
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- ATENCIÓN Y OPERACIÓN (Filtrado por permiso) --}}
        @if ($hasVisibleItems($atencion))
            <div class="admin-sidebar__section">Atención y operación</div>
            <ul class="list-unstyled m-0">
                @foreach ($atencion as $item)
                    @if ($canSeeItem($item))
                        <x-admin.sidebar-item
                            :routeName="$item['route']"
                            :permission="$item['permission'] ?? null"
                            :icon="$item['icon']"
                            :label="$item['label']" />
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- SERVICIOS E INVENTARIO (Filtrado por permiso) --}}
        @if ($hasVisibleItems($serviciosInventario))
            <div class="admin-sidebar__section">Servicios e inventario</div>
            <ul class="list-unstyled m-0">
                @foreach ($serviciosInventario as $item)
                    @if ($canSeeItem($item))
                        <x-admin.sidebar-item
                            :routeName="$item['route']"
                            :permission="$item['permission'] ?? null"
                            :icon="$item['icon']"
                            :label="$item['label']" />
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- FINANZAS Y CONTROL (Filtrado por permiso) --}}
        @if ($hasVisibleItems($finanzas))
            <div class="admin-sidebar__section">Finanzas y control</div>
            <ul class="list-unstyled m-0">
                @foreach ($finanzas as $item)
                    @if ($canSeeItem($item))
                        <x-admin.sidebar-item
                            :routeName="$item['route']"
                            :permission="$item['permission'] ?? null"
                            :icon="$item['icon']"
                            :label="$item['label']" />
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- SISTEMA (Exclusivo Administrador) --}}
        @if ($esAdmin && $hasVisibleItems($sistema))
            <div class="admin-sidebar__section">Sistema</div>
            <ul class="list-unstyled m-0">
                @foreach ($sistema as $item)
                    @if ($canSeeItem($item))
                        <x-admin.sidebar-item
                            :routeName="$item['route']"
                            :permission="$item['permission'] ?? null"
                            :icon="$item['icon']"
                            :label="$item['label']" />
                    @endif
                @endforeach
            </ul>
        @endif
    @endif
</nav>