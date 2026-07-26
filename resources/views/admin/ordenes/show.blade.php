@extends('layouts.admin')

@section('title', $orden->numero_orden)
@section('navbar-title', $orden->numero_orden)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li class="active" aria-current="page">{{ $orden->numero_orden }}</li>

@include('admin.pagos.partials.modal-tarjeta')
@endsection

@section('content')
    <x-admin.page-header
        :title="$orden->numero_orden"
        :description="optional($orden->cliente)->nombre_completo . ' — ' . ($orden->vehiculo->placa ?? '')">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.ordenes.edit', $orden) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la orden</h2>
                <dl class="admin-meta">
                    <dt>Número</dt><dd>{{ $orden->numero_orden }}</dd>
                    <dt>Cliente</dt>
                    <dd><a href="{{ route('admin.clientes.show', $orden->cliente) }}">{{ $orden->cliente->nombre_completo ?? '—' }}</a></dd>
                    <dt>Vehículo</dt>
                    <dd>
                        @if ($orden->vehiculo)
                            <a href="{{ route('admin.vehiculos.show', $orden->vehiculo) }}">{{ $orden->vehiculo->placa }}</a>
                        @else — @endif
                    </dd>
                    <dt>Sucursal</dt><dd>{{ $orden->sucursal->nombre ?? '—' }}</dd>
                    <dt>Recibido por</dt><dd>{{ $orden->usuarioRecepcion->nombre ?? '—' }}</dd>
                    <dt>Emisión</dt><dd>{{ $orden->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Inicio</dt><dd>{{ $orden->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Fin</dt><dd>{{ $orden->fecha_fin?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Entrega</dt><dd>{{ $orden->fecha_entrega?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($orden->estado) {
                                'recibida' => 'info',
                                'diagnostico' => 'warning',
                                'en_proceso' => 'warning',
                                'finalizada' => 'success',
                                'entregada' => 'success',
                                'anulada' => 'danger',
                                default => 'neutral',
                            }"
                            :icon="match($orden->estado) {
                                'recibida' => 'bi-inbox-fill',
                                'diagnostico' => 'bi-search',
                                'en_proceso' => 'bi-gear-fill',
                                'finalizada' => 'bi-check-circle-fill',
                                'entregada' => 'bi-truck',
                                'anulada' => 'bi-x-circle-fill',
                                default => 'bi-circle',
                            }"
                            :label="ucfirst(str_replace('_', ' ', $orden->estado))" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Totales</h2>
                <dl class="admin-meta">
                    <dt>Subtotal servicios</dt>
                    <dd>{{ number_format((float) $orden->subtotal_servicios, 2, ',', '.') }}</dd>
                    <dt>Subtotal repuestos</dt>
                    <dd>{{ number_format((float) $orden->subtotal_repuestos, 2, ',', '.') }}</dd>
                    <dt>Descuento</dt>
                    <dd>{{ number_format((float) $orden->descuento, 2, ',', '.') }}</dd>
                    <dt>Total</dt>
                    <dd><strong>{{ number_format((float) $orden->total_general, 2, ',', '.') }}</strong></dd>
                </dl>
                <hr>
                <h3 class="h6 fw-bold mb-2">Descripción del problema</h3>
                <p class="cell-muted small">{{ $orden->descripcion_problema }}</p>
                @if ($orden->diagnostico_general)
                    <h3 class="h6 fw-bold mt-3 mb-2">Diagnóstico</h3>
                    <p class="cell-muted small">{{ $orden->diagnostico_general }}</p>
                @endif
                @if ($orden->observaciones)
                    <h3 class="h6 fw-bold mt-3 mb-2">Observaciones</h3>
                    <p class="cell-muted small">{{ $orden->observaciones }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-table-wrap mt-3">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 fw-bold mb-0">Repuestos asignados</h2>
            @if ($orden->estado !== 'anulada')
                <a href="{{ route('admin.ordenes.repuestos', $orden) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Agregar repuesto
                </a>
            @endif
        </div>
        <div class="table-responsive">
            <table class="admin-table" aria-label="Repuestos asignados a la orden">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio unit.</th>
                        <th class="text-end">Subtotal</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $detallesRepuestos = $orden->detalles->where('tipo', 'repuesto');
                    @endphp
                    @forelse ($detallesRepuestos as $detalle)
                        @php
                            $sinStock = str_contains((string) $detalle->observaciones, 'SIN STOCK');
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $detalle->repuesto->nombre ?? $detalle->descripcion }}</div>
                                <div class="cell-muted small">{{ $detalle->repuesto->codigo ?? '' }}</div>
                            </td>
                            <td class="text-end">{{ (int) $detalle->cantidad }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                            <td>
                                @if ($sinStock)
                                    <x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" label="Sin stock" />
                                @else
                                    <x-admin.status-badge tone="success" icon="bi-check-circle-fill" label="Disponible" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center cell-muted py-3">
                                No se han asignado repuestos a esta orden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@include('admin.pagos.partials.modal-tarjeta')
@endsection


