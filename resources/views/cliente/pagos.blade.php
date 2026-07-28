@extends('layouts.cliente-sidebar')

@section('title', 'Pagos')
@section('navbar-title', 'Pagos')

@section('content')
    {{-- ÓRDENES CON SALDO PENDIENTE --}}
    @if ($ordenesPendientes->isNotEmpty())
        <h6 class="fw-bold mb-3"><i class="bi bi-credit-card"></i> Órdenes con saldo pendiente</h6>
        @foreach ($ordenesPendientes as $orden)
            @php
                $pagado = $orden->pagos()->where('estado', 'confirmado')->sum('monto');
                $saldo = max(0, (float) $orden->total_general - (float) $pagado);
            @endphp
            <div class="card border-0 shadow-sm mb-3" style="border-left:3px solid var(--tp-warning) !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $orden->numero_orden }}</strong>
                            <div class="small text-muted">{{ $orden->vehiculo?->placa ?? '—' }} · {{ $orden->vehiculo?->marca ?? '' }} {{ $orden->vehiculo?->modelo ?? '' }}</div>
                        </div>
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    </div>
                    <div class="row g-2 small mb-2">
                        <div class="col-4"><span class="text-muted">Total:</span> <strong>${{ number_format($orden->total_general, 2) }}</strong></div>
                        <div class="col-4"><span class="text-muted">Pagado:</span> <strong>${{ number_format($pagado, 2) }}</strong></div>
                        <div class="col-4"><span class="text-muted">Saldo:</span> <strong class="text-danger">${{ number_format($saldo, 2) }}</strong></div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-primary disabled" title="Próximamente"><i class="bi bi-qr-code"></i> Pagar con QR</a>
                        <a href="#" class="btn btn-sm btn-outline-success disabled" title="Próximamente"><i class="bi bi-cash-coin"></i> Efectivo</a>
                        <a href="#" class="btn btn-sm btn-outline-info disabled" title="Próximamente"><i class="bi bi-credit-card"></i> Tarjeta</a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- HISTORIAL DE PAGOS CONFIRMADOS --}}
    <h6 class="fw-bold mb-3 mt-4"><i class="bi bi-receipt"></i> Historial de pagos</h6>
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
