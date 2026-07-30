@extends('layouts.admin')

@section('title', 'Servicios')
@section('navbar-title', 'Servicios')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Servicios</li>
@endsection

@section('content')
    <x-admin.page-header title="Servicios" description="Servicios del taller: precio base y duración estimada.">
        <x-slot:actions>
            @if (Auth::user()->tienePermiso('servicios.crear'))
                <a href="{{ route('admin.servicios.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo servicio</a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.servicios.index')" search-name="q" search-placeholder="Buscar servicio">
        <x-slot:filters>
            <select name="tipo_servicio_id" class="form-select" style="max-width:220px;" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                @foreach (($tipos ?? collect()) as $t)
                    <option value="{{ $t->id }}" @selected((string) request('tipo_servicio_id') === (string) $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($servicios->isEmpty() && ! request()->has('q') && ! request()->has('tipo_servicio_id') && ! request()->has('estado'))
        <x-admin.empty-state icon="bi-gear" title="Aún no hay servicios definidos" message="Crea el catálogo de servicios ofrecidos por el taller."
            @if (Auth::user()->tienePermiso('servicios.crear'))
                :action-label="'Nuevo servicio'" :action-href="route('admin.servicios.create')" @endif />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de servicios">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th class="d-none d-md-table-cell">Tipo</th>
                        <th class="d-none d-lg-table-cell text-end">Precio base</th>
                        <th class="d-none d-lg-table-cell text-end">Duración</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($servicios as $s)
                        <tr style="border-left:3px solid #0ea5e9;">
                            <td>
                                <div class="cell-label">
                                    <i class="bi bi-gear"></i>
                                    <div>
                                        <div class="cell-strong">{{ $s->nombre }}</div>
                                        <div class="cell-secondary">Servicio #{{ $s->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge rounded-pill" style="background:#e8f4fd;color:#2563eb;font-weight:500;">{{ $s->tipoServicio->nombre ?? '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell text-end fw-semibold">
                                Bs. {{ number_format((float) $s->precio_base, 2, ',', '.') }}
                            </td>
                            <td class="d-none d-lg-table-cell text-end cell-secondary">
                                {{ $s->duracion_estimada_minutos ? $s->duracion_estimada_minutos . ' min' : '—' }}
                            </td>
                            <td>
                                <x-admin.status-badge :tone="$s->estado ? 'success' : 'neutral'"
                                    :icon="$s->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                                    :label="$s->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    @if (Auth::user()->tienePermiso('servicios.editar'))
                                    <a href="{{ route('admin.servicios.edit', $s) }}" class="btn-icon btn-icon--primary" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    <form method="POST" action="{{ route('admin.servicios.toggle', $s) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-icon" title="{{ $s->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi {{ $s->estado ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.servicios.destroy', $s) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon--danger" title="Eliminar"><i class="bi bi-trash3"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-0"><x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron servicios con los filtros aplicados." /></td></tr>
                    @endforelse
                </tbody>
            </table>
            <x-admin.table-pagination :paginator="$servicios" />
        </div>
    @endif
@endsection