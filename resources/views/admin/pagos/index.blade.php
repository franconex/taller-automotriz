@extends('layouts.admin')

@section('title', 'Pagos')
@section('navbar-title', 'Pagos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Pagos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Pagos"
        description="Pagos registrados a órdenes de trabajo con su método y estado.">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Registrar pago
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.pagos.index')"
        search-name="q"
        search-placeholder="Buscar por orden o comprobante">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="confirmado" @selected(request('estado') === 'confirmado')>Confirmado</option>
                <option value="anulado"    @selected(request('estado') === 'anulado')>Anulado</option>
            </select>
            <select name="metodo_pago_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los métodos</option>
                @foreach (($metodos ?? collect()) as $m)
                    <option value="{{ $m->id }}" @selected((string) request('metodo_pago_id') === (string) $m->id)>{{ $m->nombre }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control" style="max-width:170px;" onchange="this.form.submit()" aria-label="Filtrar por fecha">
        </x-slot:filters>
    </x-admin.filters>

    @if ($pagos->isEmpty() && ! request()->has('q') && ! request()->has('estado') && ! request()->has('metodo_pago_id') && ! request()->has('fecha'))
        <x-admin.empty-state
            icon="bi-cash-coin"
            title="Aún no hay pagos registrados"
            message="Registra el primer pago para empezar a controlar la cobranza."
            :action-label="'Registrar pago'"
            :action-href="route('admin.pagos.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de pagos">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Orden</th>
                        <th class="d-none d-md-table-cell">Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $p)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $p->fecha_pago?->format('d/m/Y') ?? '—' }}</div>
                                <div class="cell-muted small">{{ $p->fecha_pago?->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="cell-strong">{{ $p->ordenTrabajo->numero_orden ?? '—' }}</div>
                                <div class="cell-muted small">{{ $p->numero_comprobante ?? 'Sin comprobante' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $p->metodoPago->nombre ?? '—' }}</td>
                            <td class="text-end cell-strong">{{ number_format((float) $p->monto, 2, ',', '.') }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$p->estado === 'confirmado' ? 'success' : 'danger'"
                                    :icon="$p->estado === 'confirmado' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"
                                    :label="ucfirst($p->estado)" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.pagos.show', $p) }}"
                                       class="btn-icon"
                                       title="Ver detalle"
                                       aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.pagos.edit', $p) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    @if ($p->estado !== 'anulado')
                                        <form method="POST"
                                              action="{{ route('admin.pagos.anular', $p) }}"
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
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron pagos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$pagos" />
        </div>
    @endif
@endsection
