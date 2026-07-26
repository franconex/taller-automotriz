<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field name="nombre_empresa" label="Nombre de la empresa" :value="$proveedor->nombre_empresa ?? null" required icon="bi-building" />
    <x-admin.form-field name="nit" label="NIT" :value="$proveedor->nit ?? null" icon="bi-upc" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field name="contacto" label="Persona de contacto" :value="$proveedor->contacto ?? null" required icon="bi-person" />
    <x-admin.form-field name="telefono" label="Teléfono" :value="$proveedor->telefono ?? null" required icon="bi-telephone" />
    <x-admin.form-field name="email" type="email" label="Correo electrónico" :value="$proveedor->email ?? null" required icon="bi-envelope" />
    <div class="mb-3 position-relative">
        <x-admin.form-field name="direccion" label="Dirección" :value="$proveedor->direccion ?? null" required icon="bi-geo-alt" />
        <button type="button"
                class="btn btn-outline-primary btn-sm"
                id="btnBuscarMapa"
                title="Buscar en Google Maps"
                onclick="abrirGoogleMaps()"
                style="position:absolute;bottom:0;right:0;transform:translateY(-2px);">
            <i class="bi bi-map" aria-hidden="true"></i>
            Buscar en Google Maps
        </button>
    </div>
    <div id="mapPreview" class="mt-2" style="display:none;">
        <div class="ratio ratio-16x9" style="max-width:500px;border-radius:var(--tp-radius-lg);overflow:hidden;">
            <iframe id="mapIframe" allowfullscreen loading="lazy" style="border:0;width:100%;height:100%;"></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirGoogleMaps() {
    const direccion = document.querySelector('input[name="direccion"]').value;
    if (direccion.trim() === '') {
        alert('Primero escribí la dirección.');
        return;
    }
    window.open('https://www.google.com/maps/search/' + encodeURIComponent(direccion), '_blank');
}

document.querySelector('input[name="direccion"]')?.addEventListener('input', function() {
    const dir = this.value.trim();
    const preview = document.getElementById('mapPreview');
    const iframe = document.getElementById('mapIframe');
    if (dir.length > 5) {
        iframe.src = 'https://www.google.com/maps?q=' + encodeURIComponent(dir) + '&output=embed';
        preview.style.display = '';
    } else {
        preview.style.display = 'none';
    }
});

const dirInicial = '{{ old("direccion", $proveedor->direccion ?? "") }}';
if (dirInicial.length > 5) {
    document.getElementById('mapIframe').src = 'https://www.google.com/maps?q=' + encodeURIComponent(dirInicial) + '&output=embed';
    document.getElementById('mapPreview').style.display = '';
}
</script>
@endpush

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="proveedorEstado"
            name="estado"
            value="1"
            @checked(old('estado', $proveedor->estado ?? true))>
        <label class="form-check-label" for="proveedorEstado">Proveedor activo</label>
    </div>
</div>
