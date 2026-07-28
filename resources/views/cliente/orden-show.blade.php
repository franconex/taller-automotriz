@extends('layouts.cliente-sidebar')

@section('title', "Orden {$ordene->numero_orden}")
@section('navbar-title', "Orden {$ordene->numero_orden}")

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.historial') }}" class="text-decoration-none small">&larr; Volver al historial</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $ordene->numero_orden }}</h5>
                    <span class="badge fs-6 bg-{{ $ordene->estado === 'finalizada' || $ordene->estado === 'entregada' ? 'success' : 'danger' }}">
                        {{ ucfirst(str_replace('_', ' ', $ordene->estado)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase">Vehículo</h6>
                            <p class="fw-semibold">{{ $ordene->vehiculo?->placa ?? '—' }}</p>
                            <small class="text-muted">{{ $ordene->vehiculo?->marca ?? '' }} {{ $ordene->vehiculo?->modelo ?? '' }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase">Fechas</h6>
                            <p class="mb-0"><strong>Emisión:</strong> {{ $ordene->fecha_emision?->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Inicio:</strong> {{ $ordene->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
                            <p class="mb-0"><strong>Fin:</strong> {{ $ordene->fecha_fin?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted small text-uppercase">Problema reportado</h6>
                            <p>{{ $ordene->descripcion_problema ?? '—' }}</p>
                        </div>
                        @if ($ordene->diagnostico_general)
                            <div class="col-12">
                                <h6 class="text-muted small text-uppercase">Diagnóstico</h6>
                                <p>{{ $ordene->diagnostico_general }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SERVICIOS Y REPUESTOS --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Servicios y repuestos</h6>
                </div>
                <div class="card-body p-0">
                    @if ($ordene->detalles->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Cant.</th>
                                        <th>P. Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordene->detalles as $d)
                                        <tr>
                                            <td><span class="badge bg-{{ $d->tipo === 'servicio' ? 'info' : 'secondary' }}">{{ ucfirst($d->tipo) }}</span></td>
                                            <td>{{ $d->descripcion ?: ($d->servicio?->nombre ?: $d->repuesto?->nombre ?: '—') }}</td>
                                            <td>{{ $d->cantidad ?? 1 }}</td>
                                            <td>${{ number_format($d->precio_unitario, 2) }}</td>
                                            <td>${{ number_format($d->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted small">Sin detalles</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- PAGOS --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Pagos</h6>
                </div>
                <div class="card-body">
                    @if ($pagos->isNotEmpty())
                        @foreach ($pagos as $p)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <small class="d-block fw-semibold">${{ number_format($p->monto, 2) }}</small>
                                    <small class="text-muted">{{ $p->fecha_pago?->format('d/m/Y') }} · {{ $p->metodoPago?->nombre ?? '—' }}</small>
                                </div>
                                @if ($p->comprobante)
                                    <a href="{{ route('cliente.comprobante-show', $p->comprobante) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between fw-bold mt-2">
                            <span>Total pagado</span>
                            <span>${{ number_format($pagos->sum('monto'), 2) }}</span>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Sin pagos registrados</p>
                    @endif
                </div>
            </div>

            @if ($comprobante)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">Comprobante</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>N°:</strong> {{ $comprobante->numero }}</p>
                        <p class="mb-0"><strong>Total:</strong> ${{ number_format($comprobante->monto_total, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
