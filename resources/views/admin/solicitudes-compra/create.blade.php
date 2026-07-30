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
        description="Agregá los productos que deseas solicitar." />

    <form method="POST" action="{{ route('admin.solicitudes-compra.store') }}" id="solicitudForm">
        @csrf

        <div class="admin-card-modern p-4 mb-3">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-box-seam" style="font-size:1rem;"></i></span>
                    <h3 class="fw-bold mb-0" style="font-size:1rem;">Productos</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="btn-agregar-producto" title="Agregar otro producto">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <p class="cell-secondary small mb-3">Selecciona el producto y la cantidad que deseas solicitar.</p>
                <div id="productos-container">
                    <div class="producto-row border rounded-3 p-3 mb-3 position-relative" data-index="0">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-medium small">Producto <span class="required">*</span></label>
                                <select name="productos[0][repuesto_id]" class="form-select producto-select" required>
                                    <option value="">— Selecciona un producto —</option>
                                    @foreach ($inventario as $item)
                                        @php $rep = $item->repuesto; @endphp
                                        <option value="{{ $rep->id }}"
                                            data-stock="{{ $item->cantidad_actual }}"
                                            data-stock-minimo="{{ $rep->stock_minimo ?? 0 }}">
                                            {{ $rep->nombre }} ({{ $rep->codigo }}) — Stock: {{ $item->cantidad_actual }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium small">Cantidad <span class="required">*</span></label>
                                <input type="number" name="productos[0][cantidad]" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="cell-secondary small mb-2" style="padding:0.375rem 0;">
                                    <div>Stock: <strong class="stock-actual">0</strong></div>
                                    <div>Mín: <strong class="stock-minimo">0</strong></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="productos[0][stock_actual]" class="hidden-stock" value="0">
                        <input type="hidden" name="productos[0][stock_minimo]" class="hidden-stock" value="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card-modern p-4 mb-3">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="field-prioridad" class="form-label fw-medium">Prioridad <span class="required">*</span></label>
                    <select name="prioridad" id="field-prioridad" required class="form-select">
                        <option value="alta" @selected(old('prioridad') === 'alta')>Alta</option>
                        <option value="media" @selected(old('prioridad') === 'media' || !old('prioridad'))>Media</option>
                        <option value="baja" @selected(old('prioridad') === 'baja')>Baja</option>
                    </select>
                </div>
                @if ($sucursales->count() > 1)
                <div class="col-12 col-md-6">
                    <label for="field-sucursal_id" class="form-label fw-medium">Sucursal</label>
                    <select name="sucursal_id" id="field-sucursal_id" class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}" @selected((int) old('sucursal_id', auth()->user()->sucursal_id ?? session('admin_sucursal_id') ?? '') === $s->id)>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-12">
                    <label for="field-observaciones" class="form-label fw-medium">Observaciones</label>
                    <textarea name="observaciones" id="field-observaciones" rows="3"
                              class="form-control" placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4" id="btnEnviar">
                <i class="bi bi-send"></i>
                Generar solicitud
            </button>
            <a href="{{ route('admin.solicitudes-compra.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productos-container');
    const btnAgregar = document.getElementById('btn-agregar-producto');

    function actualizarStock(select) {
        const row = select.closest('.producto-row');
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) return;
        const stock = opt.getAttribute('data-stock') || '0';
        const stockMinimo = opt.getAttribute('data-stock-minimo') || '0';
        row.querySelector('.stock-actual').textContent = stock;
        row.querySelector('.stock-minimo').textContent = stockMinimo;
        row.querySelector('.hidden-stock[value]').value = stock;
        const hiddens = row.querySelectorAll('.hidden-stock');
        if (hiddens[0]) hiddens[0].value = stock;
        if (hiddens[1]) hiddens[1].value = stockMinimo;
    }

    function configurarSelect(select) {
        select.addEventListener('change', function () { actualizarStock(this); });
        if (select.value) actualizarStock(select);
    }

    let idx = container.querySelectorAll('.producto-row').length;

    btnAgregar.addEventListener('click', function () {
        const row = container.querySelector('.producto-row');
        if (!row) return;
        const clone = row.cloneNode(true);
        clone.dataset.index = idx;
        clone.querySelectorAll('input, select').forEach(function (el) {
            const name = el.getAttribute('name');
            if (name) el.name = name.replace(/\d+/, idx);
            if (el.type !== 'hidden') {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
            }
            el.classList.remove('is-invalid');
        });
        clone.querySelector('.stock-actual').textContent = '0';
        clone.querySelector('.stock-minimo').textContent = '0';

        if (!clone.querySelector('.btn-eliminar-producto')) {
            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.className = 'btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 btn-eliminar-producto';
            btnRemove.innerHTML = '<i class="bi bi-x-lg"></i>';
            btnRemove.title = 'Eliminar producto';
            btnRemove.addEventListener('click', function () { clone.remove(); });
            clone.style.position = 'relative';
            clone.appendChild(btnRemove);
        }

        container.appendChild(clone);
        const nuevoSelect = clone.querySelector('.producto-select');
        if (nuevoSelect) configurarSelect(nuevoSelect);
        idx++;
    });

    document.querySelectorAll('.producto-select').forEach(configurarSelect);

    document.getElementById('solicitudForm').addEventListener('submit', function (e) {
        const selects = document.querySelectorAll('.producto-select');
        let count = 0;
        selects.forEach(function (s) {
            if (s.value) count++;
        });
        if (count === 0) {
            e.preventDefault();
            alert('Agregá al menos un producto.');
            return;
        }
        if (!confirm('¿Generar solicitud de compra con ' + count + ' producto(s)?')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush