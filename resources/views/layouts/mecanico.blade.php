<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Mecánico') — Taller Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root {
            --mec-sidebar: #0f1219;
            --mec-sidebar-hover: #1a1f2e;
            --mec-sidebar-active: #2563eb;
            --mec-header-bg: #1a1f2e;
            --mec-body-bg: #f1f5f9;
            --mec-card-bg: #ffffff;
            --mec-text: #1e293b;
            --mec-text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--mec-body-bg);
            color: var(--mec-text);
            display: flex;
            min-height: 100vh;
        }
        .mec-sidebar {
            width: 220px;
            background: var(--mec-sidebar);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            transition: transform .2s;
        }
        .mec-sidebar__brand {
            padding: 20px 18px 14px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .mec-sidebar__brand img { height: 32px; }
        .mec-sidebar__nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
        .mec-sidebar__nav ul { list-style: none; }
        .mec-sidebar__nav li { margin-bottom: 2px; }
        .mec-sidebar__nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: all .15s;
        }
        .mec-sidebar__nav a:hover { background: var(--mec-sidebar-hover); color: #fff; }
        .mec-sidebar__nav a.active { background: var(--mec-sidebar-active); color: #fff; }
        .mec-sidebar__nav a i { font-size: 1.15rem; width: 20px; text-align: center; }
        .mec-sidebar__section {
            padding: 16px 12px 6px;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.25);
            font-weight: 600;
        }
        .mec-sidebar__footer {
            padding: 12px 10px 16px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .mec-sidebar__footer a { color: rgba(255,255,255,.4); }
        .mec-sidebar__footer a:hover { color: #ef4444; }
        .mec-content {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .mec-header {
            background: var(--mec-card-bg);
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .mec-header__title { font-size: 1rem; font-weight: 600; flex: 1; }
        .mec-header__user { display: flex; align-items: center; gap: 8px; }
        .mec-header__avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #2563eb; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700;
        }
        .mec-header__name { font-size: .85rem; font-weight: 500; }
        .mec-main {
            flex: 1;
            padding: 24px;
        }
        .mec-mobile-toggle {
            display: none;
            background: none; border: none; font-size: 1.25rem;
            color: var(--mec-text); cursor: pointer; padding: 4px;
        }
        @media (max-width: 768px) {
            .mec-sidebar { transform: translateX(-100%); }
            .mec-sidebar.open { transform: translateX(0); }
            .mec-content { margin-left: 0; }
            .mec-mobile-toggle { display: inline-flex; }
        }
    </style>
</head>
<body>
    @php
        $usuario = Auth::user();
        $iniciales = collect(array_filter(explode(' ', trim($usuario->nombre ?? ''))))->take(2)
            ->map(fn($p) => mb_substr($p, 0, 1))
            ->map(fn($l) => mb_strtoupper($l))
            ->implode('');
        if ($iniciales === '') $iniciales = 'M';
    @endphp

    <aside class="mec-sidebar" id="mecSidebar">
        <div class="mec-sidebar__brand">
            <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro">
        </div>
        @include('layouts.partials.sidebar-mecanico')
        <div class="mec-sidebar__footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" onclick="this.closest('form').submit(); return false;">
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </a>
            </form>
        </div>
    </aside>

    <div class="mec-content">
        <header class="mec-header">
            <button class="mec-mobile-toggle" onclick="document.getElementById('mecSidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <div class="mec-header__title">@yield('title', 'Panel Mecánico')</div>
            <x-notificaciones-campana />
            <div class="mec-header__user">
                <span class="mec-header__name">{{ $usuario->nombre }}</span>
                <span class="mec-header__avatar">{{ $iniciales }}</span>
            </div>
        </header>

        <main class="mec-main">
            <x-admin.toast />
            <x-admin.flash-message />
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var links = document.querySelectorAll('.mec-sidebar__nav a');
        var current = window.location.pathname;
        links.forEach(function (a) {
            if (a.getAttribute('href') === current) a.classList.add('active');
        });
    });
    </script>
</body>
</html>
