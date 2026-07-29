@extends('layouts.admin')

@section('title', 'Pago #' . $pago->id)
@section('navbar-title', 'Pago #' . $pago->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li class="active" aria-current="page">#{{ $pago->id }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Pago #' . $pago->id"
        :description="$pago->ordenTrabajo->numero_orden ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.pagos.edit', $pago) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Datos del pago
                </h2>
                <dl class="admin-meta">
                    <dt>Fecha</dt><dd>{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Monto</dt><dd><strong>{{ number_format((float) $pago->monto, 2, ',', '.') }}</strong></dd>
                    <dt>Método</dt>
                    <dd>
                        @php
                            $metodoNombre = strtolower($pago->metodoPago->nombre ?? '');
                            $metodoIcono = match(true) {
                                str_contains($metodoNombre, 'efectivo') => 'bi-cash-stack',
                                str_contains($metodoNombre, 'tarjeta') => 'bi-credit-card-2-front',
                                str_contains($metodoNombre, 'qr') => 'bi-qr-code-scan',
                                default => 'bi-coin',
                            };
                        @endphp
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="bi {{ $metodoIcono }} text-secondary"></i>
                            {{ $pago->metodoPago->nombre ?? '—' }}
                        </span>
                    </dd>
                    <dt>Orden</dt>
                    <dd>
                        @if ($pago->ordenTrabajo)
                            <a href="{{ route('admin.ordenes.show', $pago->ordenTrabajo) }}">{{ $pago->ordenTrabajo->numero_orden }}</a>
                        @else — @endif
                    </dd>
                    <dt>Cliente</dt><dd>{{ $pago->ordenTrabajo->cliente->nombre_completo ?? '—' }}</dd>
                    <dt>Comprobante</dt><dd>{{ $pago->numero_comprobante ?? '—' }}</dd>
                    @if ($pago->comprobante)
                        <dt>Comprobante fiscal</dt>
                        <dd>
                            <a href="{{ route('admin.comprobantes.show', $pago->comprobante) }}" class="d-inline-flex align-items-center gap-1">
                                <i class="bi bi-receipt"></i>
                                {{ $pago->comprobante->numero }}
                            </a>
                        </dd>
                    @endif
                    <dt>Referencia</dt><dd>{{ $pago->referencia ?? '—' }}</dd>
                    <dt>Registrado por</dt><dd>{{ $pago->usuario->nombre ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$pago->estado === 'confirmado' ? 'success' : 'danger'"
                            :icon="$pago->estado === 'confirmado' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"
                            :label="ucfirst($pago->estado)" />
                    </dd>
                </dl>
                @if ($pago->metodoPago && strcasecmp($pago->metodoPago->nombre, 'QR') === 0)
                    <hr>
                    <a href="{{ route('admin.pagos.qr', $pago) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-qr-code" aria-hidden="true"></i>
                        Ver código QR
                    </a>
                @endif
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-tools me-1"></i>
                    Servicios realizados
                </h2>
                @php
                    $servicios = $pago->ordenTrabajo?->detalles?->where('tipo', 'servicio') ?? collect();
                @endphp
                @if ($servicios->isNotEmpty())
                    <div class="small">
                        @foreach ($servicios as $d)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <i class="bi bi-tools text-secondary me-1"></i>
                                    {{ $d->descripcion ?: ($d->servicio->nombre ?? 'Servicio') }}
                                    <div class="text-muted" style="font-size:.8em;">
                                        <i class="bi bi-person-badge"></i>
                                        {{ $d->asignacionTrabajo?->mecanico?->empleado?->nombre_completo ?? '—' }}
                                    </div>
                                </div>
                                <span class="fw-semibold ms-2">{{ number_format((float) $d->subtotal, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-muted small mb-0">No hay servicios registrados en esta orden.</p>
                @endif

                <hr class="my-3">

                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-box-seam me-1"></i>
                    Repuestos
                </h2>
                @php
                    $repuestos = $pago->ordenTrabajo?->detalles?->where('tipo', 'repuesto') ?? collect();
                @endphp
                @if ($repuestos->isNotEmpty())
                    <div class="small">
                        @foreach ($repuestos as $d)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <i class="bi bi-box-seam text-secondary me-1"></i>
                                    {{ $d->descripcion ?: ($d->repuesto->nombre ?? 'Repuesto') }}
                                    <span class="text-muted"> x{{ (int) $d->cantidad }}</span>
                                </div>
                                <span class="fw-semibold ms-2">{{ number_format((float) $d->subtotal, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-muted small mb-0">No hay repuestos asignados.</p>
                @endif

                <hr class="my-3">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Totales:</span>
                    <span>
                        Servicios: {{ number_format((float) ($pago->ordenTrabajo->subtotal_servicios ?? 0), 2, ',', '.') }} |
                        Repuestos: {{ number_format((float) ($pago->ordenTrabajo->subtotal_repuestos ?? 0), 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-journal-text me-1"></i>
                    Observaciones
                </h2>
                @if ($pago->observaciones)
                    <p class="cell-muted small mb-0">{{ $pago->observaciones }}</p>
                @else
                    <p class="cell-muted small mb-0">Sin observaciones.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
