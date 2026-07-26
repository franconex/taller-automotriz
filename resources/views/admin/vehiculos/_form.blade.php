<div class="admin-form-section">
    <h3 class="admin-form-section__title">Propietario</h3>
    <x-admin.form-field name="cliente_id" label="Cliente" type="select" required>
        <option value="">— Selecciona un cliente —</option>
        @foreach (($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" @selected(old('cliente_id', $vehiculo->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Datos del vehículo</h3>
    <div class="row g-2">
        <div class="col-md-6">
            <x-admin.form-field name="marca" label="Marca" :value="$vehiculo->marca ?? null" required icon="bi-building" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field name="modelo" label="Modelo" :value="$vehiculo->modelo ?? null" required icon="bi-car-front" />
        </div>
        <div class="col-md-4">
            <x-admin.form-field name="placa" label="Placa" :value="$vehiculo->placa ?? null" required icon="bi-upc" />
        </div>
        <div class="col-md-4">
            <x-admin.form-field name="anio" type="number" label="Año" :value="$vehiculo->anio ?? null" />
        </div>
        <div class="col-md-4">
            <x-admin.form-field name="color" label="Color" :value="$vehiculo->color ?? null" icon="bi-droplet" />
        </div>
        <div class="col-12">
            <x-admin.form-field name="numero_chasis" label="Número de chasis" :value="$vehiculo->numero_chasis ?? null" icon="bi-upc-scan" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field name="kilometraje_actual" type="number" label="Kilometraje actual" :value="$vehiculo->kilometraje_actual ?? null" icon="bi-speedometer2" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field name="observaciones" label="Observaciones" :value="$vehiculo->observaciones ?? null" />
        </div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Foto</h3>
    <div class="row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label" for="vehiculo_foto">Foto del vehículo</label>
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
        <div class="col-md-4">
            <div id="vehiculo-foto-preview" class="text-center text-muted small" style="max-width:150px;">
                @if ($vehiculo->foto ?? null)
                    <img src="{{ $vehiculo->foto }}" class="img-fluid rounded" style="max-height:100px;">
                @else
                    <i class="bi bi-image fs-1 d-block"></i>
                    <span class="small">Sin foto</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input class="form-check-input" type="checkbox" id="vehiculoEstado" name="estado" value="1" @checked(old('estado', $vehiculo->estado ?? true))>
        <label class="form-check-label" for="vehiculoEstado">Vehículo activo</label>
    </div>
</div>

@push('scripts')
<script>
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