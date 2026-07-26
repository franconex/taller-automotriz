<div class="admin-form-section">
    <h3 class="admin-form-section__title">Información personal</h3>
    <x-admin.form-field name="nombre_completo" label="Nombre completo" :value="$cliente->nombre_completo ?? null" required icon="bi-person" autocomplete="name" />
    <x-admin.form-field name="ci" label="Cédula de identidad" :value="$cliente->ci ?? null" icon="bi-card-text" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field name="telefono" label="Teléfono" :value="$cliente->telefono ?? null" required icon="bi-telephone" autocomplete="tel" />
    <x-admin.form-field name="email" label="Correo electrónico" type="email" :value="$cliente->email ?? null" icon="bi-envelope" autocomplete="email" />
    <x-admin.form-field name="direccion" label="Dirección" :value="$cliente->direccion ?? null" icon="bi-geo-alt" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Vehículo (opcional)</h3>
    <p class="small text-muted mb-3">Si el cliente tiene un vehículo, puedes registrarlo aquí mismo.</p>
    <div class="row g-3">
        <div class="col-md-6">
            <x-admin.form-field name="vehiculo_marca" label="Marca" icon="bi-building" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field name="vehiculo_modelo" label="Modelo" icon="bi-car-front" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field name="vehiculo_placa" label="Placa" icon="bi-upc-scan" />
        </div>
        <div class="col-md-3">
            <x-admin.form-field name="vehiculo_anio" label="Año" type="number" />
        </div>
        <div class="col-md-3">
            <x-admin.form-field name="vehiculo_color" label="Color" />
        </div>
        <div class="col-md-6">
            <label class="form-label" for="vehiculo_foto">Foto</label>
            <div class="input-group">
                <input type="file" name="vehiculo_foto" id="vehiculo_foto" class="form-control" accept="image/*" capture="environment">
                <button type="button" class="btn btn-outline-secondary" id="btn-camara-cliente-vehiculo" title="Cámara">
                    <i class="bi bi-camera"></i>
                </button>
            </div>
            <div class="d-none mt-2" id="camara-cliente-vehiculo-container">
                <video id="camara-cliente-vehiculo-video" autoplay playsinline style="width:100%;max-width:300px;border-radius:8px;"></video>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-capturar-cliente-vehiculo">Capturar</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-cerrar-camara-cliente-vehiculo">Cancelar</button>
                </div>
            </div>
            <input type="hidden" name="vehiculo_foto_base64" id="vehiculo_foto_base64" value="">
            <div class="invalid-feedback" id="vehiculo-foto-error"></div>
            <div class="small text-success d-none" id="vehiculo-foto-ok"></div>
        </div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="clienteEstado"
            name="estado"
            value="1"
            @checked(old('estado', $cliente->estado ?? true))>
        <label class="form-check-label" for="clienteEstado">Cliente activo</label>
    </div>
</div>

@push('scripts')
<script>
let mediaStreamCliente = null;

document.getElementById('btn-camara-cliente-vehiculo')?.addEventListener('click', async function () {
    const container = document.getElementById('camara-cliente-vehiculo-container');
    const video = document.getElementById('camara-cliente-vehiculo-video');
    if (container.classList.contains('d-none')) {
        try {
            mediaStreamCliente = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = mediaStreamCliente;
            container.classList.remove('d-none');
        } catch (e) {
            document.getElementById('vehiculo_foto')?.click();
        }
    } else {
        document.getElementById('vehiculo_foto')?.click();
    }
});

document.getElementById('btn-capturar-cliente-vehiculo')?.addEventListener('click', function () {
    const video = document.getElementById('camara-cliente-vehiculo-video');
    if (!video || !video.videoWidth) return;
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const MAX = 400;
    let w = canvas.width, h = canvas.height;
    if (w > MAX || h > MAX) { const r = Math.min(MAX / w, MAX / h); w = Math.round(w * r); h = Math.round(h * r); }
    const c2 = document.createElement('canvas');
    c2.width = w; c2.height = h;
    c2.getContext('2d').drawImage(canvas, 0, 0, w, h);
    const dataUri = c2.toDataURL('image/jpeg', 0.7);
    document.getElementById('vehiculo_foto_base64').value = dataUri;
    document.getElementById('vehiculo-foto-ok').textContent = 'Foto capturada';
    document.getElementById('vehiculo-foto-ok').classList.remove('d-none');
    if (mediaStreamCliente) { mediaStreamCliente.getTracks().forEach(t => t.stop()); mediaStreamCliente = null; }
    document.getElementById('camara-cliente-vehiculo-container').classList.add('d-none');
});

document.getElementById('btn-cerrar-camara-cliente-vehiculo')?.addEventListener('click', function () {
    if (mediaStreamCliente) { mediaStreamCliente.getTracks().forEach(t => t.stop()); mediaStreamCliente = null; }
    document.getElementById('camara-cliente-vehiculo-container').classList.add('d-none');
});

document.getElementById('vehiculo_foto')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const errorEl = document.getElementById('vehiculo-foto-error');
    const okEl = document.getElementById('vehiculo-foto-ok');
    const hidden = document.getElementById('vehiculo_foto_base64');
    if (!file) return;
    okEl.classList.add('d-none');
    errorEl.classList.add('d-none');
    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
        errorEl.textContent = 'Solo JPG, PNG o WebP.';
        errorEl.classList.remove('d-none');
        this.value = '';
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        errorEl.textContent = 'Máximo 10 MB.';
        errorEl.classList.remove('d-none');
        this.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = new Image();
        img.onload = function () {
            const MAX = 400;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                const ratio = Math.min(MAX / w, MAX / h);
                w = Math.round(w * ratio);
                h = Math.round(h * ratio);
            }
            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            hidden.value = canvas.toDataURL('image/jpeg', 0.7);
            okEl.textContent = 'Foto lista';
            okEl.classList.remove('d-none');
        };
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

