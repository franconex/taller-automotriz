@extends('layouts.admin')

@section('title', 'Nueva solicitud de compra')
@section('navbar-title', 'Nueva solicitud')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.index') }}">Solicitudes de compra</a></li>
    <li class="active" aria-current="page">Nueva solicitud</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nueva solicitud de compra"
        description="Seleccioná los productos con stock bajo para solicitar su compra." />

    <form method="POST" action="{{ route('admin.solicitudes-compra.store') }}" id="solicitudForm">
        @csrf

        <div class="admin-table-wrap mb-3">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h6 fw-bold mb-0">Productos con stock bajo</h2>
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="soloStockBajo" checked onchange="filtrarStockBajo()">
                        <label class="form-check-label" for="soloStockBajo">Solo stock bajo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="seleccionarTodos" onchange="toggleTodos()">
                        <label class="form-check-label" for="seleccionarTodos">Seleccionar todos</label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="admin-table" id="productosTable">
                    <thead>
                        <tr>
                            <th style="width:40px;"></th>
                            <th>Producto</th>
                            <th class="text-end">Stock actual</th>
                            <th class="text-end">Stock mínimo</th>
                            <th class="text-end d-none d-md-table-cell">Stock máximo</th>
                            <th class="text-end d-none d-md-table-cell">Disponible</th>
                            <th style="width:120px;" class="text-end">Cant. solicitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventario as $item)
                            @php
                                $rep = $item->repuesto;
                                $disponible = (int) $item->cantidad_actual - (int) $item->cantidad_reservada;
                                $esStockBajo = $disponible <= (int) ($rep->stock_minimo ?? 0);
                                $sugerido = max(1, ($rep->stock_maximo ?? ($rep->stock_minimo * 2)) - $disponible);
                            @endphp
                            <tr class="producto-row {{ $esStockBajo ? 'stock-bajo' : '' }}" data-stock-bajo="{{ $esStockBajo ? 'true' : 'false' }}">
                                <td>
                                    <input class="form-check-input producto-checkbox"
                                           type="checkbox"
                                           name="productos[{{ $loop->index }}][repuesto_id]"
                                           value="{{ $rep->id }}"
                                           data-index="{{ $loop->index }}"
                                           data-sugerido="{{ $sugerido }}"
                                           {{ $esStockBajo ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <div class="cell-strong">{{ $rep->nombre ?? '—' }}</div>
                                    <div class="cell-muted small">{{ $rep->codigo ?? '' }}</div>
                                </td>
                                <td class="text-end">{{ $item->cantidad_actual }}</td>
                                <td class="text-end">{{ $rep->stock_minimo ?? 0 }}</td>
                                <td class="text-end d-none d-md-table-cell">{{ $rep->stock_maximo ?? '—' }}</td>
                                <td class="text-end d-none d-md-table-cell">
                                    <x-admin.status-badge
                                        :tone="$esStockBajo ? 'danger' : 'success'"
                                        :icon="$esStockBajo ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'"
                                        :label="$disponible" />
                                </td>
                                <td>
                                    <input type="number"
                                           class="form-control form-control-sm cantidad-input text-end"
                                           name="productos[{{ $loop->index }}][cantidad]"
                                           value="{{ $sugerido }}"
                                           min="1"
                                           disabled
                                           data-index="{{ $loop->index }}"
                                           style="max-width:110px;margin-left:auto;">
                                    <input type="hidden" name="productos[{{ $loop->index }}][stock_actual]" value="{{ $item->cantidad_actual }}">
                                    <input type="hidden" name="productos[{{ $loop->index }}][stock_minimo]" value="{{ $rep->stock_minimo ?? 0 }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center cell-muted py-4">
                                    No hay productos en el inventario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <x-admin.form-field name="prioridad" label="Prioridad" type="select" :value="old('prioridad', 'media')" required>
                    <option value="alta" @selected(old('prioridad') === 'alta')>Alta</option>
                    <option value="media" @selected(old('prioridad') === 'media' || !old('prioridad'))>Media</option>
                    <option value="baja" @selected(old('prioridad') === 'baja')>Baja</option>
                </x-admin.form-field>
            </div>
            @if ($sucursales->count() > 1)
                <div class="col-12 col-md-6">
                    <x-admin.form-field name="sucursal_id" label="Sucursal" type="select" :value="old('sucursal_id')">
                        <option value="">Seleccionar...</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}" @selected((int) old('sucursal_id', auth()->user()->sucursal_id ?? session('admin_sucursal_id') ?? '') === $s->id)>{{ $s->nombre }}</option>
                        @endforeach
                    </x-admin.form-field>
                </div>
            @endif
            <div class="col-12">
                <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="old('observaciones')" />
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" id="btnEnviar">
                <i class="bi bi-send" aria-hidden="true"></i>
                Generar solicitud
            </button>
            <a href="{{ route('admin.solicitudes-compra.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function filtrarStockBajo() {
    const soloBajo = document.getElementById('soloStockBajo').checked;
    document.querySelectorAll('.producto-row').forEach(row => {
        if (soloBajo && row.dataset.stockBajo === 'false') {
            row.style.display = 'none';
        } else {
            row.style.display = '';
        }
    });
}

function toggleTodos() {
    const checked = document.getElementById('seleccionarTodos').checked;
    document.querySelectorAll('.producto-checkbox:visible').forEach(cb => {
        cb.checked = checked;
        const idx = cb.dataset.index;
        const input = document.querySelector(`.cantidad-input[data-index="${idx}"]`);
        if (input) input.disabled = !checked;
    });
}

document.querySelectorAll('.producto-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const idx = this.dataset.index;
        const input = document.querySelector(`.cantidad-input[data-index="${idx}"]`);
        if (input) input.disabled = !this.checked;
    });
});

document.getElementById('solicitudForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.producto-checkbox:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert('Seleccioná al menos un producto.');
        return;
    }
    if (!confirm('¿Generar solicitud de compra con ' + checked.length + ' producto(s)?')) {
        e.preventDefault();
    }
});

filtrarStockBajo();
</script>
@endpush
