@extends('layouts.admin')

@section('title', 'Clientes')
@section('navbar-title', 'Clientes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Clientes</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Clientes"
        description="Administra los clientes registrados y su información de contacto.">
        <x-slot:actions>
            <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo cliente
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.clientes.index')"
        search-name="q"
        search-placeholder="Buscar por nombre, CI, teléfono o email">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($clientes->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-person-vcard"
            title="Aún no hay clientes registrados"
            message="Comienza registrando tu primer cliente para iniciar operaciones."
            :action-label="'Registrar cliente'"
            :action-href="route('admin.clientes.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de clientes">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="d-none d-md-table-cell">CI</th>
                        <th class="d-none d-lg-table-cell">Teléfono</th>
                        <th class="d-none d-lg-table-cell">Email</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="admin-avatar admin-avatar--sm admin-avatar--muted" aria-hidden="true">
                                        {{ mb_strtoupper(mb_substr($cliente->nombre_completo, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="cell-strong">{{ $cliente->nombre_completo }}</div>
                                        <div class="cell-muted small">Cliente #{{ $cliente->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell col-muted">{{ $cliente->ci ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell">{{ $cliente->telefono }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $cliente->email ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$cliente->estado ? 'success' : 'neutral'"
                                    :icon="$cliente->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$cliente->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.clientes.show', $cliente) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles de {{ $cliente->nombre_completo }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.clientes.edit', $cliente) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $cliente->nombre_completo }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.clientes.toggle', $cliente) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $cliente->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $cliente->estado ? 'Desactivar' : 'Activar' }} {{ $cliente->nombre_completo }}">
                                            <i class="bi {{ $cliente->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.clientes.destroy', $cliente) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $cliente->nombre_completo }}">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron clientes con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$clientes" />
        </div>
    @endif
@endsection
