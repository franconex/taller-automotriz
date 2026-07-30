@extends('layouts.admin')

@section('title', 'Vehículos')
@section('navbar-title', 'Vehículos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Vehículos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Vehículos"
        description="Vehículos registrados de cada cliente, con placa, marca y modelo.">
        <x-slot:actions>
            @if (Auth::user()->tienePermiso('vehiculos.crear'))
            <a href="{{ route('admin.vehiculos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo vehículo
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.vehiculos.index')"
        search-name="q"
        search-placeholder="Buscar por placa, chasis o cliente">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($vehiculos->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-car-front"
            title="Aún no hay vehículos registrados"
            message="Registra el primer vehículo para empezar a gestionar servicios."
            :action-label="Auth::user()->tienePermiso('vehiculos.crear') ? 'Registrar vehículo' : null"
            :action-href="Auth::user()->tienePermiso('vehiculos.crear') ? route('admin.vehiculos.create') : null" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de vehículos">
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th class="d-none d-md-table-cell">Cliente</th>
                        <th class="d-none d-lg-table-cell">Chasis</th>
                        <th class="d-none d-lg-table-cell">Año / Color</th>
                        <th>Estado</th>
                        @php $puedeEditarVehiculo = Auth::user()->tienePermiso('vehiculos.editar'); @endphp
                        @if ($puedeEditarVehiculo)
                        <th class="col-actions">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $v)
                        <tr style="border-left:3px solid #0ea5e9;">
                            <td>
                                <div class="cell-label">
                                    <i class="bi bi-car-front"></i>
                                    <div>
                                        <div class="cell-strong">{{ $v->placa }}</div>
                                        <div class="cell-secondary">
                                            {{ $v->marca ?? '—' }} {{ $v->modelo ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if ($v->cliente)
                                    <a href="{{ route('admin.clientes.show', $v->cliente) }}" class="text-decoration-none">
                                        <span class="cell-label">
                                            <i class="bi bi-person"></i>
                                            {{ $v->cliente->nombre_completo }}
                                        </span>
                                    </a>
                                @else
                                    <span class="cell-secondary">—</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell cell-secondary">{{ $v->numero_chasis ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell">
                                <span class="cell-secondary">{{ $v->anio ?? '—' }}</span>
                                @if ($v->color)
                                    <span class="badge rounded-pill" style="background:#e2e8f0;color:#475569;font-size:0.7rem;">{{ $v->color }}</span>
                                @endif
                            </td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$v->estado ? 'success' : 'neutral'"
                                    :icon="$v->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$v->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            @if ($puedeEditarVehiculo)
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.vehiculos.show', $v) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles de {{ $v->placa }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.vehiculos.edit', $v) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar {{ $v->placa }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.vehiculos.toggle', $v) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="{{ $v->estado ? 'Desactivar' : 'Activar' }}"
                                                aria-label="{{ $v->estado ? 'Desactivar' : 'Activar' }} {{ $v->placa }}">
                                            <i class="bi {{ $v->estado ? 'bi-pause-circle' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.vehiculos.destroy', $v) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar {{ $v->placa }}">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron vehículos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$vehiculos" />
        </div>
    @endif
@endsection