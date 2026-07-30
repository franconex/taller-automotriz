@extends('layouts.admin')

@section('title', 'Pago #' . $pago->id)
@section('navbar-title', 'Pago #' . $pago->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li class="active" aria-current="page">#{{ $pago->id }}</li>
@endsection

@section('content')
    <x-admin.page-header :title="'Pago #' . $pago->id" :description="$pago->ordenTrabajo->numero_orden ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
            <a href="{{ route('admin.pagos.edit', $pago) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Editar</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-info-circle"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos del pago</h2>
                </div>
                @php
                    $mn = strtolower($pago->metodoPago->nombre ?? '');
                    $mi = match(true) { str_contains($mn,'efectivo') => 'bi-cash-stack', str_contains($mn,'tarjeta') => 'bi-credit-card-2-front', str_contains($mn,'qr') => 'bi-qr-code-scan', default => 'bi-coin' };
                @endphp
                <dl class="admin-meta">
                    <dt>Fecha</dt><dd>{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Monto</dt><dd class="fw-bold" style="font-size:1.3rem;">Bs. {{ number_format((float) $pago->monto, 2, ',', '.') }}</dd>
                    <dt>Método</dt><dd><span class="cell-label"><i class="{{ $mi }}"></i> {{ $pago->metodoPago->nombre ?? '—' }}</span></dd>
                    <dt>Orden</dt>
                    <dd>@if($pago->ordenTrabajo)<a href="{{ route('admin.ordenes.show', $pago->ordenTrabajo) }}">{{ $pago->ordenTrabajo->numero_orden }}</a>@else—@endif</dd>
                    <dt>Cliente</dt><dd>{{ $pago->ordenTrabajo->cliente->nombre_completo ?? '—' }}</dd>
                    <dt>Comprobante</dt><dd>{{ $pago->numero_comprobante ?? '—' }}</dd>
                    @if ($pago->comprobante)
                        <dt>Factura</dt>
                        <dd><span>{{ $pago->comprobante->numero }}</span>@if($pago->comprobante->nit_ci)<span class="cell-secondary small"> · NIT: {{ $pago->comprobante->nit_ci }}</span>@endif<a href="{{ route('admin.factura.show', $pago->comprobante) }}" target="_blank" class="btn btn-sm ms-2" style="border:1px solid #0B1D3A;border-radius:3px;font-size:.75rem;"><i class="bi bi-printer"></i> Factura</a></dd>
                    @endif
                    <dt>Referencia</dt><dd>{{ $pago->referencia ?? '—' }}</dd>
                    <dt>Registrado por</dt><dd>{{ $pago->usuario->nombre ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd><x-admin.status-badge :tone="$pago->estado==='confirmado' ? 'success' : 'danger'" :icon="$pago->estado==='confirmado' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'" :label="ucfirst($pago->estado)" /></dd>
                </dl>
                @if ($pago->metodoPago && strcasecmp($pago->metodoPago->nombre,'QR')===0)
                    <hr>
                    <a href="{{ route('admin.pagos.qr', $pago) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-qr-code"></i> Ver código QR</a>
                @endif
            </div>
            @if ($pago->observaciones)
            <div class="admin-card-module mt-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-chat-left-text"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Observaciones</h2>
                </div>
                <p class="cell-secondary small mb-0">{{ $pago->observaciones }}</p>
            </div>
            @endif
        </div>
        <div class="col-12 col-lg-6">
            @php $orden = $pago->ordenTrabajo; $servicios = $orden?->serviciosMecanico ?? collect(); $repuestos = $orden?->repuestosMecanico ?? collect(); $manoDeObra = (float)($orden?->autorizaciones->sum('mano_de_obra')??0); @endphp

            <div class="admin-card-module mb-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-tools"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Servicios realizados</h2>
                </div>
                @if ($servicios->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach ($servicios as $s)
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;">
                                <div><i class="bi bi-tools me-1" style="color:#64748b;"></i> <strong>{{ $s->nombre_servicio ?? $s->servicio?->nombre ?? 'Servicio' }}</strong>@if($s->mecanico)<div class="cell-secondary small"><i class="bi bi-person-badge"></i> {{ $s->mecanico->empleado?->nombre_completo ?? '—' }}</div>@endif</div>
                                <span class="fw-semibold">{{ number_format((float)$s->precio_base,2,',','.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-secondary small mb-0">No hay servicios registrados en esta orden.</p>
                @endif
            </div>

            <div class="admin-card-module mb-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-box-seam"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Repuestos / Piezas</h2>
                </div>
                @if ($repuestos->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach ($repuestos as $r)
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;">
                                <div><i class="bi bi-box-seam me-1" style="color:#64748b;"></i> <strong>{{ $r->repuesto?->nombre ?? 'Repuesto' }}</strong><span class="cell-secondary"> x{{ (float)$r->cantidad }}</span></div>
                                <span class="fw-semibold">{{ number_format((float)$r->cantidad*(float)$r->precio_unitario_snapshot,2,',','.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="cell-secondary small mb-0">No hay repuestos asignados.</p>
                @endif
            </div>

            @if ($manoDeObra > 0)
            <div class="admin-card-module mb-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-person-gear"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Mano de Obra</h2>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border:1px solid #e2e8f0;border-radius:6px;">
                    <span>Mano de obra</span>
                    <span class="fw-semibold">{{ number_format($manoDeObra,2,',','.') }}</span>
                </div>
            </div>
            @endif

            <div class="admin-card-module" style="background:#f8fafc;border:2px solid #e2e8f0;">
                <div class="d-flex justify-content-between fw-bold" style="font-size:1.05rem;">
                    <span>Totales:</span>
                    <span>Bs. {{ number_format((float)$servicios->sum('precio_base')+(float)$repuestos->sum(fn($r)=>$r->cantidad*$r->precio_unitario_snapshot)+$manoDeObra,2,',','.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection