<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Órdenes Asignadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🔧 Mis Órdenes Asignadas</h2>
            <a href="{{ route('mecanico.dashboard') }}" class="btn btn-outline-secondary">⬅ Ir al Dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th># Orden</th>
                            <th>Vehículo</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                            <tr>
                                <td><strong>#{{ $orden->id }}</strong></td>
                                <td>{{ $orden->vehiculo->placa ?? 'Sin placa' }}</td>
                                <td>{{ $orden->cliente->nombre ?? 'General' }}</td>
                                <td>
                                    <span class="badge bg-{{ $orden->estado == 'Completado' ? 'success' : 'warning' }}">
                                        {{ $orden->estado ?? 'Pendiente' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('mecanico.atender', $orden->id) }}" class="btn btn-sm btn-primary">Atender Orden ➔</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay órdenes asignadas en este momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>