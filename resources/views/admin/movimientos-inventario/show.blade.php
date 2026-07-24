@extends('layouts.admin')

@section('title', 'Movimiento #' . $movimiento->id)
@section('navbar-title', 'Movimiento #' . $movimiento->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.movimientos-inventario.index') }}">Movimientos de inventario</a></li>
    <li class="active" aria-current="page">#{{ $movimiento->id }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Movimiento #' . $movimiento->id"
        :description="$movimiento->fecha_movimiento?->format('d/m/Y H:i')">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos del movimiento</h2>
                <dl class="admin-meta">
                    <dt>Tipo</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($movimiento->tipo) {
                                'entrada' => 'success',
                                'salida' => 'warning',
                                'ajuste' => 'info',
                                default => 'neutral',
                            }"
                            :icon="match($movimiento->tipo) {
                                'entrada' => 'bi-plus-circle-fill',
                                'salida' => 'bi-dash-circle-fill',
                                'ajuste' => 'bi-arrow-left-right',
                                default => 'bi-circle',
                            }"
                            :label="ucfirst($movimiento->tipo)" />
                    </dd>
                    <dt>Cantidad</dt><dd>{{ $movimiento->cantidad }}</dd>
                    <dt>Existencia anterior</dt><dd>{{ $movimiento->existencia_anterior }}</dd>
                    <dt>Existencia nueva</dt><dd>{{ $movimiento->existencia_nueva }}</dd>
                    <dt>Motivo</dt><dd>{{ $movimiento->motivo }}</dd>
                    <dt>Registrado por</dt><dd>{{ $movimiento->usuario->nombre ?? '—' }}</dd>
                    @if ($movimiento->ordenTrabajo)
                        <dt>Orden de trabajo</dt>
                        <dd><a href="{{ route('admin.ordenes.show', $movimiento->ordenTrabajo) }}">{{ $movimiento->ordenTrabajo->numero_orden }}</a></dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Repuesto y sucursal</h2>
                <dl class="admin-meta">
                    <dt>Repuesto</dt>
                    <dd>
                        @if ($movimiento->inventario?->repuesto)
                            <a href="{{ route('admin.repuestos.edit', $movimiento->inventario->repuesto) }}">{{ $movimiento->inventario->repuesto->nombre }}</a>
                        @else — @endif
                    </dd>
                    <dt>Sucursal</dt>
                    <dd>{{ optional($movimiento->inventario->sucursal)->nombre ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
