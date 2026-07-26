@extends('layouts.admin')

@section('title', 'Sucursales')
@section('navbar-title', 'Sucursales')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Sucursales</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Sucursales"
        description="Administra las sedes, horarios y datos operativos del taller.">
        <x-slot:actions>
            <a href="{{ route('admin.sucursales.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nueva sucursal
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.sucursales.index')"
        search-name="q"
        search-placeholder="Buscar por nombre, dirección o teléfono">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="1" @selected(request('estado') === '1')>Activas</option>
                <option value="0" @selected(request('estado') === '0')>Inactivas</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($sucursales->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-building"
            title="Aún no hay sucursales registradas"
            message="Registra la primera sucursal para comenzar a operar."
            :action-label="'Registrar sucursal'"
            :action-href="route('admin.sucursales.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de sucursales">
                <thead>
                    <tr>
                        <th>Sucursal</th>
                        <th class="d-none d-md-table-cell">Dirección</th>
                        <th class="d-none d-lg-table-cell">Teléfono</th>
                        <th class="d-none d-lg-table-cell">Horario</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sucursales as $sucursal)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $sucursal->nombre }}</div>
                                <div class="cell-muted small">Sucursal #{{ $sucursal->id }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $sucursal->direccion }}</td>
                            <td class="d-none d-lg-table-cell">{{ $sucursal->telefono }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $sucursal->horario_atencion ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$sucursal->estado ? 'success' : 'neutral'"
                                    :icon="$sucursal->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$sucursal->estado ? 'Activa' : 'Inactiva'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.sucursales.show', $sucursal) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles de {{ $sucursal->nombre }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.sucursales.edit', $sucursal) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $sucursal->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.sucursales.toggle', $sucursal) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $sucursal->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $sucursal->estado ? 'Desactivar' : 'Activar' }} {{ $sucursal->nombre }}">
                                            <i class="bi {{ $sucursal->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.sucursales.destroy', $sucursal) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $sucursal->nombre }}">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state
                                    icon="bi-search"
                                    title="Sin resultados"
                                    message="No se encontraron sucursales con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$sucursales" />
        </div>
    @endif
@endsection
