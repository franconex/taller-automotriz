@extends('layouts.admin')

@section('title', 'Comprobantes')
@section('navbar-title', 'Comprobantes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Comprobantes</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Comprobantes"
        description="Comprobantes emitidos a clientes por servicios y repuestos.">
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.comprobantes.index')"
        search-name="q"
        search-placeholder="Buscar por número, NIT o razón social">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="emitido"  @selected(request('estado') === 'emitido')>Emitido</option>
                <option value="anulado"  @selected(request('estado') === 'anulado')>Anulado</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($comprobantes->isEmpty() && ! request()->has('q') && ! request()->has('estado'))
        <x-admin.empty-state
            icon="bi-receipt"
            title="Aún no hay comprobantes emitidos"
            message="Los comprobantes se generan al registrar pagos." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de comprobantes">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th class="d-none d-md-table-cell">Fecha</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comprobantes as $c)
                        <tr>
                            <td class="cell-strong">{{ $c->numero }}</td>
                            <td>
                                <div class="cell-strong">{{ $c->cliente->nombre_completo ?? '—' }}</div>
                                <div class="cell-muted small">{{ $c->razon_social ?: ($c->nit_ci ?: 'Consumidor final') }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $c->fecha_emision?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end cell-strong">{{ number_format((float) $c->monto_total, 2, ',', '.') }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$c->estado === 'emitido' ? 'success' : 'danger'"
                                    :icon="$c->estado === 'emitido' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"
                                    :label="ucfirst($c->estado)" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.comprobantes.show', $c) }}"
                                       class="btn-icon"
                                       title="Ver"
                                       aria-label="Ver">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.comprobantes.edit', $c) }}"
                                       class="btn-icon btn-icon--primary"
                                       title="Editar"
                                       aria-label="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    @if ($c->estado !== 'anulado')
                                        <form method="POST"
                                              action="{{ route('admin.comprobantes.anular', $c) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn-icon btn-icon--danger"
                                                    title="Anular"
                                                    aria-label="Anular"
                                                    data-tp-confirm
                                                    data-tp-confirm-title="¿Anular comprobante?"
                                                    data-tp-confirm-message="El comprobante {{ $c->numero }} será anulado."
                                                    data-tp-confirm-text="Anular"
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
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron comprobantes con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$comprobantes" />
        </div>
    @endif
@endsection
