@extends('layouts.cliente-sidebar')

@section('title', 'Pagos')
@section('navbar-title', 'Pagos')

@section('content')
    @if ($ordenesConPagos->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-cash-coin display-4 d-block mb-3"></i>
            <p>No tienes pagos registrados.</p>
        </div>
    @else
        @foreach ($ordenesConPagos as $orden)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $orden->numero_orden }}</h6>
                        <small class="text-muted">{{ $orden->vehiculo?->placa ?? '—' }}</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    @foreach ($orden->pagos as $pago)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">${{ number_format($pago->monto, 2) }}</div>
                                <small class="text-muted">{{ $pago->fecha_pago?->format('d/m/Y') }} · {{ $pago->metodoPago?->nombre ?? '—' }}</small>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-success">{{ ucfirst($pago->estado) }}</span>
                                @if ($pago->comprobante)
                                    <a href="{{ route('cliente.comprobante-show', $pago->comprobante) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-text"></i> Comprobante
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
@endsection
