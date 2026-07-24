@extends('layouts.admin')

@section('title', $repuesto->nombre)
@section('navbar-title', $repuesto->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.repuestos.index') }}">Repuestos</a></li>
    <li class="active" aria-current="page">{{ $repuesto->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$repuesto->nombre"
        :description="$repuesto->codigo">
        <x-slot:actions>
            <a href="{{ route('admin.repuestos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.repuestos.edit', $repuesto) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos</h2>
                <dl class="admin-meta">
                    <dt>Código</dt><dd>{{ $repuesto->codigo }}</dd>
                    <dt>Nombre</dt><dd>{{ $repuesto->nombre }}</dd>
                    <dt>Descripción</dt><dd>{{ $repuesto->descripcion ?? '—' }}</dd>
                    <dt>Proveedor</dt>
                    <dd>
                        @if ($repuesto->proveedor)
                            <a href="{{ route('admin.proveedores.show', $repuesto->proveedor) }}">{{ $repuesto->proveedor->nombre_empresa }}</a>
                        @else — @endif
                    </dd>
                    <dt>Costo</dt><dd>{{ number_format((float) $repuesto->costo_compra, 2, ',', '.') }}</dd>
                    <dt>Precio de venta</dt><dd>{{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</dd>
                    <dt>Stock mínimo</dt><dd>{{ $repuesto->stock_minimo }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$repuesto->estado ? 'success' : 'neutral'"
                            :icon="$repuesto->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$repuesto->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom">
                    <h2 class="h6 fw-bold mb-0">Stock por sucursal</h2>
                </div>
                @if ($repuesto->inventarios->isEmpty())
                    <div class="p-4 cell-muted text-center">Sin inventario registrado.</div>
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sucursal</th>
                                <th class="text-end">Actual</th>
                                <th class="text-end">Reservado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repuesto->inventarios as $inv)
                                <tr>
                                    <td>{{ $inv->sucursal->nombre ?? '—' }}</td>
                                    <td class="text-end cell-strong">{{ $inv->cantidad_actual }}</td>
                                    <td class="text-end cell-muted">{{ $inv->cantidad_reservada }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
