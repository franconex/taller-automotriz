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

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.ordenes.update', $orden) }}">
            @csrf
            @method('PUT')
            @include('admin.ordenes._form', ['orden' => $orden])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.ordenes.show', $orden) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    <div class="admin-table-wrap mt-3">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 fw-bold mb-0">Repuestos</h2>
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
