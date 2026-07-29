@extends('layouts.cliente-sidebar')

@section('title', 'Comprobante')
@section('navbar-title', 'Comprobante')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.pagos') }}" class="text-decoration-none small">&larr; Volver a pagos</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Comprobante N° {{ $comprobante->numero }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Orden</h6>
                    <p>{{ $comprobante->pago?->ordenTrabajo?->numero_orden ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Vehículo</h6>
                    <p>{{ $comprobante->pago?->ordenTrabajo?->vehiculo?->placa ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Fecha de emisión</h6>
                    <p>{{ $comprobante->fecha_emision?->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Monto total</h6>
                    <p class="fw-bold fs-5" style="color:#E31E24;">${{ number_format($comprobante->monto_total, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">NIT/CI</h6>
                    <p>{{ $comprobante->nit_ci ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Razón social</h6>
                    <p>{{ $comprobante->razon_social ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Método de pago</h6>
                    <p>{{ $comprobante->pago?->metodoPago?->nombre ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase">Estado</h6>
                    <span class="badge bg-success">{{ ucfirst($comprobante->estado) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
