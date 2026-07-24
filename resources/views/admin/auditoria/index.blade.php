@extends('layouts.admin')

@section('title', 'Auditoría')
@section('navbar-title', 'Auditoría')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Auditoría</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Auditoría"
        description="Registro de acciones realizadas en el sistema.">
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.auditoria.index')"
        search-name="q"
        search-placeholder="Buscar por usuario, módulo o acción">
        <x-slot:filters>
            <select name="modulo" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los módulos</option>
                @foreach (($modulos ?? []) as $m)
                    <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
            <input type="date" name="desde" value="{{ request('desde') }}" class="form-control" style="max-width:160px;" aria-label="Desde">
            <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" style="max-width:160px;" aria-label="Hasta">
            <button class="btn btn-outline-secondary btn-sm" type="submit">
                <i class="bi bi-funnel" aria-hidden="true"></i>
                Aplicar
            </button>
        </x-slot:filters>
    </x-admin.filters>

    @if ($registros->isEmpty() && ! request()->has('q') && ! request()->has('modulo') && ! request()->has('desde'))
        <x-admin.empty-state
            icon="bi-journal-text"
            title="Sin actividad registrada"
            message="El historial de auditoría se poblará conforme se realicen acciones." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Registro de auditoría">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th class="d-none d-md-table-cell">Módulo</th>
                        <th class="d-none d-md-table-cell">Acción</th>
                        <th class="d-none d-lg-table-cell">Entidad</th>
                        <th class="col-actions">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $a)
                        <tr>
                            <td class="cell-muted">{{ $a->fecha_accion?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="cell-strong">{{ $a->usuario->nombre ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ ucfirst($a->modulo) }}</td>
                            <td class="d-none d-md-table-cell">
                                <x-admin.status-badge tone="info" :label="ucfirst($a->accion)" />
                            </td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $a->entidad_tipo }} #{{ $a->entidad_id ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.auditoria.show', $a) }}"
                                   class="btn-icon btn-icon--primary"
                                   title="Ver detalle"
                                   aria-label="Ver detalle">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-0">
                            <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron registros con los filtros aplicados." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
            <x-admin.table-pagination :paginator="$registros" />
        </div>
    @endif
@endsection
