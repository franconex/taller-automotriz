@extends('layouts.admin')

@section('title', 'Editar orden')
@section('navbar-title', 'Editar orden')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar orden"
        :description="'Modifica los datos de la orden ' . $orden->numero_orden . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.ordenes.update', $orden) }}">
            @csrf
            @method('PUT')
            @include('admin.ordenes._form', ['orden' => $orden])

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.ordenes.show', $orden) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    <div class="admin-card-modern mt-3 p-0 overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:#f8fafc;">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;border-radius:6px;background:#e8f4fd;color:#2563eb;font-size:0.85rem;">
                    <i class="bi bi-box-seam"></i>
                </span>
                <h2 class="fw-bold mb-0" style="font-size:0.95rem;">Repuestos</h2>
            </div>
            <a href="{{ route('admin.ordenes.repuestos', $orden) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-gear" aria-hidden="true"></i>
                Gestionar repuestos
            </a>
        </div>
        <div class="table-responsive">
            <table class="admin-table" aria-label="Repuestos asignados">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="text-end">Cant.</th>
                        <th class="text-end">Subtotal</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $detallesRep = $orden->detalles?->where('tipo', 'repuesto') ?? collect();
                    @endphp
                    @forelse ($detallesRep as $detalle)
                        @php
                            $sinStock = str_contains((string) $detalle->observaciones, 'SIN STOCK');
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $detalle->repuesto->nombre ?? $detalle->descripcion }}</div>
                                <div class="cell-muted small">{{ $detalle->repuesto->codigo ?? '' }}</div>
                            </td>
                            <td class="text-end">{{ (int) $detalle->cantidad }}</td>
                            <td class="text-end">$ {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                            <td>
                                @if ($sinStock)
                                    <x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" label="Sin stock" />
                                @else
                                    <x-admin.status-badge tone="success" icon="bi-check-circle-fill" label="Disponible" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center cell-muted py-3">
                                No se han asignado repuestos a esta orden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection