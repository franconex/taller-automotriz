<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Panel Administrativo') - Taller Pro
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900 antialiased">
    <div
        x-data="{ sidebarOpen: false }"
        class="min-h-screen"
    >
        {{-- Fondo oscuro para celular --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        @include('components.admin.sidebar')

        <div class="lg:pl-72">
            {{-- Navbar --}}
            @include('components.admin.navbar')

            {{-- Contenido principal --}}
            <main class="min-h-[calc(100vh-80px)] p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
                    >
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer
                class="border-t border-gray-200 bg-white px-6 py-4 text-center text-sm text-gray-500"
            >
                © {{ date('Y') }} Taller Pro. Sistema de gestión automotriz.
            </footer>
        </div>
    </div>
</body>
</html>
