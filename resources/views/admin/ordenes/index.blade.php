@extends('layouts.admin')

@section('title', 'Órdenes de trabajo')
@section('navbar-title', 'Órdenes de trabajo')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Órdenes de trabajo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Órdenes de trabajo"
        :description="$esMecanico ? 'Tus órdenes asignadas' : 'Órdenes de servicio emitidas, en proceso, finalizadas o entregadas.'">
        <x-slot:actions>
            @if (!$esMecanico && Auth::user()->tienePermiso('ordenes.crear'))
            <a href="{{ route('admin.ordenes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nueva orden
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.ordenes.index')"
        search-name="q"
        search-placeholder="Buscar por N° de orden, cliente o vehículo">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="recibida" @selected(request('estado') === 'recibida')>Recibida</option>
                <option value="diagnostico" @selected(request('estado') === 'diagnostico')>En diagnóstico</option>
                <option value="en_proceso" @selected(request('estado') === 'en_proceso')>En proceso</option>
                <option value="esperando_repuestos" @selected(request('estado') === 'esperando_repuestos')>Esperando repuestos</option>
                <option value="finalizada" @selected(request('estado') === 'finalizada')>Finalizada</option>
                <option value="entregada" @selected(request('estado') === 'entregada')>Entregada</option>
                <option value="anulada" @selected(request('estado') === 'anulada')>Anulada</option>
            </select>
            @if (!$esMecanico)
                <select name="mecanico_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                    <option value="">Todos los mecánicos</option>
                    @foreach (($mecanicos ?? collect()) as $m)
                        <option value="{{ $m->id }}" @selected((string) request('mecanico_id') === (string) $m->id)>{{ $m->empleado->nombre_completo ?? 'Mecánico #' . $m->id }}</option>
                    @endforeach
                </select>
            @endif
        </x-slot:filters>
    </x-admin.filters>

    @if ($ordenes->isEmpty() && ! request()->has('q') && ! request()->has('estado') && ! request()->has('sucursal_id') && ! request()->has('mecanico_id'))
        <x-admin.empty-state
            icon="bi-clipboard-check"
            :title="$esMecanico ? 'No tienes órdenes asignadas' : 'Aún no hay órdenes de trabajo'"
            :message="$esMecanico ? 'Cuando el recepcionista te asigne una orden, aparecerá aquí.' : 'Emite la primera orden de trabajo para iniciar el flujo operativo.'"
            :action-label="(!$esMecanico && Auth::user()->tienePermiso('ordenes.crear')) ? 'Nueva orden' : null"
            :action-href="(!$esMecanico && Auth::user()->tienePermiso('ordenes.crear')) ? route('admin.ordenes.create') : null" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de órdenes de trabajo">
                <thead>
                    <tr>
                        <th>N° de orden</th>
                        <th>Cliente / Vehículo</th>
                        @if (!$esMecanico)
                        <th class="d-none d-md-table-cell">Mecánico</th>
                        @endif
                        <th class="d-none d-md-table-cell">Emisión</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $o)
                        @php
                            $asignacion = $o->asignaciones->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $o->numero_orden }}</div>
                                <div class="cell-muted small">Orden #{{ $o->id }}</div>
                            </td>
                            <td>
                                <div class="cell-strong">{{ $o->cliente->nombre_completo ?? '—' }}</div>
                                <div class="cell-muted small">{{ $o->vehiculo->placa ?? '—' }}</div>
                            </td>
                            @if (!$esMecanico)
                            <td class="d-none d-md-table-cell cell-muted">
                                {{ $asignacion?->mecanico?->empleado?->nombre_completo ?? '—' }}
                            </td>
                            @endif
                            <td class="d-none d-md-table-cell cell-muted">
                                {{ $o->fecha_emision?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($o->estado) {
                                        'recibida' => 'info',
                                        'diagnostico' => 'warning',
                                        'en_proceso' => 'warning',
                                        'finalizada' => 'success',
                                        'entregada' => 'success',
                                        'anulada' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :icon="match($o->estado) {
                                        'recibida' => 'bi-inbox-fill',
                                        'diagnostico' => 'bi-search',
                                        'en_proceso' => 'bi-gear-fill',
                                        'finalizada' => 'bi-check-circle-fill',
                                        'entregada' => 'bi-truck',
                                        'anulada' => 'bi-x-circle-fill',
                                        default => 'bi-circle',
                                    }"
                                    :label="ucfirst(str_replace('_', ' ', $o->estado))" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.ordenes.show', $o) }}"
                                       class="btn-icon"
                                       title="Ver detalle"
                                       aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    @if ($esMecanico && $asignacion)
                                    <a href="{{ route('admin.ordenes.show', $o) }}" class="btn-icon btn-icon--primary" title="Gestionar">
                                        <i class="bi bi-gear" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                    @if (!$esMecanico && Auth::user()->tienePermiso('ordenes.editar'))
                                    <a href="{{ route('admin.ordenes.edit', $o) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                    @if (!$esMecanico && $o->estado !== 'anulada')
                                    <form method="POST"
                                          action="{{ route('admin.ordenes.cancelar', $o) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Anular"
                                                aria-label="Anular">
                                            <i class="bi bi-x-circle" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esMecanico ? 5 : 6 }}" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron órdenes con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$ordenes" />
        </div>
    @endif
@endsection
