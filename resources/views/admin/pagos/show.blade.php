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
                        <dt>Factura</dt>
                        <dd>
                            <span>{{ $pago->comprobante->numero }}</span>
                            @if ($pago->comprobante->nit_ci)
                                <span class="text-muted small"> · NIT: {{ $pago->comprobante->nit_ci }}</span>
                            @endif
                            <a href="{{ route('admin.factura.show', $pago->comprobante) }}" target="_blank" class="btn btn-sm ms-2" style="border:1px solid #0B1D3A;border-radius:3px;font-size:.75rem;">
                                <i class="bi bi-printer"></i> Factura
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
            @php
                $orden = $pago->ordenTrabajo;
                $servicios = $orden?->serviciosMecanico ?? collect();
                $repuestos = $orden?->repuestosMecanico ?? collect();
                $manoDeObra = (float) ($orden?->autorizaciones->sum('mano_de_obra') ?? 0);
            @endphp

            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-tools me-1"></i>
                    Servicios realizados
                </h2>
                @if ($servicios->isNotEmpty())
                    <div class="small">
                        @foreach ($servicios as $s)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <i class="bi bi-tools text-secondary me-1"></i>
                                    {{ $s->nombre_servicio ?? $s->servicio?->nombre ?? 'Servicio' }}
                                    @if ($s->mecanico)
                                        <div class="text-muted" style="font-size:.8em;">
                                            <i class="bi bi-person-badge"></i>
                                            {{ $s->mecanico->empleado?->nombre_completo ?? '—' }}
                                        </div>
                                    @endif
                                </div>
                                <span class="fw-semibold ms-2">{{ number_format((float) $s->precio_base, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-muted small mb-0">No hay servicios registrados en esta orden.</p>
                @endif
            </div>

            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3">
                    <i class="bi bi-box-seam me-1"></i>
                    Repuestos / Piezas
                </h2>
                @if ($repuestos->isNotEmpty())
                    <div class="small">
                        @foreach ($repuestos as $r)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <i class="bi bi-box-seam text-secondary me-1"></i>
                                    {{ $r->repuesto?->nombre ?? 'Repuesto' }}
                                    <span class="text-muted"> x{{ (float) $r->cantidad }}</span>
                                </div>
                                <span class="fw-semibold ms-2">{{ number_format((float) $r->cantidad * (float) $r->precio_unitario_snapshot, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-muted small mb-0">No hay repuestos asignados.</p>
                @endif
            </div>

            @if ($manoDeObra > 0)
                <div class="admin-table-wrap p-4 mb-3">
                    <h2 class="h6 fw-bold mb-3">
                        <i class="bi bi-person-gear me-1"></i>
                        Mano de Obra
                    </h2>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span>Mano de obra</span>
                        <span class="fw-semibold">{{ number_format($manoDeObra, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endif

            <div class="admin-table-wrap p-4">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Totales:</span>
                    <span>
                        Servicios: {{ number_format((float) $servicios->sum('precio_base'), 2, ',', '.') }} |
                        Repuestos: {{ number_format((float) $repuestos->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot), 2, ',', '.') }}
                        @if ($manoDeObra > 0)
                            | Mano obra: {{ number_format($manoDeObra, 2, ',', '.') }}
                        @endif
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
