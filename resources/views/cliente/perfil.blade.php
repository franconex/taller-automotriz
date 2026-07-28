@extends('layouts.cliente-sidebar')

@section('title', 'Mi perfil')
@section('navbar-title', 'Mi perfil')

@section('content')
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Datos personales</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">Nombre completo</h6>
                        <p class="fw-semibold">{{ $cliente->nombre_completo ?? '—' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">CI</h6>
                        <p>{{ $cliente->ci ?? '—' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">Email</h6>
                        <p>{{ $cliente->email ?? '—' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">Teléfono</h6>
                        <p>{{ $cliente->telefono ?? '—' }}</p>
                    </div>
                    <div class="mb-0">
                        <h6 class="text-muted small text-uppercase">Dirección</h6>
                        <p>{{ $cliente->direccion ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Editar información</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cliente.perfil.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="telefono" class="form-label small fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" placeholder="Nuevo teléfono">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label small fw-semibold">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" placeholder="Nueva dirección">
                        </div>
                        <button type="submit" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-save me-1"></i>Actualizar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
