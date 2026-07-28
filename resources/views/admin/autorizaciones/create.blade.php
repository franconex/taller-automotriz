@extends('layouts.admin')

@section('title', 'Nueva autorización')
@section('navbar-title', 'Nueva autorización')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.ordenes.show', $ordene) }}" class="text-decoration-none small">&larr; Volver a la orden</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Solicitar autorización — {{ $ordene->numero_orden }}</h5>
            <small class="text-muted">{{ $ordene->cliente?->nombre_completo }} · {{ $ordene->vehiculo?->placa }}</small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.autorizaciones.store', $ordene) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                               value="{{ old('titulo') }}" placeholder="Ej: Reparación de freno delantero" required>
                        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Importe adicional <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="importe" class="form-control @error('importe') is-invalid @enderror"
                                   value="{{ old('importe') }}" required>
                            @error('importe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="5" placeholder="Describe el trabajo adicional encontrado..." required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-send me-1"></i>Enviar solicitud al cliente
                        </button>
                        <a href="{{ route('admin.ordenes.show', $ordene) }}" class="btn btn-outline-secondary ms-2">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
