@extends('layouts.admin')

@section('title', 'Autorización')
@section('navbar-title', 'Autorización #' . $autorizacione->id)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.autorizaciones.index') }}" class="text-decoration-none small">&larr; Volver</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $autorizacione->titulo }}</h5>
                    <span class="badge fs-6 bg-{{ $autorizacione->estado === 'autorizada' ? 'success' : ($autorizacione->estado === 'rechazada' || $autorizacione->estado === 'cancelada' ? 'danger' : ($autorizacione->estado === 'pendiente' ? 'warning' : 'info')) }}">
                        {{ $autorizacione->estado_label }}
                    </span>
                </div>
                <div class="card-body">
                    <p>{{ $autorizacione->descripcion }}</p>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Importe</h6>
                            <p class="fw-bold fs-5" style="color:#E31E24;">${{ number_format($autorizacione->importe, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Solicitado por</h6>
                            <p>{{ $autorizacione->usuarioSolicitante?->nombre ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Fecha</h6>
                            <p>{{ $autorizacione->fecha_solicitud?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Orden</h6>
                            <p>{{ $autorizacione->ordenTrabajo?->numero_orden ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Cliente</h6>
                            <p>{{ $autorizacione->ordenTrabajo?->cliente?->nombre_completo ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Vehículo</h6>
                            <p>{{ $autorizacione->ordenTrabajo?->vehiculo?->placa ?? '—' }}</p>
                        </div>
                    </div>

                    @if ($autorizacione->comentario_cliente)
                        <hr>
                        <h6 class="text-muted small text-uppercase">Comentario del cliente</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $autorizacione->comentario_cliente }}</p>
                        </div>
                    @endif

                    @if ($autorizacione->respondidoPor)
                        <hr>
                        <h6 class="text-muted small text-uppercase">Respondido por</h6>
                        <p>{{ $autorizacione->respondidoPor?->nombre }} — {{ $autorizacione->fecha_respuesta?->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if (! $autorizacione->esFinal())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">Acciones</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.autorizaciones.cancelar', $autorizacione) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('¿Cancelar esta solicitud?')">
                                <i class="bi bi-x-lg me-1"></i>Cancelar solicitud
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
