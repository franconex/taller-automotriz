@extends('layouts.admin')

@section('title', 'Editar inventario')
@section('navbar-title', 'Editar inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar inventario"
        :description="$inventario->repuesto->nombre . ' — ' . $inventario->sucursal->nombre">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.inventario.update', $inventario) }}">
            @csrf
            @method('PUT')

            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Datos del registro</h3>
                <p class="cell-muted small">Solo puedes ajustar la cantidad reservada. Para modificar el stock real, registra un movimiento de inventario.</p>
                <x-admin.form-field name="cantidad_actual" label="Cantidad actual" :value="$inventario->cantidad_actual" disabled icon="bi-123" />
                <x-admin.form-field name="cantidad_reservada" type="number" label="Cantidad reservada" :value="$inventario->cantidad_reservada" icon="bi-bookmark" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
