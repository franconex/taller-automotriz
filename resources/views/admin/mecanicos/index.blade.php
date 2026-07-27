@extends('layouts.admin')

@section('title', 'Mecánicos')
@section('navbar-title', 'Mecánicos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Mecánicos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Mecánicos"
        description="Personal técnico disponible para asignación de órdenes de trabajo.">
        <x-slot:actions>
            @if (Auth::user()->tienePermiso('mecanicos.crear'))
            <a href="{{ route('admin.mecanicos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo mecánico
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.mecanicos.index')"
        search-name="q"
        search-placeholder="Buscar por nombre o especialidad">
        <x-slot:filters>
            <select name="disponibilidad" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Toda disponibilidad</option>
                <option value="disponible" @selected(request('disponibilidad') === 'disponible')>Disponible</option>
                <option value="ocupado"    @selected(request('disponibilidad') === 'ocupado')>Ocupado</option>
                <option value="ausente"    @selected(request('disponibilidad') === 'ausente')>Ausente</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($mecanicos->isEmpty() && ! request()->has('q') && ! request()->has('disponibilidad'))
        <x-admin.empty-state
            icon="bi-tools"
            title="Aún no hay mecánicos registrados"
            message="Asigna el primer mecánico para iniciar las operaciones técnicas."
            @if (Auth::user()->tienePermiso('mecanicos.crear'))
            :action-label="'Nuevo mecánico'"
            :action-href="route('admin.mecanicos.create')"
            @endif
            />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de mecánicos">
                <thead>
                    <tr>
                        <th>Mecánico</th>
                        <th class="d-none d-md-table-cell">Especialidad</th>
                        <th class="d-none d-lg-table-cell">Sucursal</th>
                        <th>Disponibilidad</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mecanicos as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="admin-avatar admin-avatar--sm" aria-hidden="true">
                                        {{ mb_strtoupper(mb_substr($m->empleado->nombre_completo ?? 'M', 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="cell-strong">{{ $m->empleado->nombre_completo ?? '—' }}</div>
                                        <div class="cell-muted small">
                                            {{ $m->empleado->rol->nombre ?? '' }}
                                            @if ($m->empleado->cargo)
                                                — {{ $m->empleado->cargo }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $m->especialidad->nombre ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell cell-muted">{{ $m->empleado->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($m->disponibilidad) {
                                        'disponible' => 'success',
                                        'ocupado' => 'warning',
                                        'ausente' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :icon="match($m->disponibilidad) {
                                        'disponible' => 'bi-check-circle-fill',
                                        'ocupado' => 'bi-hourglass-split',
                                        'ausente' => 'bi-x-circle-fill',
                                        default => 'bi-circle',
                                    }"
                                    :label="ucfirst($m->disponibilidad)" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.mecanicos.show', $m) }}"
                                       class="btn-icon"
                                       title="Ver detalles"
                                       aria-label="Ver detalles">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    @if (Auth::user()->tienePermiso('mecanicos.editar'))
                                    <a href="{{ route('admin.mecanicos.edit', $m) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.mecanicos.toggle', $m) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn-icon"
                                                title="Cambiar disponibilidad"
                                                aria-label="Cambiar disponibilidad">
                                            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.mecanicos.destroy', $m) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-icon--danger"
                                                title="Eliminar"
                                                aria-label="Eliminar">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron mecánicos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$mecanicos" />
        </div>
    @endif
@endsection
