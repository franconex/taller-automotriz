@extends('layouts.admin')

@section('title', 'Usuarios')
@section('navbar-title', 'Usuarios')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Usuarios</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Usuarios"
        description="Cuentas con acceso al sistema. Cada usuario está vinculado a un rol y a un empleado.">
        <x-slot:actions>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo usuario
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.usuarios.index')"
        search-name="q"
        search-placeholder="Buscar por nombre, usuario o email">
        <x-slot:filters>
            <select name="rol_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach (($roles ?? collect()) as $r)
                    <option value="{{ $r->id }}" @selected((string) request('rol_id') === (string) $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($usuarios->isEmpty() && ! request()->has('q') && ! request()->has('rol_id') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-person-badge"
            title="Aún no hay usuarios con acceso"
            message="Crea el primer usuario para comenzar a operar el sistema."
            :action-label="'Nuevo usuario'"
            :action-href="route('admin.usuarios.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de usuarios">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th class="d-none d-md-table-cell">Username</th>
                        <th>Rol</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="admin-avatar admin-avatar--sm" aria-hidden="true">
                                        {{ mb_strtoupper(mb_substr($u->nombre, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="cell-strong">{{ $u->nombre }}</div>
                                        <div class="cell-muted small">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $u->username }}</td>
                            <td><x-admin.status-badge tone="info" :label="$u->rol->nombre ?? '—'" /></td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $u->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($u->estado) {
                                        'activo' => 'success',
                                        'vacaciones' => 'warning',
                                        default => 'neutral',
                                    }"
                                    :icon="match($u->estado) {
                                        'activo' => 'bi-check-circle-fill',
                                        'vacaciones' => 'bi-sun-fill',
                                        default => 'bi-pause-circle-fill',
                                    }"
                                    :label="match($u->estado) {
                                        'activo' => 'Activo',
                                        'vacaciones' => 'Vacaciones',
                                        default => 'Inactivo',
                                    }" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.usuarios.show', $u) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles de {{ $u->nombre }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.usuarios.edit', $u) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $u->nombre }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.usuarios.show', $u) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Restablecer contraseña"
                                       aria-label="Restablecer contraseña de {{ $u->nombre }}">
                                        <i class="bi bi-key" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.usuarios.toggle', $u) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $u->estado === 'activo' ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $u->estado === 'activo' ? 'Desactivar' : 'Activar' }} {{ $u->nombre }}">
                                            <i class="bi {{ $u->estado === 'activo' ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron usuarios con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$usuarios" />
        </div>
    @endif
@endsection
