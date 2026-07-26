@extends('layouts.admin')

@section('title', 'Recibir productos — ' . $orden->numero)
@section('navbar-title', 'Recibir productos')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes-compra.index') }}">Órdenes de compra</a></li>
    <li><a href="{{ route('admin.ordenes-compra.show', $orden) }}">{{ $orden->numero }}</a></li>
    <li class="active" aria-current="page">Recibir productos</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Recibir productos"
        :description="$orden->numero . ' — ' . ($orden->proveedor->nombre_empresa ?? '')" />

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <dl class="admin-meta mb-0">
                    <dt>Proveedor</dt>
                    <dd>{{ $orden->proveedor->nombre_empresa ?? '—' }}</dd>
                    <dt>Sucursal</dt>
                    <dd>{{ $orden->sucursal->nombre ?? '—' }}</dd>
                    <dt>Orden</dt>
                    <dd>{{ $orden->numero }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.ordenes-compra.procesar-recepcion', $orden) }}" id="recepcionForm">
        @csrf

        <div class="admin-table-wrap mb-4">
            <div class="px-4 py-3 border-bottom">
                <h2 class="h6 fw-bold mb-0">Productos recibidos</h2>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Solicitado</th>
                            <th style="min-width:90px;" class="text-end">Recibida</th>
                            <th style="min-width:90px;" class="text-end">Aceptada</th>
                            <th style="min-width:90px;" class="text-end">Rechazada</th>
                            <th>Motivo rechazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orden->detalles as $i => $det)
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $det->id }}">
                                </td>
                                <td class="text-end">{{ $det->cantidad_solicitada }}</td>
                                <td>
                                    <input type="number" min="0" max="{{ $det->cantidad_solicitada }}"
                                           name="items[{{ $i }}][cantidad_recibida]"
                                           class="form-control form-control-sm text-end recibida-input"
                                           value="{{ old('items.' . $i . '.cantidad_recibida', $det->cantidad_recibida ?: $det->cantidad_solicitada) }}"
                                           data-index="{{ $i }}"
                                           required
                                           oninput="calcularRechazado({{ $i }})">
                                </td>
                                <td>
                                    <input type="number" min="0"
                                           name="items[{{ $i }}][cantidad_aceptada]"
                                           class="form-control form-control-sm text-end aceptada-input"
                                           value="{{ old('items.' . $i . '.cantidad_aceptada', $det->cantidad_aceptada ?: $det->cantidad_solicitada) }}"
                                           data-index="{{ $i }}"
                                           required
                                           oninput="calcularRechazado({{ $i }})">
                                </td>
                                <td>
                                    <input type="number" min="0"
                                           name="items[{{ $i }}][cantidad_rechazada]"
                                           class="form-control form-control-sm text-end rechazada-input"
                                           value="{{ old('items.' . $i . '.cantidad_rechazada', $det->cantidad_rechazada ?: 0) }}"
                                           data-index="{{ $i }}"
                                           readonly
                                           style="background:#f0f0f0;">
                                </td>
                                <td>
                                    <input type="text"
                                           name="items[{{ $i }}][motivo_rechazo]"
                                           class="form-control form-control-sm motivo-input"
                                           value="{{ old('items.' . $i . '.motivo_rechazo', $det->motivo_rechazo ?? '') }}"
                                           placeholder="Motivo (requerido si rechaza)">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="old('observaciones')" />
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success" id="btnProcesar">
                <i class="bi bi-check2-square" aria-hidden="true"></i>
                Procesar recepción
            </button>
            <a href="{{ route('admin.ordenes-compra.show', $orden) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function calcularRechazado(index) {
    const recibida = parseInt(document.querySelector(`.recibida-input[data-index="${index}"]`).value) || 0;
    const aceptada = parseInt(document.querySelector(`.aceptada-input[data-index="${index}"]`).value) || 0;

    if (aceptada > recibida) {
        alert('La cantidad aceptada no puede ser mayor que la recibida.');
        document.querySelector(`.aceptada-input[data-index="${index}"]`).value = recibida;
        return;
    }

    const rechazada = Math.max(0, recibida - aceptada);
    document.querySelector(`.rechazada-input[data-index="${index}"]`).value = rechazada;

    const motivoInput = document.querySelector(`.motivo-input[data-index="${index}"]`);
    if (rechazada > 0) {
        motivoInput.setAttribute('required', 'required');
    } else {
        motivoInput.removeAttribute('required');
    }
}

document.getElementById('recepcionForm').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('.recibida-input');
    let valid = true;
    inputs.forEach(input => {
        const idx = input.dataset.index;
        const recibida = parseInt(input.value) || 0;
        const aceptada = parseInt(document.querySelector(`.aceptada-input[data-index="${idx}"]`).value) || 0;
        if (aceptada > recibida) {
            valid = false;
            alert('La cantidad aceptada no puede ser mayor que la recibida (fila ' + (parseInt(idx) + 1) + ').');
        }
        const max = parseInt(input.getAttribute('max'));
        if (recibida > max) {
            valid = false;
            alert('La cantidad recibida no puede superar la solicitada (fila ' + (parseInt(idx) + 1) + ').');
        }
    });
    if (!valid) e.preventDefault();
});

// Inicializar cálculos
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.recibida-input').forEach(input => {
        calcularRechazado(parseInt(input.dataset.index));
    });
});
</script>
@endpush
