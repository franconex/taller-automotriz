@extends('layouts.admin')

@section('title', 'Nuevo movimiento de inventario')
@section('navbar-title', 'Nuevo movimiento')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.movimientos-inventario.index') }}">Movimientos de inventario</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo movimiento de inventario"
        description="Registra una entrada, salida o ajuste de stock.">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.movimientos-inventario.store') }}">
            @csrf
            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Ubicación y repuesto</h3>
                <x-admin.form-field name="sucursal_id" label="Sucursal" type="select" required>
                    <option value="">— Selecciona una sucursal —</option>
                    @foreach (($sucursales ?? collect()) as $s)
                        <option value="{{ $s->id }}" @selected(old('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </x-admin.form-field>
                <x-admin.form-field name="repuesto_id" label="Repuesto" type="select" required>
                    <option value="">— Selecciona un repuesto —</option>
                    @foreach (($repuestos ?? collect()) as $r)
                        <option value="{{ $r->id }}" @selected(old('repuesto_id') == $r->id)>{{ $r->codigo }} — {{ $r->nombre }}</option>
                    @endforeach
                </x-admin.form-field>
            </div>
            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Movimiento</h3>
                <x-admin.form-field name="tipo" label="Tipo" type="select" required>
                    <option value="entrada" @selected(old('tipo', 'entrada') === 'entrada')>Entrada (suma stock)</option>
                    <option value="salida"  @selected(old('tipo') === 'salida')>Salida (resta stock)</option>
                    <option value="ajuste"  @selected(old('tipo') === 'ajuste')>Ajuste (fija cantidad exacta)</option>
                </x-admin.form-field>
                <x-admin.form-field name="cantidad" type="number" label="Cantidad" :value="old('cantidad')" required icon="bi-123" />
                <x-admin.form-field name="motivo" label="Motivo" :value="old('motivo')" required icon="bi-chat-left-text" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.movimientos-inventario.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar movimiento
                </button>
            </div>
        </form>
    </div>
@endsection
