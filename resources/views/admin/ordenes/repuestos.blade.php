@extends('layouts.admin')

@section('title', 'Repuestos - ' . $orden->numero_orden)
@section('navbar-title', 'Repuestos: ' . $orden->numero_orden)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li><a href="{{ route('admin.ordenes.show', $orden) }}">{{ $orden->numero_orden }}</a></li>
    <li class="active" aria-current="page">Repuestos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Repuestos y materiales"
        :description="'Gestión de repuestos para la orden ' . $orden->numero_orden">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.show', $orden) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver a la orden
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">

        <div class="col-12 col-lg-7">
            <div class="admin-table-wrap">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="h6 fw-bold mb-0">Repuestos en la orden</h2>
                    @php
                        $detallesSinStock = $orden->detalles->filter(fn ($d) => $d->tipo === 'repuesto' && str_contains((string) $d->observaciones, 'SIN STOCK'));
                    @endphp
                    @if ($detallesSinStock->isNotEmpty())
                        <a href="{{ route('admin.ordenes.sugerir-compra', $orden) }}"
                           class="btn btn-warning btn-sm"
                           onclick="return confirm('¿Crear solicitud de compra para los {{ $detallesSinStock->count() }} repuesto(s) sin stock?')">
                            <i class="bi bi-cart3" aria-hidden="true"></i>
                            Solicitar compra ({{ $detallesSinStock->count() }})
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="admin-table" aria-label="Repuestos asignados">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                                <th>Stock</th>
                                <th class="col-actions" style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $detallesRepuestos = $orden->detalles->where('tipo', 'repuesto');
                            @endphp
                            @forelse ($detallesRepuestos as $detalle)
                                @php
                                    $sinStock = str_contains((string) $detalle->observaciones, 'SIN STOCK');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="cell-strong">{{ $detalle->repuesto->nombre ?? $detalle->descripcion }}</div>
                                        <div class="cell-muted small">{{ $detalle->repuesto->codigo ?? '' }}</div>
                                    </td>
                                    <td class="text-end">{{ (int) $detalle->cantidad }}</td>
                                    <td class="text-end">$ {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                                    <td class="text-end">$ {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($sinStock)
                                            <x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" label="Sin stock" />
                                        @else
                                            <x-admin.status-badge tone="success" icon="bi-check-circle-fill" label="Disponible" />
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST"
                                              action="{{ route('admin.ordenes.detalles.destroy', [$orden, $detalle]) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn-icon btn-icon--danger"
                                                    title="Quitar repuesto"
                                                    aria-label="Quitar repuesto"
                                                    onclick="return confirm('¿Quitar este repuesto de la orden? El stock se restaurará.')">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center cell-muted py-4">
                                        No se han asignado repuestos a esta orden todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($detallesRepuestos->isNotEmpty())
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total repuestos:</td>
                                    <td class="text-end">$ {{ number_format((float) $orden->subtotal_repuestos, 2, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Agregar repuesto</h2>
                <form method="POST" action="{{ route('admin.ordenes.detalles.store', $orden) }}" id="agregarRepuestoForm">
                    @csrf

                    <div class="mb-3">
                        <label for="repuesto_id" class="form-label">Repuesto <span class="required" aria-hidden="true">*</span></label>
                        <select id="repuesto_id"
                                name="repuesto_id"
                                class="form-select @error('repuesto_id') is-invalid @enderror"
                                required>
                            <option value="">— Selecciona un repuesto —</option>
                            @foreach ($repuestos as $r)
                                @php
                                    $stockActual = (int) ($r->stock_actual ?? 0);
                                    $sinStock = $stockActual <= 0;
                                    $stockBajo = $stockActual <= (int) ($r->stock_minimo ?? 0);
                                @endphp
                                <option value="{{ $r->id }}"
                                        data-precio="{{ $r->precio_venta ?? 0 }}"
                                        data-stock="{{ $stockActual }}"
                                        data-sin-stock="{{ $sinStock ? 'true' : 'false' }}"
                                        {{ old('repuesto_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->codigo ? "[" . $r->codigo . "] " : "" }}{{ $r->nombre }}
                                    @if ($r->marca) ({{ $r->marca }}) @endif
                                    — Stock: {{ $stockActual }}
                                </option>
                            @endforeach
                        </select>
                        @error('repuesto_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="stockInfo" class="mb-3" style="display:none;">
                        <div id="stockInfoBadge"></div>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad <span class="required" aria-hidden="true">*</span></label>
                        <input type="number"
                               id="cantidad"
                               name="cantidad"
                               class="form-control @error('cantidad') is-invalid @enderror"
                               value="{{ old('cantidad', 1) }}"
                               min="1"
                               required>
                        @error('cantidad')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="precio_unitario" class="form-label">Precio unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   id="precio_unitario"
                                   name="precio_unitario"
                                   class="form-control @error('precio_unitario') is-invalid @enderror"
                                   value="{{ old('precio_unitario') }}"
                                   step="0.01"
                                   min="0">
                        </div>
                        <div class="form-text">Si se deja vacío, se usará el precio de venta del catálogo.</div>
                        @error('precio_unitario')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion"
                                  name="descripcion"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnAgregar">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                            Agregar a la orden
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
(function() {
    const select = document.getElementById('repuesto_id');
    const stockInfo = document.getElementById('stockInfo');
    const stockInfoBadge = document.getElementById('stockInfoBadge');
    const precioInput = document.getElementById('precio_unitario');
    const cantidadInput = document.getElementById('cantidad');
    const btnAgregar = document.getElementById('btnAgregar');

    function actualizarStock() {
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) {
            stockInfo.style.display = 'none';
            return;
        }

        const precio = parseFloat(option.dataset.precio) || 0;
        const stock = parseInt(option.dataset.stock) || 0;
        const sinStock = option.dataset.sinStock === 'true';

        if (!precioInput.value || precioInput.value == 0) {
            precioInput.value = precio;
        }

        const cantidad = parseInt(cantidadInput.value) || 1;
        let html = '';

        if (sinStock) {
            html = '<x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" label="Sin stock en esta sucursal" />';
            html += '<div class="form-text text-danger mt-1">El repuesto se agregará pero quedará marcado como "Sin stock". Deberás solicitar una compra.</div>';
        } else if (stock < cantidad) {
            html = '<x-admin.status-badge tone="warning" icon="bi-exclamation-triangle-fill" label="Stock insuficiente: ' + stock + ' disponible(s)" />';
            html += '<div class="form-text text-warning mt-1">No hay suficiente stock. Se agregará como "Sin stock".</div>';
        } else {
            html = '<x-admin.status-badge tone="success" icon="bi-check-circle-fill" label="Stock disponible: ' + stock + ' unidad(es)" />';
        }

        stockInfoBadge.innerHTML = html;
        stockInfo.style.display = 'block';
    }

    select.addEventListener('change', actualizarStock);
    cantidadInput.addEventListener('input', actualizarStock);

    if (select.value) {
        actualizarStock();
    }
})();
</script>
@endpush
