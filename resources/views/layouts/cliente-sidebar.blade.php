<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Portal') — Taller Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-body">
    @php
        $usuario = Auth::user();
        $cliente = $usuario->cliente;
        $usuario->loadMissing('perfil');
        $fotoPerfil = $usuario->perfil?->foto_url;
        $partesNombre = array_filter(explode(' ', trim($cliente?->nombre_completo ?? $usuario->nombre ?? '')));
        $iniciales = mb_strtoupper(
            collect($partesNombre)->take(2)->map(fn($p) => mb_substr($p, 0, 1))->implode('')
        );
        if ($iniciales === '') { $iniciales = 'C'; }

        $navItems = [
            ['route' => 'cliente.dashboard',  'icon' => 'bi-speedometer2',  'label' => 'Inicio'],
            ['route' => 'cliente.vehiculos',  'icon' => 'bi-car-front',     'label' => 'Mis vehículos'],
            ['route' => 'cliente.citas.crear','icon' => 'bi-calendar-plus',  'label' => 'Agendar cita'],
            ['route' => 'cliente.citas',      'icon' => 'bi-calendar-check','label' => 'Mis citas'],
            ['route' => 'cliente.seguimiento','icon' => 'bi-clipboard-data','label' => 'Seguimiento'],
            ['route' => 'cliente.historial',  'icon' => 'bi-clock-history', 'label' => 'Historial'],
            ['route' => 'cliente.autorizaciones','icon' => 'bi-file-check','label' => 'Autorizaciones'],
            ['route' => 'cliente.pagos',      'icon' => 'bi-cash-coin',     'label' => 'Pagos y comprobantes'],
            ['route' => 'cliente.perfil',     'icon' => 'bi-person',        'label' => 'Mi perfil'],
        ];

        $currentRoute = Route::currentRouteName();
    @endphp

    <div class="admin-shell">
        {{-- SIDEBAR --}}
        <aside id="adminSidebar" class="admin-sidebar d-none d-lg-flex" aria-label="Menú del cliente">
            <div class="admin-sidebar__brand">
                <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro">
            </div>
            <div class="admin-sidebar__nav-wrap">
                <nav class="admin-sidebar__nav" aria-label="Navegación del cliente">
                    <div class="admin-sidebar__section">Portal del cliente</div>
                    <ul class="list-unstyled m-0">
                        @foreach ($navItems as $item)
                            <x-admin.sidebar-item
                                :routeName="$item['route']"
                                :icon="$item['icon']"
                                :label="$item['label']" />
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- MOBILE DRAWER --}}
        <aside id="adminMobileDrawer"
               class="offcanvas offcanvas-start admin-mobile-drawer d-lg-none"
               tabindex="-1"
               aria-labelledby="adminMobileDrawerLabel">
            <div class="offcanvas-header admin-mobile-drawer__header">
                <h2 class="offcanvas-title h6 mb-0" id="adminMobileDrawerLabel">Menú</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
            </div>
            <div class="offcanvas-body admin-mobile-drawer__body p-0">
                <div class="admin-mobile-drawer__brand">
                    <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro">
                </div>
                <nav class="admin-sidebar__nav" aria-label="Navegación del cliente">
                    <div class="admin-sidebar__section">Portal del cliente</div>
                    <ul class="list-unstyled m-0">
                        @foreach ($navItems as $item)
                            <x-admin.sidebar-item
                                :routeName="$item['route']"
                                :icon="$item['icon']"
                                :label="$item['label']" />
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="admin-content">
            <nav class="admin-navbar" aria-label="Barra superior">
                <button class="btn admin-navbar__toggle d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#adminMobileDrawer"
                        aria-controls="adminMobileDrawer"
                        aria-label="Abrir menú">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <button class="btn admin-navbar__toggle d-none d-lg-inline-flex"
                        type="button"
                        id="adminSidebarToggle"
                        aria-controls="adminSidebar"
                        aria-label="Plegar o desplegar menú">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>

                <div class="admin-navbar__title">
                    @hasSection('navbar-title')
                        @yield('navbar-title')
                    @else
                        @yield('title', 'Mi Portal')
                    @endif
                </div>

                <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                    <x-notificaciones-campana />
                    <div class="dropdown">
                        <button class="admin-navbar__user dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-label="Menú de usuario">
                            @if ($fotoPerfil)
                                <img src="{{ $fotoPerfil }}" alt="" class="admin-avatar admin-avatar--img" aria-hidden="true">
                            @else
                                <span class="admin-avatar" aria-hidden="true">{{ $iniciales }}</span>
                            @endif
                            <span class="d-none d-sm-inline">
                                <span class="admin-navbar__user-name d-block">{{ $cliente?->nombre_completo ?? $usuario->nombre }}</span>
                                <span class="admin-navbar__user-role d-block">Cliente</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end admin-dropdown">
                            <li class="dropdown-header">{{ $cliente?->email ?? $usuario->email }}</li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('cliente.perfil') }}">
                                    <i class="bi bi-person" aria-hidden="true"></i>
                                    Mi perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                        Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main id="adminMain" class="admin-main" tabindex="-1">
                <x-admin.toast />
                <x-admin.flash-message />

                @hasSection('breadcrumb')
                    <nav aria-label="Ruta de navegación">
                        <ol class="admin-breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
