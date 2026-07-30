<div class="row g-4">

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-badge" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Propietario</h3>
            </div>
            <div class="mb-0">
                <label for="field-cliente_id" class="form-label fw-medium">
                    Cliente <span class="required" aria-hidden="true">*</span>
                </label>
                <select name="cliente_id" id="field-cliente_id" required
                        class="form-select{{ $errors->has('cliente_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un cliente —</option>
                    @foreach (($clientes ?? collect()) as $c)
                        <option value="{{ $c->id }}" @selected(old('cliente_id', $vehiculo->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
                    @endforeach
                </select>
                @if ($errors->has('cliente_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('cliente_id') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-car-front" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>

            <div class="mb-3">
                <label for="field-placa" class="form-label fw-medium">
                    Placa <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-upc" style="color:#64748b;"></i>
                    </span>
                    <input id="field-placa"
                           type="text"
                           name="placa"
                           value="{{ old('placa', $vehiculo->placa ?? '') }}"
                           required
                           class="form-control{{ $errors->has('placa') ? ' is-invalid' : '' }}"
                           placeholder="1234ABC"
                           style="border-left:0;"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();">
                </div>
                @if ($errors->has('placa'))
                    <div class="invalid-feedback d-block">{{ $errors->first('placa') }}</div>
                @else
                    <div class="form-text">3-4 números seguidos de 3 letras (ej: 1234ABC).</div>
                @endif
                <div class="small text-success d-none" id="vehiculo-verificacion-ok"></div>
            </div>

            <div class="mb-0">
                <label for="field-numero_chasis" class="form-label fw-medium">
                    Número de chasis
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-upc-scan" style="color:#64748b;"></i>
                    </span>
                    <input id="field-numero_chasis"
                           type="text"
                           name="numero_chasis"
                           value="{{ old('numero_chasis', $vehiculo->numero_chasis ?? '') }}"
                           class="form-control{{ $errors->has('numero_chasis') ? ' is-invalid' : '' }}"
                           placeholder="Ej: 8AP11112222333344"
                           style="border-left:0;">
                </div>
                @if ($errors->has('numero_chasis'))
                    <div class="invalid-feedback d-block">{{ $errors->first('numero_chasis') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-info-circle" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Datos del vehículo</h3>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="field-marca" class="form-label fw-medium">
                        Marca <span class="required" aria-hidden="true">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">
                            <i class="bi bi-building" style="color:#64748b;"></i>
                        </span>
                        <input id="field-marca"
                               type="text"
                               name="marca"
                               value="{{ old('marca', $vehiculo->marca ?? '') }}"
                               required
                               class="form-control{{ $errors->has('marca') ? ' is-invalid' : '' }}"
                               placeholder="Ej: Toyota"
                               style="border-left:0;"
                               oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');">
                    </div>
                    @if ($errors->has('marca'))
                        <div class="invalid-feedback d-block">{{ $errors->first('marca') }}</div>
                    @else
                        <div class="form-text">Solo letras, sin números.</div>
                    @endif
                </div>
                <div class="col-md-4">
                    <label for="field-modelo" class="form-label fw-medium">
                        Modelo <span class="required" aria-hidden="true">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">
                            <i class="bi bi-car-front" style="color:#64748b;"></i>
                        </span>
                        <input id="field-modelo"
                               type="text"
                               name="modelo"
                               value="{{ old('modelo', $vehiculo->modelo ?? '') }}"
                               required
                               class="form-control{{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                               placeholder="Ej: Corolla"
                               style="border-left:0;">
                    </div>
                    @if ($errors->has('modelo'))
                        <div class="invalid-feedback d-block">{{ $errors->first('modelo') }}</div>
                    @endif
                </div>
                <div class="col-md-2">
                    <label for="field-anio" class="form-label fw-medium">Año</label>
                    <input id="field-anio"
                           type="number"
                           name="anio"
                           value="{{ old('anio', $vehiculo->anio ?? '') }}"
                           class="form-control{{ $errors->has('anio') ? ' is-invalid' : '' }}"
                           placeholder="2024">
                    @if ($errors->has('anio'))
                        <div class="invalid-feedback d-block">{{ $errors->first('anio') }}</div>
                    @endif
                </div>
                <div class="col-md-2">
                    <label for="field-color" class="form-label fw-medium">Color</label>
                    <input id="field-color"
                           type="text"
                           name="color"
                           value="{{ old('color', $vehiculo->color ?? '') }}"
                           class="form-control{{ $errors->has('color') ? ' is-invalid' : '' }}"
                           placeholder="Blanco"
                           oninput="if(this.value.startsWith('#')) this.value = '';">
                    @if ($errors->has('color'))
                        <div class="invalid-feedback d-block">{{ $errors->first('color') }}</div>
                    @else
                        <div class="form-text">Nombre del color.</div>
                    @endif
                </div>
                <div class="col-md-4">
                    <label for="field-kilometraje_actual" class="form-label fw-medium">
                        Kilometraje actual
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">
                            <i class="bi bi-speedometer2" style="color:#64748b;"></i>
                        </span>
                        <input id="field-kilometraje_actual"
                               type="number"
                               name="kilometraje_actual"
                               value="{{ old('kilometraje_actual', $vehiculo->kilometraje_actual ?? 0) }}"
                               class="form-control{{ $errors->has('kilometraje_actual') ? ' is-invalid' : '' }}"
                               placeholder="0"
                               style="border-left:0;">
                    </div>
                    @if ($errors->has('kilometraje_actual'))
                        <div class="invalid-feedback d-block">{{ $errors->first('kilometraje_actual') }}</div>
                    @endif
                </div>
                <div class="col-12">
                    <label for="field-observaciones" class="form-label fw-medium">Observaciones</label>
                    <textarea id="field-observaciones"
                              name="observaciones"
                              rows="2"
                              class="form-control{{ $errors->has('observaciones') ? ' is-invalid' : '' }}"
                              placeholder="Notas adicionales...">{{ old('observaciones', $vehiculo->observaciones ?? '') }}</textarea>
                    @if ($errors->has('observaciones'))
                        <div class="invalid-feedback d-block">{{ $errors->first('observaciones') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-camera" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Foto</h3>
            </div>
            <div class="row g-2 align-items-end">
                <div class="col-12">
                    <label class="form-label fw-medium" for="vehiculo_foto">Foto del vehículo</label>
                    <div class="d-flex gap-2">
                        <input type="file" name="vehiculo_foto" id="vehiculo_foto" class="form-control" accept="image/*" capture="environment">
                        <button type="button" class="btn btn-outline-secondary" id="btn-camara-vehiculo" title="Tomar foto con la cámara">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    <div class="d-none mt-2" id="camara-vehiculo-container">
                        <video id="camara-vehiculo-video" autoplay playsinline style="width:100%;max-width:300px;border-radius:8px;"></video>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="btn-capturar-vehiculo">Capturar</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-cerrar-camara-vehiculo">Cancelar</button>
                        </div>
                    </div>
                    <input type="hidden" name="vehiculo_foto_base64" id="vehiculo_foto_base64" value="{{ old('vehiculo_foto_base64', $vehiculo->foto ?? '') }}">
                    @error('vehiculo_foto_base64') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="invalid-feedback d-none" id="vehiculo-foto-error"></div>
                    <div class="small text-success d-none" id="vehiculo-foto-ok"></div>
                </div>
                <div class="col-12">
                    <div id="vehiculo-foto-preview" class="text-center text-muted small p-3 rounded" style="background:#f8fafc;border:1px dashed #e2e8f0;max-width:200px;">
                        @if ($vehiculo->foto ?? null)
                            <img src="{{ $vehiculo->foto }}" class="img-fluid rounded" style="max-height:100px;">
                        @else
                            <i class="bi bi-image fs-1 d-block" style="color:#94a3b8;"></i>
                            <span class="small">Sin foto</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module d-flex flex-column">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-toggle-on" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Estado</h3>
            </div>
            <div class="mt-auto">
                <div class="form-check form-switch">
                    <input type="hidden" name="estado" value="0">
                    <input class="form-check-input" type="checkbox" id="vehiculoEstado" name="estado" value="1" @checked(old('estado', $vehiculo->estado ?? true))>
                    <label class="form-check-label fw-medium" for="vehiculoEstado">Vehículo activo</label>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.getElementById('field-placa')?.addEventListener('blur', async function () {
    const placa = this.value.trim();
    if (placa.length < 3) return;

    const verificarBtn = document.getElementById('btn-verificar-placa');
    if (verificarBtn) verificarBtn.disabled = true;

    try {
        const res = await fetch('{{ route("admin.vehiculos.verificar-placa") }}?placa=' + encodeURIComponent(placa));
        const data = await res.json();

        if (data.existente) {
            const msg = 'Este vehículo ya está registrado. Cliente: ' + (data.vehiculo.cliente_nombre || 'N/A');
            if (!confirm(msg + '\n\n¿Redirigir a la edición del vehículo?')) return;
            window.location.href = '{{ url("admin/vehiculos") }}/' + data.vehiculo.id + '/edit';
            return;
        }

        if (data.vehiculo.marca) {
            const marcaInput = document.getElementById('field-marca');
            const modeloInput = document.getElementById('field-modelo');
            const anioInput = document.getElementById('field-anio');
            const colorInput = document.getElementById('field-color');
            const chasisInput = document.getElementById('field-numero_chasis');

            if (marcaInput && !marcaInput.value) marcaInput.value = data.vehiculo.marca;
            if (modeloInput && !modeloInput.value) modeloInput.value = data.vehiculo.modelo;
            if (anioInput && !anioInput.value && data.vehiculo.anio) anioInput.value = data.vehiculo.anio;
            if (colorInput && !colorInput.value && data.vehiculo.color) colorInput.value = data.vehiculo.color;
            if (chasisInput && !chasisInput.value && data.vehiculo.numero_chasis) chasisInput.value = data.vehiculo.numero_chasis;

            const okEl = document.getElementById('vehiculo-verificacion-ok');
            if (okEl) {
                okEl.textContent = '✓ Datos obtenidos de verificación';
                okEl.classList.remove('d-none');
            }
        }
    } catch (e) {
        console.error('Error al verificar placa:', e);
    } finally {
        if (verificarBtn) verificarBtn.disabled = false;
    }
});

document.getElementById('btn-camara-vehiculo')?.addEventListener('click', async function () {
    const container = document.getElementById('camara-vehiculo-container');
    const video = document.getElementById('camara-vehiculo-video');
    if (!container) return;
    if (container.classList.contains('d-none')) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            window._vehiculoStream = stream;
            video.srcObject = stream;
            container.classList.remove('d-none');
        } catch (e) { document.getElementById('vehiculo_foto')?.click(); }
    } else { document.getElementById('vehiculo_foto')?.click(); }
});

document.getElementById('btn-capturar-vehiculo')?.addEventListener('click', function () {
    const video = document.getElementById('camara-vehiculo-video');
    if (!video || !video.videoWidth) return;
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const MAX = 400; let w = canvas.width, h = canvas.height;
    if (w > MAX || h > MAX) { const r = Math.min(MAX / w, MAX / h); w = Math.round(w * r); h = Math.round(h * r); }
    const c2 = document.createElement('canvas'); c2.width = w; c2.height = h;
    c2.getContext('2d').drawImage(canvas, 0, 0, w, h);
    const dataUri = c2.toDataURL('image/jpeg', 0.7);
    document.getElementById('vehiculo_foto_base64').value = dataUri;
    document.getElementById('vehiculo-foto-preview').innerHTML = '<img src="' + dataUri + '" class="img-fluid rounded" style="max-height:100px;">';
    document.getElementById('vehiculo-foto-ok').textContent = 'Foto capturada';
    document.getElementById('vehiculo-foto-ok').classList.remove('d-none');
    if (window._vehiculoStream) { window._vehiculoStream.getTracks().forEach(t => t.stop()); window._vehiculoStream = null; }
    document.getElementById('camara-vehiculo-container').classList.add('d-none');
});

document.getElementById('btn-cerrar-camara-vehiculo')?.addEventListener('click', function () {
    if (window._vehiculoStream) { window._vehiculoStream.getTracks().forEach(t => t.stop()); window._vehiculoStream = null; }
    document.getElementById('camara-vehiculo-container').classList.add('d-none');
});

document.getElementById('vehiculo_foto')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const errorEl = document.getElementById('vehiculo-foto-error');
    const okEl = document.getElementById('vehiculo-foto-ok');
    const hidden = document.getElementById('vehiculo_foto_base64');
    const preview = document.getElementById('vehiculo-foto-preview');
    if (!file) return;
    okEl.classList.add('d-none'); errorEl.classList.add('d-none');
    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) { errorEl.textContent = 'Solo JPG, PNG o WebP.'; errorEl.classList.remove('d-none'); this.value = ''; return; }
    if (file.size > 10 * 1024 * 1024) { errorEl.textContent = 'Máximo 10 MB.'; errorEl.classList.remove('d-none'); this.value = ''; return; }
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = new Image();
        img.onload = function () {
            const MAX = 400; let w = img.width, h = img.height;
            if (w > MAX || h > MAX) { const r = Math.min(MAX / w, MAX / h); w = Math.round(w * r); h = Math.round(h * r); }
            const canvas = document.createElement('canvas'); canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            hidden.value = canvas.toDataURL('image/jpeg', 0.7);
            preview.innerHTML = '<img src="' + hidden.value + '" class="img-fluid rounded" style="max-height:100px;">';
            okEl.textContent = 'Foto lista'; okEl.classList.remove('d-none');
        }; img.src = ev.target.result;
    }; reader.readAsDataURL(file);
});
</script>
@endpush