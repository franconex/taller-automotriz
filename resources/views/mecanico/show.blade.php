@extends('layouts.admin')

@section('title', 'Atención de Orden #' . $orden->id)

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-uppercase text-muted fw-bold small">Atención y Operación</span>
            <h2 class="fw-bold mb-0">🔧 Atención de Orden #{{ $orden->id }}</h2>
        </div>
        <a href="{{ route('mecanico.mis_ordenes') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            ← Volver a mis órdenes
        </a>
    </div>

    <!-- Mensajes de Alerta -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Diagnóstico y Estado -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-dark text-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-pulse me-2"></i>Diagnóstico Técnico y Estado</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('mecanico.diagnostico', $orden->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Diagnóstico</label>
                            <textarea name="diagnostico" class="form-control rounded-3" rows="4" required placeholder="Escribe el diagnóstico del vehículo...">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Observaciones</label>
                            <textarea name="observaciones" class="form-control rounded-3" rows="2" placeholder="Observaciones adicionales...">{{ old('observaciones', $orden->observaciones) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Estado de la Orden</label>
                            <select name="estado" class="form-select rounded-3">
                                <option value="Pendiente" {{ $orden->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En Proceso" {{ $orden->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Completado" {{ $orden->estado == 'Completado' ? 'selected' : '' }}>Completado (Notificar Fin)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm rounded-3">
                             Guardar Diagnóstico y Estado
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Repuestos y Stock -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-danger text-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Registrar Repuesto Utilizado</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('mecanico.repuestos', $orden->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Seleccionar Repuesto</label>
                            <select name="repuesto_id" class="form-select rounded-3" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($repuestos as $repuesto)
                                    <option value="{{ $repuesto->id }}">
                                        {{ $repuesto->nombre }} (Stock: {{ $repuesto->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control rounded-3" value="1" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 shadow-sm rounded-3">
                            ➕ Agregar Repuesto y Descontar Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection