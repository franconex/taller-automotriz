<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field
        name="nombre"
        label="Nombre de la sucursal"
        :value="$sucursal->nombre ?? null"
        required
        icon="bi-building" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Ubicación y contacto</h3>
    <x-admin.form-field
        name="direccion"
        label="Dirección"
        :value="$sucursal->direccion ?? null"
        required
        icon="bi-geo-alt" />
    <x-admin.form-field
        name="telefono"
        label="Teléfono"
        :value="$sucursal->telefono ?? null"
        required
        icon="bi-telephone"
        autocomplete="tel" />
    <x-admin.form-field
        name="horario_atencion"
        label="Horario de atención"
        :value="$sucursal->horario_atencion ?? null"
        icon="bi-clock"
        help="Ej. Lun a Vie 8:00–18:00, Sáb 9:00–13:00" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Coordenadas (para el mapa de rutas)</h3>
    <div class="row g-3">
        <div class="col-md-6">
            <x-admin.form-field
                name="latitud"
                label="Latitud"
                type="text"
                :value="$sucursal->latitud ?? null"
                icon="bi-geo"
                placeholder="-17.7838" />
        </div>
        <div class="col-md-6">
            <x-admin.form-field
                name="longitud"
                label="Longitud"
                type="text"
                :value="$sucursal->longitud ?? null"
                icon="bi-geo"
                placeholder="-63.1823" />
        </div>
    </div>
    <div class="geocoder-box">
        <input type="text" id="geocoder-input" placeholder="Ej: Plaza 24 de Septiembre, Santa Cruz" />
        <button type="button" id="geocoder-btn">Buscar</button>
        <button type="button" id="geocoder-ubicacion">Mi ubicación</button>
    </div>
    <div class="geocoder-result" id="geocoder-result"></div>
    <div id="sucursal-map" style="height: 300px; border-radius: 8px;"></div>
    <div class="form-text">Haz clic en el mapa para marcar la ubicación o arrastra el marcador.</div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="sucursalEstado"
            name="estado"
            value="1"
            @checked(old('estado', $sucursal->estado ?? true))>
        <label class="form-check-label" for="sucursalEstado">Sucursal activa</label>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .geocoder-box { display: flex; gap: 0.375rem; margin-bottom: 0.5rem; }
        .geocoder-box input { flex: 1; padding: 0.375rem 0.75rem; font-size: 0.875rem; border: 1px solid var(--border, #ced4da); border-radius: 6px; outline: none; }
        .geocoder-box input:focus { border-color: var(--primary, #4361ee); box-shadow: 0 0 0 2px rgba(67,97,238,0.15); }
        .geocoder-box button { padding: 0.375rem 0.75rem; font-size: 0.8rem; border: 1px solid var(--border, #ced4da); border-radius: 6px; background: var(--surface, #fff); cursor: pointer; white-space: nowrap; }
        .geocoder-box button:hover { background: var(--bg-subtle, #f1f5f9); }
        .geocoder-result { font-size: 0.8rem; color: var(--muted, #64748b); margin-bottom: 0.375rem; min-height: 1.2em; }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('field-latitud');
            const lngInput = document.getElementById('field-longitud');
            const searchInput = document.getElementById('geocoder-input');
            const searchBtn = document.getElementById('geocoder-btn');
            const searchResult = document.getElementById('geocoder-result');
            const ubicacionBtn = document.getElementById('geocoder-ubicacion');

            const defaultLat = -17.7838;
            const defaultLng = -63.1823;
            const lat = parseFloat(latInput?.value) || defaultLat;
            const lng = parseFloat(lngInput?.value) || defaultLng;

            const map = L.map('sucursal-map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            function actualizarCoordenadas(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
            });

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
            });

            latInput?.addEventListener('change', function () {
                const val = parseFloat(this.value);
                if (!isNaN(val)) {
                    marker.setLatLng([val, parseFloat(lngInput.value) || lng]);
                    map.setView(marker.getLatLng());
                }
            });

            lngInput?.addEventListener('change', function () {
                const val = parseFloat(this.value);
                if (!isNaN(val)) {
                    marker.setLatLng([parseFloat(latInput.value) || lat, val]);
                    map.setView(marker.getLatLng());
                }
            });

            if (searchBtn && searchInput) {
                searchBtn.addEventListener('click', function () {
                    const q = searchInput.value.trim();
                    if (!q) return;
                    searchBtn.disabled = true;
                    searchBtn.textContent = 'Buscando...';
                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1&accept-language=es')
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data && data.length > 0) {
                                const r = data[0];
                                const latR = parseFloat(r.lat);
                                const lngR = parseFloat(r.lon);
                                marker.setLatLng([latR, lngR]);
                                map.setView([latR, lngR], 16);
                                actualizarCoordenadas(latR, lngR);
                                if (searchResult) searchResult.textContent = r.display_name;
                            } else {
                                if (searchResult) searchResult.textContent = 'No se encontró la dirección.';
                            }
                        })
                        .catch(function () {
                            if (searchResult) searchResult.textContent = 'Error al buscar.';
                        })
                        .finally(function () {
                            searchBtn.disabled = false;
                            searchBtn.textContent = 'Buscar';
                        });
                });
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') searchBtn.click();
                });
            }

            if (ubicacionBtn) {
                ubicacionBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        if (searchResult) searchResult.textContent = 'Geolocalización no disponible.';
                        return;
                    }
                    ubicacionBtn.disabled = true;
                    ubicacionBtn.textContent = 'Obteniendo...';
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            const latR = pos.coords.latitude;
                            const lngR = pos.coords.longitude;
                            marker.setLatLng([latR, lngR]);
                            map.setView([latR, lngR], 16);
                            actualizarCoordenadas(latR, lngR);
                            if (searchResult) searchResult.textContent = 'Ubicación actual (' + latR.toFixed(4) + ', ' + lngR.toFixed(4) + ')';
                            ubicacionBtn.disabled = false;
                            ubicacionBtn.textContent = 'Mi ubicación';
                        },
                        function () {
                            if (searchResult) searchResult.textContent = 'No se pudo obtener la ubicación.';
                            ubicacionBtn.disabled = false;
                            ubicacionBtn.textContent = 'Mi ubicación';
                        }
                    );
                });
            }
        });
    </script>
@endpush
