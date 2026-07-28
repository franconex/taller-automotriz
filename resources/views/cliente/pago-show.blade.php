@extends('layouts.cliente-sidebar')

@section('title', 'Pago')
@section('navbar-title', 'Detalle de pago')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.pagos') }}" class="text-decoration-none small">&larr; Volver a pagos</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Pago — ${{ number_format($pago->monto, 2) }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Orden</h6>
                    <p>{{ $pago->ordenTrabajo?->numero_orden ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Vehículo</h6>
                    <p>{{ $pago->ordenTrabajo?->vehiculo?->placa ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Fecha</h6>
                    <p>{{ $pago->fecha_pago?->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Método</h6>
                    <p>{{ $pago->metodoPago?->nombre ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Estado</h6>
                    <span class="badge bg-success">{{ ucfirst($pago->estado) }}</span>
                </div>
                @if ($pago->referencia)
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase">Referencia</h6>
                        <p>{{ $pago->referencia }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
