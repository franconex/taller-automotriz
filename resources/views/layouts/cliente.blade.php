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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('cliente.dashboard') }}">Taller Pro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clienteNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="clienteNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-light">{{ Auth::user()->nombre }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-danger">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
