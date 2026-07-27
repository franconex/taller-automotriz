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
                <h2 class="h6 fw-bold mb-3">Datos del pago</h2>
                <dl class="admin-meta">
                    <dt>Fecha</dt><dd>{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Monto</dt><dd><strong>{{ number_format((float) $pago->monto, 2, ',', '.') }}</strong></dd>
                    <dt>Método</dt><dd>{{ $pago->metodoPago->nombre ?? '—' }}</dd>
                    <dt>Orden</dt>
                    <dd>
                        @if ($pago->ordenTrabajo)
                            <a href="{{ route('admin.ordenes.show', $pago->ordenTrabajo) }}">{{ $pago->ordenTrabajo->numero_orden }}</a>
                        @else — @endif
                    </dd>
                    <dt>Cliente</dt><dd>{{ $pago->ordenTrabajo->cliente->nombre_completo ?? '—' }}</dd>
                    <dt>Comprobante</dt><dd>{{ $pago->numero_comprobante ?? '—' }}</dd>
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
                <h2 class="h6 fw-bold mb-3">Notas y comprobante</h2>
                @if ($pago->observaciones)
                    <p class="cell-muted small">{{ $pago->observaciones }}</p>
                @else
                    <p class="cell-muted small">Sin observaciones.</p>
                @endif
                <hr>
                @if ($pago->comprobante)
                    <a href="{{ route('admin.comprobantes.show', $pago->comprobante) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-receipt" aria-hidden="true"></i>
                        Ver comprobante {{ $pago->comprobante->numero }}
                    </a>
                @else
                    <p class="cell-muted small mb-0">Este pago aún no tiene un comprobante emitido.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
