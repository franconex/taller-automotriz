@extends('layouts.admin')

@section('title', 'Empleados')
@section('navbar-title', 'Empleados')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Empleados</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Empleados"
        description="Gestiona la información personal y laboral de los trabajadores.">
        <x-slot:actions>
            <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo empleado
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.empleados.index')"
        search-name="q"
        search-placeholder="Buscar por nombre, CI o cargo">
        <x-slot:filters>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach (($sucursales ?? collect()) as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <select name="rol_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach (($roles ?? collect()) as $r)
                    <option value="{{ $r->id }}" @selected((string) request('rol_id') === (string) $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($empleados->isEmpty() && ! request()->has('q') && ! request()->has('sucursal_id') && ! request()->has('rol_id') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-people"
            title="Aún no hay empleados registrados"
            message="Registra el primer empleado para comenzar a conformar tu equipo."
            :action-label="'Nuevo empleado'"
            :action-href="route('admin.empleados.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de empleados">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th class="d-none d-md-table-cell">CI</th>
                        <th class="d-none d-md-table-cell">Rol</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th class="d-none d-lg-table-cell">Cuenta</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($empleados as $emp)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="admin-avatar admin-avatar--sm" aria-hidden="true">
                                        {{ mb_strtoupper(mb_substr($emp->nombre_completo, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="cell-strong">{{ $emp->nombre_completo }}</div>
                                        <div class="cell-muted small">{{ $emp->telefono }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell col-muted">{{ $emp->ci }}</td>
                            <td class="d-none d-md-table-cell">
                                <x-admin.status-badge
                                    tone="info"
                                    icon="bi-shield-lock"
                                    :label="$emp->rol->nombre ?? '—'" />
                                @if ($emp->cargo)
                                    <div class="cell-muted small mt-1">{{ $emp->cargo }}</div>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $emp->sucursal->nombre ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell">
                                @if ($emp->user)
                                    <x-admin.status-badge tone="info" icon="bi-person-badge" :label="$emp->user->username" />
                                @else
                                    <span class="cell-muted small">Sin cuenta</span>
                                @endif
                            </td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$emp->estado ? 'success' : 'neutral'"
                                    :icon="$emp->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$emp->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.empleados.show', $emp) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles de {{ $emp->nombre_completo }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.empleados.edit', $emp) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $emp->nombre_completo }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.empleados.toggle', $emp) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon {{ $emp->estado ? '' : 'btn-icon--primary' }}"
                                                title="{{ $emp->estado ? 'Dar de baja' : 'Activar' }}"
                                                aria-label="{{ $emp->estado ? 'Dar de baja' : 'Activar' }} {{ $emp->nombre_completo }}">
                                            <i class="bi {{ $emp->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron empleados con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$empleados" />
        </div>
    @endif
@endsection
