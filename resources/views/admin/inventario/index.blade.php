@extends('layouts.admin')

@section('title', 'Inventario')
@section('navbar-title', 'Inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Inventario</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Inventario"
        description="Existencias de repuestos por sucursal, con stock actual y reservado.">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Registrar movimiento
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.inventario.index')"
        search-name="q"
        search-placeholder="Buscar repuesto">
        <x-slot:filters>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach (($sucursales ?? collect()) as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($items->isEmpty() && ! request()->has('q') && ! request()->has('sucursal_id'))
        <x-admin.empty-state
            icon="bi-boxes"
            title="Aún no hay existencias en inventario"
            message="Registra un movimiento de entrada para iniciar el control de stock."
            :action-label="'Registrar movimiento'"
            :action-href="route('admin.movimientos-inventario.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Inventario por sucursal">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="d-none d-md-table-cell">Sucursal</th>
                        <th class="text-end">Actual</th>
                        <th class="d-none d-md-table-cell text-end">Reservado</th>
                        <th class="d-none d-lg-table-cell text-end">Disponible</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $it)
                        @php
                            $disponible = (int) $it->cantidad_actual - (int) $it->cantidad_reservada;
                            $alerta = $disponible <= (int) optional($it->repuesto)->stock_minimo;
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $it->repuesto->nombre ?? '—' }}</div>
                                <div class="cell-muted small">{{ $it->repuesto->codigo ?? '' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $it->sucursal->nombre ?? '—' }}</td>
                            <td class="text-end cell-strong">{{ $it->cantidad_actual }}</td>
                            <td class="d-none d-md-table-cell text-end cell-muted">{{ $it->cantidad_reservada }}</td>
                            <td class="d-none d-lg-table-cell text-end">
                                <x-admin.status-badge
                                    :tone="$alerta ? 'danger' : 'success'"
                                    :icon="$alerta ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'"
                                    :label="$disponible" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.inventario.show', $it) }}"
                                       class="btn-icon"
                                       title="Ver detalle"
                                       aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.inventario.edit', $it) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron items con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$items" />
        </div>
    @endif
@endsection
