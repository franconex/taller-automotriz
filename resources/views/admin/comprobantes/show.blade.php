@extends('layouts.admin')

@section('title', $comprobante->numero)
@section('navbar-title', 'Comprobante ' . $comprobante->numero)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.comprobantes.index') }}">Comprobantes</a></li>
    <li class="active" aria-current="page">{{ $comprobante->numero }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Comprobante ' . $comprobante->numero"
        :description="$comprobante->cliente->nombre_completo ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.comprobantes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.comprobantes.edit', $comprobante) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos</h2>
                <dl class="admin-meta">
                    <dt>Número</dt><dd>{{ $comprobante->numero }}</dd>
                    <dt>Fecha emisión</dt><dd>{{ $comprobante->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Cliente</dt>
                    <dd>
                        @if ($comprobante->cliente)
                            <a href="{{ route('admin.clientes.show', $comprobante->cliente) }}">{{ $comprobante->cliente->nombre_completo }}</a>
                        @else — @endif
                    </dd>
                    <dt>NIT/CI</dt><dd>{{ $comprobante->nit_ci ?? '—' }}</dd>
                    <dt>Razón social</dt><dd>{{ $comprobante->razon_social ?? '—' }}</dd>
                    <dt>Monto</dt><dd><strong>{{ number_format((float) $comprobante->monto_total, 2, ',', '.') }}</strong></dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$comprobante->estado === 'emitido' ? 'success' : 'danger'"
                            :icon="$comprobante->estado === 'emitido' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"
                            :label="ucfirst($comprobante->estado)" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Pago asociado</h2>
                @if ($comprobante->pago)
                    <dl class="admin-meta">
                        <dt>Pago</dt>
                        <dd><a href="{{ route('admin.pagos.show', $comprobante->pago) }}">#{{ $comprobante->pago->id }}</a></dd>
                        <dt>Orden</dt>
                        <dd>
                            @if ($comprobante->pago->ordenTrabajo)
                                <a href="{{ route('admin.ordenes.show', $comprobante->pago->ordenTrabajo) }}">{{ $comprobante->pago->ordenTrabajo->numero_orden }}</a>
                            @else — @endif
                        </dd>
                        <dt>Monto</dt><dd>{{ number_format((float) $comprobante->pago->monto, 2, ',', '.') }}</dd>
                        <dt>Método</dt><dd>{{ $comprobante->pago->metodoPago->nombre ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="cell-muted small mb-0">Sin pago asociado.</p>
                @endif
                @if ($comprobante->observaciones)
                    <h3 class="h6 fw-bold mt-3 mb-2">Observaciones</h3>
                    <p class="cell-muted small">{{ $comprobante->observaciones }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
