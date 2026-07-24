@extends('layouts.admin')

@section('title', 'Métodos de pago')
@section('navbar-title', 'Métodos de pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Métodos de pago</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Métodos de pago"
        description="Formas de pago aceptadas por el taller.">
        <x-slot:actions>
            <a href="{{ route('admin.metodos-pago.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo método
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.metodos-pago.index')"
        search-name="q"
        search-placeholder="Buscar método de pago">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($metodos->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-credit-card"
            title="Aún no hay métodos de pago"
            message="Registra las formas de pago aceptadas (efectivo, tarjeta, transferencia, etc.)."
            :action-label="'Nuevo método'"
            :action-href="route('admin.metodos-pago.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de métodos de pago">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="d-none d-md-table-cell">Pagos</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($metodos as $m)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $m->nombre }}</div>
                                <div class="cell-muted small">Método #{{ $m->id }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $m->descripcion ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ $m->pagos_count }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$m->estado ? 'success' : 'neutral'"
                                    :icon="$m->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$m->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.metodos-pago.edit', $m) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $m->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.metodos-pago.toggle', $m) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $m->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $m->estado ? 'Desactivar' : 'Activar' }} {{ $m->nombre }}">
                                            <i class="bi {{ $m->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form id="delete-metodo-{{ $m->id }}"
                                          method="POST"
                                          action="{{ route('admin.metodos-pago.destroy', $m) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $m->nombre }}"
                                                data-tp-confirm
                                                data-tp-confirm-title="¿Eliminar método de pago?"
                                                data-tp-confirm-message="Se eliminará {{ $m->nombre }}. Esta acción no se puede deshacer."
                                                data-tp-confirm-text="Eliminar"
                                                data-tp-form-id="delete-metodo-{{ $m->id }}"
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
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron métodos de pago con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$metodos" />
        </div>
    @endif
@endsection
