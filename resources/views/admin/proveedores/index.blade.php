@extends('layouts.admin')

@section('title', 'Proveedores')
@section('navbar-title', 'Proveedores')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Proveedores</li>
@endsection

@section('content')
    <x-admin.page-header title="Proveedores" description="Empresas que suministran repuestos al taller.">
        <x-slot:actions>
            @if (Auth::user()->tienePermiso('proveedores.crear'))
            <a href="{{ route('admin.proveedores.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo proveedor</a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.proveedores.index')" search-name="q" search-placeholder="Buscar proveedor">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option><option value="1" @selected(request('estado')==='1')>Activos</option><option value="0" @selected(request('estado')==='0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($proveedores->isEmpty() && !request()->has('q') && !request()->has('estado'))
        <x-admin.empty-state icon="bi-truck" title="Aún no hay proveedores" message="Registra el primer proveedor para gestionar compras."
            @if (Auth::user()->tienePermiso('proveedores.crear')) :action-label="'Nuevo proveedor'" :action-href="route('admin.proveedores.create')" @endif />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de proveedores">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th class="d-none d-md-table-cell">Contacto</th>
                        <th class="d-none d-lg-table-cell">Teléfono</th>
                        <th class="d-none d-lg-table-cell">NIT</th>
                        <th class="d-none d-md-table-cell">Repuestos</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proveedores as $p)
                        <tr style="border-left:3px solid #f59e0b;">
                            <td>
                                <div class="cell-label">
                                    <i class="bi bi-building"></i>
                                    <div>
                                        <div class="cell-strong">{{ $p->nombre_empresa }}</div>
                                        <div class="cell-secondary">{{ $p->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $p->contacto ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell"><span class="cell-label"><i class="bi bi-telephone"></i> {{ $p->telefono }}</span></td>
                            <td class="d-none d-lg-table-cell cell-secondary">{{ $p->nit ?? '—' }}</td>
                            <td class="d-none d-md-table-cell"><span class="badge" style="background:#e2e8f0;color:#475569;">{{ $p->repuestos_count }}</span></td>
                            <td><x-admin.status-badge :tone="$p->estado ? 'success' : 'neutral'" :icon="$p->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'" :label="$p->estado ? 'Activo' : 'Inactivo'" /></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.proveedores.show', $p) }}" class="btn-icon" title="Ver detalles"><i class="bi bi-eye"></i></a>
                                    @if (Auth::user()->tienePermiso('proveedores.editar'))
                                    <a href="{{ route('admin.proveedores.edit', $p) }}" class="btn-icon btn-icon--primary" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    <form method="POST" action="{{ route('admin.proveedores.toggle', $p) }}" class="d-inline">@csrf @method('PATCH')<button type="submit" class="btn-icon" title="{{ $p->estado ? 'Desactivar' : 'Activar' }}"><i class="bi {{ $p->estado ? 'bi-pause-circle' : 'bi-play-circle' }}"></i></button></form>
                                    <form method="POST" action="{{ route('admin.proveedores.destroy', $p) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn-icon btn-icon--danger" title="Eliminar"><i class="bi bi-trash3"></i></button></form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-0"><x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron proveedores con los filtros aplicados." /></td></tr>
                    @endforelse
                </tbody>
            </table>
            <x-admin.table-pagination :paginator="$proveedores" />
        </div>
    @endif
@endsection