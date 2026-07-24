@extends('layouts.admin')

@section('title', 'Citas')
@section('navbar-title', 'Citas')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Citas</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Citas"
        description="Agenda de citas registradas para diagnóstico, mantenimiento o reparación.">
        <x-slot:actions>
            <a href="{{ route('admin.citas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nueva cita
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.citas.index')"
        search-name="q"
        search-placeholder="Buscar por cliente, vehículo o descripción">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="pendiente"  @selected(request('estado') === 'pendiente')>Pendiente</option>
                <option value="confirmada" @selected(request('estado') === 'confirmada')>Confirmada</option>
                <option value="atendida"   @selected(request('estado') === 'atendida')>Atendida</option>
                <option value="cancelada"  @selected(request('estado') === 'cancelada')>Cancelada</option>
            </select>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach (($sucursales ?? collect()) as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control" style="max-width:170px;" onchange="this.form.submit()" aria-label="Filtrar por fecha">
        </x-slot:filters>
    </x-admin.filters>

    @if ($citas->isEmpty() && ! request()->has('q') && ! request()->has('estado') && ! request()->has('fecha') && ! request()->has('sucursal_id'))
        <x-admin.empty-state
            icon="bi-calendar-check"
            title="No hay citas registradas"
            message="Crea la primera cita para comenzar a llenar la agenda."
            :action-label="'Nueva cita'"
            :action-href="route('admin.citas.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de citas">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Cliente / Vehículo</th>
                        <th class="d-none d-md-table-cell">Tipo</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citas as $c)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $c->fecha?->format('d/m/Y') ?? '—' }}</div>
                                <div class="cell-muted small">{{ $c->hora }}</div>
                            </td>
                            <td>
                                <div class="cell-strong">{{ $c->cliente->nombre_completo ?? '—' }}</div>
                                <div class="cell-muted small">{{ $c->vehiculo->placa ?? '—' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ ucfirst($c->tipo) }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $c->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($c->estado) {
                                        'confirmada' => 'info',
                                        'atendida' => 'success',
                                        'cancelada' => 'danger',
                                        default => 'warning',
                                    }"
                                    :icon="match($c->estado) {
                                        'confirmada' => 'bi-check2-circle',
                                        'atendida' => 'bi-check-circle-fill',
                                        'cancelada' => 'bi-x-circle-fill',
                                        default => 'bi-hourglass-split',
                                    }"
                                    :label="ucfirst($c->estado)" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.citas.show', $c) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.citas.edit', $c) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    @if (! $c->ordenTrabajo)
                                        <form method="POST"
                                              action="{{ route('admin.citas.convertir-orden', $c) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn-icon btn-icon--primary"
                                                    title="Convertir a orden"
                                                    aria-label="Convertir cita a orden"
                                                    data-tp-confirm
                                                    data-tp-confirm-title="¿Convertir cita en orden?"
                                                    data-tp-confirm-message="Se creará una orden de trabajo a partir de esta cita."
                                                    data-tp-confirm-text="Convertir"
                                                    data-tp-confirm-icon="info">
                                                <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($c->estado !== 'cancelada')
                                        <form method="POST"
                                              action="{{ route('admin.citas.cancelar', $c) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn-icon btn-icon--danger"
                                                    title="Cancelar"
                                                    aria-label="Cancelar cita"
                                                    data-tp-confirm
                                                    data-tp-confirm-title="¿Cancelar cita?"
                                                    data-tp-confirm-message="La cita del {{ $c->fecha?->format('d/m/Y') }} a las {{ $c->hora }} será cancelada."
                                                    data-tp-confirm-text="Cancelar"
                                                    data-tp-confirm-icon="warning">
                                                <i class="bi bi-x-circle" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron citas con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$citas" />
        </div>
    @endif
@endsection
