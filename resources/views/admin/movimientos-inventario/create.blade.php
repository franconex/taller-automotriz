@extends('layouts.admin')

@section('title', 'Nuevo movimiento de inventario')
@section('navbar-title', 'Nuevo movimiento')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.movimientos-inventario.index') }}">Movimientos de inventario</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header title="Nuevo movimiento de inventario" description="Registra una entrada, salida o ajuste de stock.">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.movimientos-inventario.store') }}">
            @csrf
            @php
                $sucursalActualId = auth()->user()->sucursal_id ?? session('admin_sucursal_id');
            @endphp
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="admin-card-module">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-box-seam" style="font-size:1rem;"></i></span>
                            <h3 class="fw-bold mb-0" style="font-size:1rem;">Repuesto</h3>
                        </div>
                        <div class="mb-0">
                            <label for="field-repuesto_id" class="form-label fw-medium">Repuesto <span class="required">*</span></label>
                            <select name="repuesto_id" id="field-repuesto_id" required class="form-select{{ $errors->has('repuesto_id') ? ' is-invalid' : '' }}">
                                <option value="">— Selecciona un repuesto —</option>
                                @foreach (($repuestos ?? collect()) as $r)
                                    <option value="{{ $r->id }}" @selected(old('repuesto_id') == $r->id)>{{ $r->nombre }} ({{ $r->codigo }})</option>
                                @endforeach
                            </select>
                            @if ($errors->has('repuesto_id'))<div class="invalid-feedback d-block">{{ $errors->first('repuesto_id') }}</div>@endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="admin-card-module">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-building" style="font-size:1rem;"></i></span>
                            <h3 class="fw-bold mb-0" style="font-size:1rem;">Sucursal origen</h3>
                        </div>
                        <div class="mb-0">
                            <label for="field-sucursal_origen_id" class="form-label fw-medium">Sucursal <span class="required">*</span></label>
                            <select name="sucursal_origen_id" id="field-sucursal_origen_id" required class="form-select{{ $errors->has('sucursal_origen_id') ? ' is-invalid' : '' }}">
                                <option value="">— Selecciona una sucursal —</option>
                                @foreach (($todasLasSucursales ?? collect()) as $s)
                                    <option value="{{ $s->id }}" @selected(old('sucursal_origen_id', $sucursalActualId) == $s->id)>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sucursal_origen_id'))<div class="invalid-feedback d-block">{{ $errors->first('sucursal_origen_id') }}</div>
                            @else<div class="form-text">Se preseleccionó tu sucursal actual. Puedes cambiarla.</div>@endif
                        </div>
                    </div>
                    <div class="admin-card-module mt-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-arrow-right" style="font-size:1rem;"></i></span>
                            <h3 class="fw-bold mb-0" style="font-size:1rem;">Sucursal destino</h3>
                        </div>
                        <div class="mb-0">
                            <label for="field-sucursal_destino_id" class="form-label fw-medium">Sucursal destino</label>
                            <select name="sucursal_destino_id" id="field-sucursal_destino_id" class="form-select{{ $errors->has('sucursal_destino_id') ? ' is-invalid' : '' }}">
                                <option value="">— Sin destino —</option>
                                @foreach (($todasLasSucursales ?? collect()) as $s)
                                    <option value="{{ $s->id }}" @selected(old('sucursal_destino_id') == $s->id)>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sucursal_destino_id'))<div class="invalid-feedback d-block">{{ $errors->first('sucursal_destino_id') }}</div>
                            @else<div class="form-text">Selecciona solo si deseas transferir a otra sucursal.</div>@endif
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="admin-card-module">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-sliders" style="font-size:1rem;"></i></span>
                            <h3 class="fw-bold mb-0" style="font-size:1rem;">Tipo y cantidad</h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="field-tipo" class="form-label fw-medium">Tipo <span class="required">*</span></label>
                                <select name="tipo" id="field-tipo" required class="form-select{{ $errors->has('tipo') ? ' is-invalid' : '' }}">
                                    <option value="">— Selecciona —</option>
                                    <optgroup label="Entradas">
                                        <option value="entrada" @selected(old('tipo') === 'entrada')>Entrada genérica</option>
                                        <option value="entrada_compra" @selected(old('tipo') === 'entrada_compra')>Entrada por compra</option>
                                        <option value="devolucion" @selected(old('tipo') === 'devolucion')>Devolución</option>
                                        <option value="ajuste_positivo" @selected(old('tipo') === 'ajuste_positivo')>Ajuste positivo</option>
                                    </optgroup>
                                    <optgroup label="Salidas">
                                        <option value="salida" @selected(old('tipo') === 'salida')>Salida genérica</option>
                                        <option value="consumo" @selected(old('tipo') === 'consumo')>Consumo interno</option>
                                        <option value="dañado" @selected(old('tipo') === 'dañado')>Dañado</option>
                                        <option value="vencido" @selected(old('tipo') === 'vencido')>Vencido</option>
                                        <option value="perdida" @selected(old('tipo') === 'perdida')>Pérdida</option>
                                        <option value="devolucion_proveedor" @selected(old('tipo') === 'devolucion_proveedor')>Devolución a proveedor</option>
                                        <option value="ajuste_negativo" @selected(old('tipo') === 'ajuste_negativo')>Ajuste negativo</option>
                                    </optgroup>
                                    <optgroup label="Otros">
                                        <option value="transferencia" @selected(old('tipo') === 'transferencia')>Transferencia entre sucursales</option>
                                    </optgroup>
                                </select>
                                @if ($errors->has('tipo'))<div class="invalid-feedback d-block">{{ $errors->first('tipo') }}</div>@endif
                            </div>
                            <div class="col-md-2">
                                <label for="field-cantidad" class="form-label fw-medium">Cantidad <span class="required">*</span></label>
                                <input id="field-cantidad" type="number" name="cantidad" value="{{ old('cantidad') }}" required min="1"
                                       class="form-control{{ $errors->has('cantidad') ? ' is-invalid' : '' }}" placeholder="1">
                                @if ($errors->has('cantidad'))<div class="invalid-feedback d-block">{{ $errors->first('cantidad') }}</div>@endif
                            </div>
                            <div class="col-md-7">
                                <label for="field-motivo" class="form-label fw-medium">Motivo <span class="required">*</span></label>
                                <input id="field-motivo" type="text" name="motivo" value="{{ old('motivo') }}" required maxlength="500"
                                       class="form-control{{ $errors->has('motivo') ? ' is-invalid' : '' }}" placeholder="Ej: Compra a proveedor">
                                @if ($errors->has('motivo'))<div class="invalid-feedback d-block">{{ $errors->first('motivo') }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.movimientos-inventario.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Registrar movimiento</button>
            </div>
        </form>
    </div>
@endsection

