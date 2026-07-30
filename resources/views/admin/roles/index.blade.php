@extends('layouts.admin')

@section('title', 'Roles y permisos')
@section('navbar-title', 'Roles y permisos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Roles y permisos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Roles y permisos"
        description="Define los perfiles de acceso y los permisos que tendrá cada uno.">
        <x-slot:actions>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo rol
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.roles.index')"
        search-name="q"
        search-placeholder="Buscar rol"
        :autocomplete="false">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($roles->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-shield-lock"
            title="Aún no hay roles definidos"
            message="Crea el primer rol para empezar a asignar permisos."
            :action-label="'Nuevo rol'"
            :action-href="route('admin.roles.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de roles">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="d-none d-md-table-cell">Usuarios</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $rol)
                        <tr class="row-accent-rol">
                            <td>
                                <div class="cell-label">
                                    <i class="bi bi-shield-lock"></i>
                                    <div>
                                        <div class="cell-strong">{{ $rol->nombre }}</div>
                                        <div class="cell-secondary">Rol #{{ $rol->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell cell-secondary">{{ $rol->descripcion ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">
                                <span class="cell-label">
                                    <i class="bi bi-people"></i>
                                    {{ $rol->users_count }}
                                </span>
                            </td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$rol->estado ? 'success' : 'neutral'"
                                    :icon="$rol->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$rol->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.roles.permisos', $rol) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Permisos"
                                       aria-label="Permisos de {{ $rol->nombre }}">
                                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.edit', $rol) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $rol->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.roles.toggle', $rol) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $rol->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $rol->estado ? 'Desactivar' : 'Activar' }} {{ $rol->nombre }}">
                                            <i class="bi {{ $rol->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $rol) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $rol->nombre }}">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron roles con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$roles" />
        </div>
    @endif
@endsection
