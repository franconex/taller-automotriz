@extends('layouts.admin')

@section('title', $inventario->repuesto->nombre ?? 'Inventario')
@section('navbar-title', $inventario->repuesto->nombre ?? 'Inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li class="active" aria-current="page">{{ $inventario->repuesto->nombre ?? '' }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$inventario->repuesto->nombre ?? 'Inventario'"
        :description="'Stock: ' . $inventario->cantidad_actual . ' unidades'">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.inventario.edit', $inventario) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Existencias</h2>
                <dl class="admin-meta">
                    <dt>Repuesto</dt>
                    <dd>
                        @if ($inventario->repuesto)
                            <a href="{{ route('admin.repuestos.edit', $inventario->repuesto) }}">{{ $inventario->repuesto->nombre }}</a>
                            <span class="d-block cell-muted small">{{ $inventario->repuesto->codigo }}</span>
                        @else — @endif
                    </dd>
                    <dt>Stock actual</dt><dd><strong>{{ $inventario->cantidad_actual }}</strong></dd>
                    <dt>Reservado</dt><dd>{{ $inventario->cantidad_reservada }}</dd>
                    <dt>Disponible</dt><dd class="{{ (int) $inventario->cantidad_actual - (int) $inventario->cantidad_reservada < 5 ? 'text-danger fw-bold' : '' }}">
                        {{ (int) $inventario->cantidad_actual - (int) $inventario->cantidad_reservada }}
                        @if ((int) $inventario->cantidad_actual - (int) $inventario->cantidad_reservada < 5)
                            <span class="badge bg-danger ms-1">Stock bajo</span>
                        @endif
                    </dd>
                    <dt>Última actualización</dt><dd>{{ $inventario->fecha_actualizacion?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0">Últimos movimientos</h2>
                    <a href="{{ route('admin.movimientos-inventario.index', ['repuesto_id' => $inventario->repuesto_id]) }}" class="cell-muted small">Ver todos</a>
                </div>
                @if ($inventario->movimientos->isEmpty())
                    <div class="p-4 text-center cell-muted">Sin movimientos registrados.</div>
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Existencias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventario->movimientos as $m)
                                <tr>
                                    <td class="cell-muted small">{{ $m->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="match($m->tipo) {
                                                'entrada' => 'success',
                                                'salida' => 'warning',
                                                'ajuste' => 'info',
                                                default => 'neutral',
                                            }"
                                            :label="ucfirst($m->tipo)" />
                                    </td>
                                    <td class="text-end cell-strong">{{ $m->cantidad }}</td>
                                    <td class="text-end cell-muted small">{{ $m->existencia_anterior }} → {{ $m->existencia_nueva }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
