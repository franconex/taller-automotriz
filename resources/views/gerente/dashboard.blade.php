<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerente — Taller Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Panel de Gerente</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar sesión</button>
            </form>
        </div>
        <div class="alert alert-info">Bienvenido, {{ auth()->user()->nombre }}. Tu rol es <strong>Gerente</strong>.</div>
        <p class="text-muted">Dashboard en construcción.</p>
    </div>
</body>
</html>
