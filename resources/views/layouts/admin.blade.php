<!DOCTYPE html>
<html lang="es" x-data="theme()" x-init="init" :class="resolved === 'dark' ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Panel') - Taller Pro</title>
    <x-admin.vite-assets :entry="['resources/css/app.css', 'resources/js/app.js']" />
</head>
<body class="antialiased font-sans" style="background-color: var(--color-bg); color: var(--color-text);">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-950/40 lg:hidden" @click="sidebarOpen = false"></div>

        @include('components.admin.sidebar')

        <div class="lg:pl-64">
            @include('components.admin.navbar')

            <main class="min-h-[calc(100vh-64px)] p-5 sm:p-6 lg:p-8">
                @if (session('success'))
                    <x-admin.alert type="success" dismissible>{{ session('success') }}</x-admin.alert>
                @endif
                @if (session('error'))
                    <x-admin.alert type="error" dismissible>{{ session('error') }}</x-admin.alert>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
