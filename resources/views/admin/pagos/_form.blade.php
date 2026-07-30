<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-journal-text" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Orden de trabajo</h3>
            </div>
            <div class="mb-0">
                <label for="field-orden_trabajo_id" class="form-label fw-medium">Orden de trabajo <span class="required">*</span></label>
                <select name="orden_trabajo_id" id="field-orden_trabajo_id" required class="form-select{{ $errors->has('orden_trabajo_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona una orden —</option>
                    @foreach (($ordenes ?? collect()) as $o)
                        <option value="{{ $o->id }}" @selected(old('orden_trabajo_id', $pago->orden_trabajo_id ?? ($ordenId ?? null)) == $o->id)>
                            {{ $o->numero_orden }} — {{ $o->cliente->nombre_completo ?? '' }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('orden_trabajo_id'))<div class="invalid-feedback d-block">{{ $errors->first('orden_trabajo_id') }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-cash-coin" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Pago</h3>
            </div>
            <div class="mb-3">
                <label for="field-metodo_pago_id" class="form-label fw-medium">Método de pago <span class="required">*</span></label>
                <select name="metodo_pago_id" id="field-metodo_pago_id" required class="form-select{{ $errors->has('metodo_pago_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un método —</option>
                    @foreach (($metodos ?? collect()) as $m)
                        <option value="{{ $m->id }}" @selected(old('metodo_pago_id', $pago->metodo_pago_id ?? null) == $m->id)>{{ $m->nombre }}</option>
                    @endforeach
                </select>
                @if ($errors->has('metodo_pago_id'))<div class="invalid-feedback d-block">{{ $errors->first('metodo_pago_id') }}</div>@endif
            </div>
            <div class="row g-3">
                <div class="col-7">
                    <label for="field-monto" class="form-label fw-medium">Monto <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">Bs</span>
                        <input id="field-monto" type="number" step="0.01" min="0" name="monto" value="{{ old('monto', $pago->monto ?? '') }}" required
                               class="form-control{{ $errors->has('monto') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="0.00">
                    </div>
                    @if ($errors->has('monto'))<div class="invalid-feedback d-block">{{ $errors->first('monto') }}</div>@endif
                </div>
                <div class="col-5">
                    <label for="field-fecha_pago" class="form-label fw-medium">Fecha y hora</label>
                    <input id="field-fecha_pago" type="datetime-local" name="fecha_pago"
                           value="{{ old('fecha_pago', isset($pago)&&$pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required
                           class="form-control{{ $errors->has('fecha_pago') ? ' is-invalid' : '' }}">
                    @if ($errors->has('fecha_pago'))<div class="invalid-feedback d-block">{{ $errors->first('fecha_pago') }}</div>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-receipt" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Comprobante</h3>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="field-numero_comprobante" class="form-label fw-medium">N° de comprobante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-upc" style="color:#64748b;"></i></span>
                        <input id="field-numero_comprobante" type="text" name="numero_comprobante" value="{{ old('numero_comprobante', $pago->numero_comprobante ?? '') }}"
                               class="form-control{{ $errors->has('numero_comprobante') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="FAC-001">
                    </div>
                    @if ($errors->has('numero_comprobante'))<div class="invalid-feedback d-block">{{ $errors->first('numero_comprobante') }}</div>@endif
                </div>
                <div class="col-md-4">
                    <label for="field-referencia" class="form-label fw-medium">Referencia</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-link-45deg" style="color:#64748b;"></i></span>
                        <input id="field-referencia" type="text" name="referencia" value="{{ old('referencia', $pago->referencia ?? '') }}"
                               class="form-control{{ $errors->has('referencia') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="REF-001">
                    </div>
                    @if ($errors->has('referencia'))<div class="invalid-feedback d-block">{{ $errors->first('referencia') }}</div>@endif
                </div>
                <div class="col-md-4">
                    <label for="field-observaciones" class="form-label fw-medium">Observaciones</label>
                    <input id="field-observaciones" type="text" name="observaciones" value="{{ old('observaciones', $pago->observaciones ?? '') }}"
                           class="form-control{{ $errors->has('observaciones') ? ' is-invalid' : '' }}" placeholder="Notas adicionales...">
                    @if ($errors->has('observaciones'))<div class="invalid-feedback d-block">{{ $errors->first('observaciones') }}</div>@endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR --}}
<div id="seccion-qr" class="admin-card-modern mt-3 p-4 d-none">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-qr-code" style="font-size:1rem;"></i></span>
        <h3 class="fw-bold mb-0" style="font-size:1rem;">Código QR</h3>
    </div>
    <p class="cell-secondary small mb-3">El código QR se genera automáticamente al seleccionar el método QR y completar el monto.</p>
    <div id="qr-preview-container" class="text-center py-3 rounded-3" style="background:#f8fafc;border:1px dashed #e2e8f0;">
        <div class="text-muted small"><i class="bi bi-qr-code" style="font-size:3rem;display:block;margin-bottom:.5rem;"></i> Selecciona QR como método de pago<br>y completa el monto para ver el código.</div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/admin/pago-qr.js'])
@endpush