@extends('layouts.admin')

@section('title', $inventario->repuesto->nombre ?? 'Inventario')
@section('navbar-title', $inventario->repuesto->nombre ?? 'Inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li class="active" aria-current="page">{{ $inventario->repuesto->nombre ?? '' }}</li>
@endsection

@section('content')
    <x-admin.page-header :title="$inventario->repuesto->nombre ?? 'Inventario'" :description="'Stock: ' . $inventario->cantidad_actual . ' unidades'">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-box-seam"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Existencias</h2>
                </div>
                @php $disponible = (int)$inventario->cantidad_actual - (int)$inventario->cantidad_reservada; @endphp
                <dl class="admin-meta">
                    <dt>Repuesto</dt>
                    <dd>
                        @if ($inventario->repuesto)
                            <a href="{{ route('admin.repuestos.edit', $inventario->repuesto) }}">{{ $inventario->repuesto->nombre }}</a>
                            <span class="d-block cell-secondary small">{{ $inventario->repuesto->codigo }}</span>
                        @else — @endif
                    </dd>
                    <dt>Stock actual</dt><dd class="fw-bold" style="font-size:1.2rem;">{{ $inventario->cantidad_actual }}</dd>
                    <dt>Reservado</dt><dd>{{ $inventario->cantidad_reservada }}</dd>
                    <dt>Disponible</dt>
                    <dd>
                        <span class="fw-bold {{ $disponible < 5 ? 'text-danger' : 'text-success' }}" style="font-size:1.1rem;">{{ $disponible }}</span>
                        @if ($disponible < 5)<span class="badge bg-danger ms-1">Stock bajo</span>@endif
                    </dd>
                    <dt>Última actualización</dt><dd>{{ $inventario->fecha_actualizacion?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module p-0 overflow-hidden">
                <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;border-radius:6px;background:#fef2f2;color:#dc2626;font-size:0.85rem;"><i class="bi bi-arrow-left-right"></i></span>
                        <h2 class="fw-bold mb-0" style="font-size:0.95rem;">Últimos movimientos</h2>
                    </div>
                    <a href="{{ route('admin.movimientos-inventario.index', ['repuesto_id' => $inventario->repuesto_id]) }}" class="cell-muted small">Ver todos</a>
                </div>
                @if ($inventario->movimientos->isEmpty())
                    <div class="p-4 text-center cell-secondary">Sin movimientos registrados.</div>
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
                                    <td class="cell-secondary small">{{ $m->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="match($m->tipo) { 'entrada' => 'success', 'salida' => 'warning', 'ajuste' => 'info', default => 'neutral' }"
                                            :label="ucfirst($m->tipo)" />
                                    </td>
                                    <td class="text-end cell-strong">{{ $m->cantidad }}</td>
                                    <td class="text-end cell-secondary small">{{ $m->existencia_anterior }} → {{ $m->existencia_nueva }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection