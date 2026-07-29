<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" id="stripe-key" content="{{ config('stripe.key') }}">
    <title>@yield('title', 'Panel') — Taller Pro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'])
    @stack('styles')
</head>
<body class="admin-body">
    <a class="visually-hidden-focusable" href="#adminMain">Saltar al contenido principal</a>

    @php
        $usuario = Auth::user();
        $rolNombre = $usuario->rol->nombre ?? 'Sin rol';
        $empleado = $usuario->empleado;
        $usuario->loadMissing('perfil');
        $fotoPerfil = $usuario->perfil?->foto_url;
        $partesNombre = array_filter(explode(' ', trim($usuario->nombre ?? '')));
        $iniciales = mb_strtoupper(
            collect($partesNombre)->take(2)->map(fn($p) => mb_substr($p, 0, 1))->implode('')
        );
        if ($iniciales === '') { $iniciales = 'U'; }
        $sucursalesDisponibles = \App\Models\Sucursal::where('estado', true)->orderBy('nombre')->get();
        $sucursalActiva = $usuario->sucursal ?? $sucursalesDisponibles->firstWhere('id', session('admin_sucursal_id'));
    @endphp

    <div class="admin-shell">

        {{-- DESKTOP SIDEBAR (always visible on >= lg) --}}
        <aside id="adminSidebar" class="admin-sidebar d-none d-lg-flex" aria-label="Menú principal">
            <div class="admin-sidebar__brand">
                <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro">
            </div>
            <div class="admin-sidebar__nav-wrap">
                @if ($usuario->tieneRol('Mecánico'))
                    @include('layouts.partials.sidebar-mecanico')
                @else
                    @include('layouts.partials.sidebar-menu')
                @endif
            </div>
        </aside>

        {{-- MOBILE DRAWER (offcanvas, only on < lg) --}}
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
                @if ($usuario->tieneRol('Mecánico'))
                    @include('layouts.partials.sidebar-mecanico')
                @else
                    @include('layouts.partials.sidebar-menu')
                @endif
                </div>
            </aside>

        <div class="admin-content">
            <nav class="admin-navbar" aria-label="Barra superior">
                {{-- Mobile hamburger: opens offcanvas drawer --}}
                <button class="btn admin-navbar__toggle d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#adminMobileDrawer"
                        aria-controls="adminMobileDrawer"
                        aria-label="Abrir menú">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>

                {{-- Desktop collapse: toggles icon-only sidebar --}}
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
                        @yield('title', 'Panel')
                    @endif
                </div>

                <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                    <x-notificaciones-campana />
                    @if ($usuario->sucursal_id === null || $usuario->tieneRol('Administrador') || $usuario->tieneRol('Gerente'))
                        <div class="dropdown d-none d-md-inline-flex">
                            <button class="admin-navbar__branch dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Sucursal activa">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                {{ $sucursalActiva?->nombre ?? 'Todas las sucursales' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end admin-dropdown">
                                <li>
                                    <form method="POST" action="{{ route('admin.sucursales.seleccionar') }}">
                                        @csrf
                                        <input type="hidden" name="sucursal_id" value="">
                                        <button type="submit" class="dropdown-item {{ session('admin_sucursal_id') === null ? 'active' : '' }}">
                                            <i class="bi bi-building"></i> Todas las sucursales
                                        </button>
                                    </form>
                                </li>
                                @foreach ($sucursalesDisponibles as $s)
                                    <li>
                                        <form method="POST" action="{{ route('admin.sucursales.seleccionar') }}">
                                            @csrf
                                            <input type="hidden" name="sucursal_id" value="{{ $s->id }}">
                                            <button type="submit" class="dropdown-item {{ (int) session('admin_sucursal_id') === (int) $s->id ? 'active' : '' }}">
                                                <i class="bi bi-building"></i> {{ $s->nombre }}
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif ($usuario->sucursal)
                        <span class="admin-navbar__branch d-none d-md-inline-flex" title="Sucursal activa">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            {{ $usuario->sucursal->nombre }}
                        </span>
                    @endif

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
                                <span class="admin-navbar__user-name d-block">{{ $usuario->nombre }}</span>
                                <span class="admin-navbar__user-role d-block">{{ $rolNombre }}</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end admin-dropdown">
                            <li class="dropdown-header">{{ $usuario->email }}</li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.perfil.index') }}">
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

    <button class="admin-fab" id="fabRegistrarPago" title="Registrar pago rápido" aria-label="Registrar pago">
        <i class="bi bi-cash-coin" aria-hidden="true"></i>
    </button>

    @stack('modals')
    @stack('offcanvas')
    @stack('scripts')
    <script>
    // Búsqueda en selects con filtro: oculta options que no coinciden
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.search-select-input').forEach(function(input) {
            input.addEventListener('keyup', function() {
                var targetId = this.dataset.target;
                var select = document.getElementById(targetId);
                if (!select) return;
                var q = this.value.toLowerCase();
                Array.from(select.options).forEach(function(opt) {
                    if (!opt.value) return;
                    opt.hidden = !opt.text.toLowerCase().includes(q);
                });
            });
        });

        var fab = document.getElementById('fabRegistrarPago');
        if (fab) {
            fab.addEventListener('click', function () {
                if (typeof window.TPPago !== 'undefined' && window.TPPago?.abrirModal) {
                    window.TPPago.abrirModal();
                } else {
                    window.location.href = '{{ route("admin.pagos.index") }}';
                }
            });
        }
    });
    </script>
</body>
</html>


