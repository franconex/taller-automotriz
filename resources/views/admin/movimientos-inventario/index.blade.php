@extends('layouts.admin')

@section('title', 'Movimientos de inventario')
@section('navbar-title', 'Movimientos de inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Movimientos de inventario</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Movimientos de inventario"
        description="Entradas, salidas y ajustes de stock por sucursal.">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo movimiento
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.movimientos-inventario.index')"
        search-name="q"
        search-placeholder="Buscar por repuesto o motivo">
        <x-slot:filters>
            <select name="tipo" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <option value="entrada" @selected(request('tipo') === 'entrada')>Entrada</option>
                <option value="salida"  @selected(request('tipo') === 'salida')>Salida</option>
                <option value="ajuste"  @selected(request('tipo') === 'ajuste')>Ajuste</option>
            </select>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach (($sucursales ?? collect()) as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <select name="repuesto_id" class="form-select" style="max-width:240px;" onchange="this.form.submit()">
                <option value="">Todos los repuestos</option>
                @foreach (($repuestos ?? collect()) as $r)
                    <option value="{{ $r->id }}" @selected((string) request('repuesto_id') === (string) $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($movimientos->isEmpty() && ! request()->has('q') && ! request()->has('tipo') && ! request()->has('sucursal_id') && ! request()->has('repuesto_id'))
        <x-admin.empty-state
            icon="bi-arrow-left-right"
            title="Aún no hay movimientos de inventario"
            message="Registra el primer movimiento para iniciar el control de stock."
            :action-label="'Nuevo movimiento'"
            :action-href="route('admin.movimientos-inventario.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de movimientos de inventario">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Repuesto</th>
                        <th class="d-none d-md-table-cell">Sucursal</th>
                        <th>Tipo</th>
                        <th class="d-none d-lg-table-cell text-end">Cantidad</th>
                        <th class="d-none d-lg-table-cell text-end">Existencias</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movimientos as $m)
                        <tr>
                            <td class="cell-muted">{{ $m->fecha_movimiento?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                <div class="cell-strong">{{ optional($m->inventario->repuesto)->nombre ?? '—' }}</div>
                                <div class="cell-muted small">{{ optional($m->inventario->repuesto)->codigo ?? '' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ optional($m->inventario->sucursal)->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($m->tipo) {
                                        'entrada' => 'success',
                                        'salida' => 'warning',
                                        'ajuste' => 'info',
                                        default => 'neutral',
                                    }"
                                    :icon="match($m->tipo) {
                                        'entrada' => 'bi-plus-circle-fill',
                                        'salida' => 'bi-dash-circle-fill',
                                        'ajuste' => 'bi-arrow-left-right',
                                        default => 'bi-circle',
                                    }"
                                    :label="ucfirst($m->tipo)" />
                            </td>
                            <td class="d-none d-lg-table-cell text-end cell-strong">{{ $m->cantidad }}</td>
                            <td class="d-none d-lg-table-cell text-end cell-muted">
                                {{ $m->existencia_anterior }} → {{ $m->existencia_nueva }}
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.movimientos-inventario.show', $m) }}"
                                       class="btn-icon"
                                       title="Ver detalle"
                                       aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.movimientos-inventario.destroy', $m) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar movimiento">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron movimientos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$movimientos" />
        </div>
    @endif
@endsection
