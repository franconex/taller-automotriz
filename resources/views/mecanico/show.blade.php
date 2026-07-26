<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Atender Orden #{{ $orden->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2> Atención de Orden #{{ $orden->id }}</h2>
            <a href="{{ route('mecanico.mis_ordenes') }}" class="btn btn-outline-secondary">⬅ Volver a mis órdenes</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Diagnóstico y Estado -->
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">Diagnóstico Técnico y Estado</div>
                    <div class="card-body">
                        <form action="{{ route('mecanico.diagnostico', $orden->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control" rows="3" required placeholder="Escribe el diagnóstico del vehículo...">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales...">{{ old('observaciones', $orden->observaciones) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estado de la Orden</label>
                                <select name="estado" class="form-select">
                                    <option value="Pendiente" {{ $orden->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="En Proceso" {{ $orden->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="Completado" {{ $orden->estado == 'Completado' ? 'selected' : '' }}>Completado (Notificar Fin)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100">💾 Guardar Diagnóstico y Estado</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Repuestos y Stock -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"> Registrar Repuesto Utilizado</div>
                    <div class="card-body">
                        <form action="{{ route('mecanico.repuestos', $orden->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Repuesto</label>
                                <select name="repuesto_id" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($repuestos as $repuesto)
                                        <option value="{{ $repuesto->id }}">
                                            {{ $repuesto->nombre }} (Stock: {{ $repuesto->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                            </div>

                            <button type="submit" class="btn btn-outline-primary w-100">➕ Agregar Repuesto y Descontar Stock</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>