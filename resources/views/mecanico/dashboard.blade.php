<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mecánico — Taller Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Panel de Mecánico</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar sesión</button>
            </form>
        </div>

        <div class="alert alert-info">
            Bienvenido, {{ auth()->user()->nombre }}. Tu rol es <strong>Mecánico</strong>.
        </div>

        <!-- Acceso al módulo de Diana -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">🔧 Mis Órdenes Asignadas</h5>
                        <p class="card-text text-muted">
                            Revisa los vehículos a tu cargo, registra diagnósticos técnicos y asigna los repuestos utilizados con validación de stock.
                        </p>
                        <a href="{{ route('mecanico.mis_ordenes') }}" class="btn btn-primary">
                            Ir a mis órdenes ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>