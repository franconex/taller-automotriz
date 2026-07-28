@extends('layouts.cliente-sidebar')

@section('title', 'Registrar vehículo')
@section('navbar-title', 'Registrar vehículo')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.vehiculos') }}" class="text-decoration-none small">&larr; Volver a mis vehículos</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Registrar nuevo vehículo</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.vehiculos.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Placa <span class="text-danger">*</span></label>
                        <input type="text" name="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa') }}" placeholder="Ej: ABC-123" required>
                        @error('placa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Marca <span class="text-danger">*</span></label>
                        <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}" placeholder="Ej: Toyota" required>
                        @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Modelo <span class="text-danger">*</span></label>
                        <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}" placeholder="Ej: Corolla" required>
                        @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Año</label>
                        <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio') }}" placeholder="Ej: 2020" min="1900" max="2099">
                        @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Color</label>
                        <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}" placeholder="Ej: Blanco">
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">N° Chasis</label>
                        <input type="text" name="numero_chasis" class="form-control @error('numero_chasis') is-invalid @enderror" value="{{ old('numero_chasis') }}" placeholder="Ej: 8AGCM56T8X1234567">
                        @error('numero_chasis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kilometraje actual</label>
                        <input type="number" name="kilometraje_actual" class="form-control @error('kilometraje_actual') is-invalid @enderror" value="{{ old('kilometraje_actual') }}" placeholder="Ej: 50000" min="0">
                        @error('kilometraje_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="2" placeholder="Detalles adicionales...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-save me-1"></i>Registrar vehículo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
