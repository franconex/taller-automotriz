@extends('layouts.admin')

@section('title', 'Registrar cotización')
@section('navbar-title', 'Registrar cotización')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.index') }}">Solicitudes</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.show', $solicitud) }}">{{ $solicitud->numero }}</a></li>
    <li class="active" aria-current="page">Registrar cotización</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Registrar cotización"
        :description="'Solicitud: ' . $solicitud->numero" />

    <form method="POST" action="{{ route('admin.cotizaciones.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="solicitud_compra_id" value="{{ $solicitud->id }}">

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <x-admin.form-field name="proveedor_id" label="Proveedor" type="select" required>
                    <option value="">Seleccionar proveedor...</option>
                    @foreach ($proveedores as $p)
                        <option value="{{ $p->id }}" @selected($proveedorSeleccionado?->id === $p->id || old('proveedor_id') == $p->id)>
                            {{ $p->nombre_empresa }} — {{ $p->telefono }}
                        </option>
                    @endforeach
                </x-admin.form-field>
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="medio_contacto" label="Medio de contacto" type="select" required>
                    <option value="">Seleccionar...</option>
                    <option value="whatsapp" @selected(old('medio_contacto') === 'whatsapp')>WhatsApp manual</option>
                    <option value="llamada" @selected(old('medio_contacto') === 'llamada')>Llamada telefónica</option>
                    <option value="correo" @selected(old('medio_contacto') === 'correo')>Correo electrónico</option>
                    <option value="presencial" @selected(old('medio_contacto') === 'presencial')>Presencial</option>
                    <option value="doc_fisico" @selected(old('medio_contacto') === 'doc_fisico')>Documento físico</option>
                    <option value="otro" @selected(old('medio_contacto') === 'otro')>Otro</option>
                </x-admin.form-field>
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="nombre_contacto" label="Nombre de quien respondió" :value="old('nombre_contacto')" />
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="fecha_vencimiento" type="date" label="Vencimiento de la oferta" :value="old('fecha_vencimiento')" />
            </div>
            <div class="col-12">
                <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="old('observaciones')" />
            </div>
            <div class="col-12">
                <x-admin.form-field name="archivo" type="file" label="Archivo adjunto (PDF, imagen)" help="Máx. 5 MB" />
            </div>
        </div>

        <div class="admin-table-wrap mb-4">
            <div class="px-4 py-3 border-bottom">
                <h2 class="h6 fw-bold mb-0">Productos cotizados</h2>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Solicitado</th>
                            <th style="min-width:100px;" class="text-end">Precio unitario (Bs)</th>
                            <th style="min-width:80px;" class="text-end d-none d-md-table-cell">Dto. (Bs)</th>
                            <th style="min-width:80px;" class="text-end d-none d-md-table-cell">Imp. (Bs)</th>
                            <th style="min-width:80px;" class="text-end d-none d-lg-table-cell">Envío (Bs)</th>
                            <th class="text-end d-none d-lg-table-cell">Subtotal</th>
                            <th style="min-width:60px;" class="text-end d-none d-lg-table-cell">Entrega (días)</th>
                            <th style="min-width:60px;" class="text-end d-none d-lg-table-cell">Garantía (días)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($solicitud->detalles as $i => $det)
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                                    <div class="cell-muted small">{{ $det->repuesto->codigo ?? '' }}</div>
                                    <input type="hidden" name="productos[{{ $i }}][repuesto_id]" value="{{ $det->repuesto_id }}">
                                    <input type="hidden" name="productos[{{ $i }}][cantidad_solicitada]" value="{{ $det->cantidad_solicitada }}">
                                </td>
                                <td class="text-end">{{ $det->cantidad_solicitada }}</td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][precio_unitario]"
                                           class="form-control form-control-sm text-end precio-input"
                                           value="{{ old('productos.' . $i . '.precio_unitario', $det->repuesto->costo_compra ?? 0) }}"
                                           required
                                           data-index="{{ $i }}"
                                           oninput="calcularSubtotal({{ $i }})">
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][descuento]"
                                           class="form-control form-control-sm text-end descuento-input"
                                           value="{{ old('productos.' . $i . '.descuento', 0) }}"
                                           data-index="{{ $i }}"
                                           oninput="calcularSubtotal({{ $i }})">
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][impuesto]"
                                           class="form-control form-control-sm text-end impuesto-input"
                                           value="{{ old('productos.' . $i . '.impuesto', 0) }}"
                                           data-index="{{ $i }}"
                                           oninput="calcularSubtotal({{ $i }})">
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][costo_envio]"
                                           class="form-control form-control-sm text-end envio-input"
                                           value="{{ old('productos.' . $i . '.costo_envio', 0) }}"
                                           data-index="{{ $i }}"
                                           oninput="calcularSubtotal({{ $i }})">
                                </td>
                                <td class="text-end d-none d-lg-table-cell">
                                    <span class="subtotal-label" id="subtotal-{{ $i }}">Bs 0.00</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" min="0"
                                           name="productos[{{ $i }}][tiempo_entrega_dias]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.tiempo_entrega_dias') }}"
                                           style="max-width:80px;">
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" min="0"
                                           name="productos[{{ $i }}][garantia_dias]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.garantia_dias') }}"
                                           style="max-width:80px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save" aria-hidden="true"></i>
                Registrar cotización
            </button>
            <a href="{{ route('admin.solicitudes-compra.show', $solicitud) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function calcularSubtotal(index) {
    const precio = parseFloat(document.querySelector(`.precio-input[data-index="${index}"]`).value) || 0;
    const descuento = parseFloat(document.querySelector(`.descuento-input[data-index="${index}"]`).value) || 0;
    const impuesto = parseFloat(document.querySelector(`.impuesto-input[data-index="${index}"]`).value) || 0;
    const envio = parseFloat(document.querySelector(`.envio-input[data-index="${index}"]`).value) || 0;
    const cantidad = 1;

    // El subtotal se calcula por cada registro, considerando cantidad ya fija
    const label = document.getElementById(`subtotal-${index}`);
    // Note: cantidad is handled on server side, this is just a visual reference
    const subtotal = (precio * cantidad) - descuento + impuesto + envio;
    label.textContent = 'Bs ' + subtotal.toFixed(2);
}

// Inicializar subtotales
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.precio-input');
    inputs.forEach(input => {
        const idx = input.dataset.index;
        calcularSubtotal(parseInt(idx));
    });
});
</script>
@endpush
