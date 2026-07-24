@extends('layouts.admin')

@section('title', 'Tipos de servicio')
@section('navbar-title', 'Tipos de servicio')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Tipos de servicio</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Tipos de servicio"
        description="Categorías principales bajo las cuales se agrupan los servicios del taller.">
        <x-slot:actions>
            <a href="{{ route('admin.tipos-servicio.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo tipo
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.tipos-servicio.index')"
        search-name="q"
        search-placeholder="Buscar tipo de servicio">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($tipos->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-tags"
            title="Aún no hay tipos de servicio"
            message="Crea categorías como 'Mantenimiento', 'Diagnóstico' o 'Reparación'."
            :action-label="'Nuevo tipo'"
            :action-href="route('admin.tipos-servicio.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de tipos de servicio">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="d-none d-md-table-cell">Servicios</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipos as $t)
                        <tr>
                            <td class="cell-strong">{{ $t->nombre }}</td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $t->descripcion ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ $t->servicios_count }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$t->estado ? 'success' : 'neutral'"
                                    :icon="$t->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$t->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.tipos-servicio.edit', $t) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $t->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.tipos-servicio.toggle', $t) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $t->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $t->estado ? 'Desactivar' : 'Activar' }} {{ $t->nombre }}">
                                            <i class="bi {{ $t->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form id="delete-tipo-{{ $t->id }}"
                                          method="POST"
                                          action="{{ route('admin.tipos-servicio.destroy', $t) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $t->nombre }}"
                                                data-tp-confirm
                                                data-tp-confirm-title="¿Eliminar tipo de servicio?"
                                                data-tp-confirm-message="Se eliminará {{ $t->nombre }}. Esta acción no se puede deshacer."
                                                data-tp-confirm-text="Eliminar"
                                                data-tp-form-id="delete-tipo-{{ $t->id }}"
                                                data-tp-confirm-icon="warning">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron tipos de servicio con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$tipos" />
        </div>
    @endif
@endsection
