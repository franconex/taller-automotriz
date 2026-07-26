@extends('layouts.admin')

@section('title', 'Órdenes de compra')
@section('navbar-title', 'Órdenes de compra')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Órdenes de compra</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Órdenes de compra"
        description="Órdenes enviadas a proveedores." />

    <x-admin.filters
        :action="route('admin.ordenes-compra.index')"
        search-name="q"
        search-placeholder="Buscar por número">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="borrador" @selected(request('estado') === 'borrador')>Borrador</option>
                <option value="pendiente_aprobacion" @selected(request('estado') === 'pendiente_aprobacion')>Pendiente aprobación</option>
                <option value="aprobada" @selected(request('estado') === 'aprobada')>Aprobada</option>
                <option value="enviada" @selected(request('estado') === 'enviada')>Enviada</option>
                <option value="parcialmente_recibida" @selected(request('estado') === 'parcialmente_recibida')>Parcialmente recibida</option>
                <option value="recibida" @selected(request('estado') === 'recibida')>Recibida</option>
                <option value="cancelada" @selected(request('estado') === 'cancelada')>Cancelada</option>
            </select>
            <select name="proveedor_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los proveedores</option>
                @foreach ($proveedores as $p)
                    <option value="{{ $p->id }}" @selected((string) request('proveedor_id') === (string) $p->id)>{{ $p->nombre_empresa }}</option>
                @endforeach
            </select>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach ($sucursales as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($ordenes->isEmpty() && ! request()->has('q') && ! request()->has('estado') && ! request()->has('proveedor_id'))
        <x-admin.empty-state
            icon="bi-file-earmark-check"
            title="Aún no hay órdenes de compra"
            message="Las órdenes se generan automáticamente al seleccionar una cotización."
            :action-label="'Ir a solicitudes'"
            :action-href="route('admin.solicitudes-compra.index')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Órdenes de compra">
                <thead>
                    <tr>
                        <th>N° Orden</th>
                        <th class="d-none d-md-table-cell">Proveedor</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th>Estado</th>
                        <th class="text-end d-none d-md-table-cell">Total</th>
                        <th class="d-none d-md-table-cell">Fecha</th>
                        <th class="text-end d-none d-lg-table-cell">Avance</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $o)
                        @php
                            $solicitadas = $o->detalles->sum('cantidad_solicitada');
                            $recibidas = $o->detalles->sum('cantidad_recibida');
                            $avance = $solicitadas > 0 ? round(($recibidas / $solicitadas) * 100) : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $o->numero }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $o->proveedor->nombre_empresa ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $o->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($o->estado) {
                                        'borrador' => 'neutral',
                                        'pendiente_aprobacion' => 'warning',
                                        'aprobada' => 'info',
                                        'enviada' => 'primary',
                                        'parcialmente_recibida' => 'warning',
                                        'recibida' => 'success',
                                        'cancelada' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :label="match($o->estado) {
                                        'borrador' => 'Borrador',
                                        'pendiente_aprobacion' => 'Pendiente aprob.',
                                        'aprobada' => 'Aprobada',
                                        'enviada' => 'Enviada',
                                        'parcialmente_recibida' => 'Parcial',
                                        'recibida' => 'Recibida',
                                        'cancelada' => 'Cancelada',
                                        default => $o->estado,
                                    }" />
                            </td>
                            <td class="text-end d-none d-md-table-cell cell-strong">Bs {{ number_format((float) $o->total, 2) }}</td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $o->fecha_emision?->format('d/m/Y') }}</td>
                            <td class="text-end d-none d-lg-table-cell">
                                <div class="d-flex align-items-center gap-2 justify-content-end">
                                    <span>{{ $avance }}%</span>
                                    <div class="progress" style="width:60px;height:6px;">
                                        <div class="progress-bar" style="width:{{ $avance }}%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.ordenes-compra.show', $o) }}"
                                       class="btn-icon"
                                       title="Ver detalle"
                                       aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron órdenes con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$ordenes" />
        </div>
    @endif
@endsection
