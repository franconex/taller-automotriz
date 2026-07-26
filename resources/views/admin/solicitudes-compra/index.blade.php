@extends('layouts.admin')

@section('title', 'Solicitudes de compra')
@section('navbar-title', 'Solicitudes de compra')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Solicitudes de compra</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Solicitudes de compra"
        description="Solicitudes internas de compra de repuestos.">
        <x-slot:actions>
            <a href="{{ route('admin.solicitudes-compra.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nueva solicitud
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.solicitudes-compra.index')"
        search-name="q"
        search-placeholder="Buscar por número">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendientes</option>
                <option value="aprobada" @selected(request('estado') === 'aprobada')>Aprobadas</option>
                <option value="rechazada" @selected(request('estado') === 'rechazada')>Rechazadas</option>
            </select>
            <select name="prioridad" class="form-select" style="max-width:140px;" onchange="this.form.submit()">
                <option value="">Todas</option>
                <option value="alta" @selected(request('prioridad') === 'alta')>Alta</option>
                <option value="media" @selected(request('prioridad') === 'media')>Media</option>
                <option value="baja" @selected(request('prioridad') === 'baja')>Baja</option>
            </select>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach ($sucursales as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($solicitudes->isEmpty() && ! request()->has('q') && ! request()->has('estado') && ! request()->has('prioridad'))
        <x-admin.empty-state
            icon="bi-cart3"
            title="Aún no hay solicitudes de compra"
            message="Crea la primera solicitud cuando detectes stock bajo en el inventario."
            :action-label="'Nueva solicitud'"
            :action-href="route('admin.solicitudes-compra.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Solicitudes de compra">
                <thead>
                    <tr>
                        <th>N° Solicitud</th>
                        <th class="d-none d-md-table-cell">Fecha</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="d-none d-md-table-cell">Solicitante</th>
                        <th class="text-end">Productos</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $s)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $s->numero }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $s->fecha_solicitud?->format('d/m/Y') }}</td>
                            <td class="d-none d-lg-table-cell">{{ $s->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($s->prioridad) {
                                        'alta' => 'danger',
                                        'media' => 'warning',
                                        'baja' => 'info',
                                        default => 'neutral',
                                    }"
                                    :label="ucfirst($s->prioridad)" />
                            </td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($s->estado) {
                                        'pendiente' => 'warning',
                                        'aprobada' => 'success',
                                        'rechazada' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :icon="match($s->estado) {
                                        'pendiente' => 'bi-clock',
                                        'aprobada' => 'bi-check-circle-fill',
                                        'rechazada' => 'bi-x-circle-fill',
                                        default => 'bi-question-circle',
                                    }"
                                    :label="match($s->estado) {
                                        'pendiente' => 'Pendiente',
                                        'aprobada' => 'Aprobada',
                                        'rechazada' => 'Rechazada',
                                        default => $s->estado,
                                    }" />
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $s->usuarioSolicitante->nombre ?? '—' }}</td>
                            <td class="text-end">{{ $s->detalles->count() }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.solicitudes-compra.show', $s) }}"
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
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron solicitudes con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$solicitudes" />
        </div>
    @endif
@endsection
