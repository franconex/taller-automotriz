@extends('layouts.admin')

@section('title', 'Repuestos')
@section('navbar-title', 'Repuestos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Repuestos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Repuestos"
        description="Catálogo de repuestos con código, costo y precio de venta.">
        <x-slot:actions>
            <a href="{{ route('admin.repuestos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo repuesto
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.repuestos.index')"
        search-name="q"
        search-placeholder="Buscar por código o nombre">
        <x-slot:filters>
            <select name="proveedor_id" class="form-select" style="max-width:220px;" onchange="this.form.submit()">
                <option value="">Todos los proveedores</option>
                @foreach (($proveedores ?? collect()) as $p)
                    <option value="{{ $p->id }}" @selected((string) request('proveedor_id') === (string) $p->id)>{{ $p->nombre_empresa }}</option>
                @endforeach
            </select>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($repuestos->isEmpty() && ! request()->has('q') && ! request()->has('proveedor_id') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-box-seam"
            title="Aún no hay repuestos en el catálogo"
            message="Registra el primer repuesto para iniciar el inventario."
            :action-label="'Nuevo repuesto'"
            :action-href="route('admin.repuestos.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de repuestos">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="d-none d-md-table-cell">Código</th>
                        <th class="d-none d-lg-table-cell">Proveedor</th>
                        <th class="d-none d-md-table-cell text-end">Costo</th>
                        <th class="d-none d-md-table-cell text-end">Venta</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repuestos as $r)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $r->nombre }}</div>
                                <div class="cell-muted small">Repuesto #{{ $r->id }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $r->codigo }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $r->proveedor->nombre_empresa ?? '—' }}</td>
                            <td class="d-none d-md-table-cell text-end">{{ number_format((float) $r->costo_compra, 2, ',', '.') }}</td>
                            <td class="d-none d-md-table-cell text-end cell-strong">{{ number_format((float) $r->precio_venta, 2, ',', '.') }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$r->estado ? 'success' : 'neutral'"
                                    :icon="$r->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$r->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.repuestos.edit', $r) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $r->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.repuestos.toggle', $r) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $r->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $r->estado ? 'Desactivar' : 'Activar' }} {{ $r->nombre }}">
                                            <i class="bi {{ $r->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form id="delete-repuesto-{{ $r->id }}"
                                          method="POST"
                                          action="{{ route('admin.repuestos.destroy', $r) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $r->nombre }}"
                                                data-tp-confirm
                                                data-tp-confirm-title="¿Eliminar repuesto?"
                                                data-tp-confirm-message="Se eliminará {{ $r->nombre }}. Esta acción no se puede deshacer."
                                                data-tp-confirm-text="Eliminar"
                                                data-tp-form-id="delete-repuesto-{{ $r->id }}"
                                                data-tp-confirm-icon="warning">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron repuestos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$repuestos" />
        </div>
    @endif
@endsection
