<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field
        name="nombre_completo"
        label="Nombre completo"
        :value="$empleado->nombre_completo ?? null"
        required
        icon="bi-person" />
    <x-admin.form-field
        name="ci"
        label="Cédula de identidad"
        :value="$empleado->ci ?? null"
        required
        icon="bi-card-text" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field
        name="telefono"
        label="Teléfono"
        :value="$empleado->telefono ?? null"
        required
        icon="bi-telephone" />
    <x-admin.form-field
        name="email"
        type="email"
        label="Correo electrónico"
        :value="$empleado->email ?? null"
        icon="bi-envelope" />
    <x-admin.form-field
        name="direccion"
        label="Dirección"
        :value="$empleado->direccion ?? null"
        icon="bi-geo-alt" />
    <div class="admin-form-section" style="border:1px solid var(--border,#e2e8f0);border-radius:8px;padding:1rem;margin-top:0.5rem;">
        <h3 class="admin-form-section__title" style="font-size:0.95rem;">Ubicación en el mapa</h3>
        <input type="hidden" id="empleado-field-latitud" value="">
        <input type="hidden" id="empleado-field-longitud" value="">
        <div class="geocoder-box">
            <input type="text" id="empleado-geocoder-input" placeholder="Ej: Plaza 24 de Septiembre, Santa Cruz" />
            <button type="button" id="empleado-geocoder-btn">Buscar</button>
            <button type="button" id="empleado-geocoder-ubicacion">Mi ubicación</button>
        </div>
        <div class="geocoder-result" id="empleado-geocoder-result"></div>
        <div id="empleado-map" style="height: 260px; border-radius: 8px;"></div>
        <div class="form-text">Haz clic en el mapa para marcar la ubicación o arrastra el marcador. La dirección se cargará automáticamente.</div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Asignación</h3>
    <x-admin.form-field
        name="sucursal_id"
        label="Sucursal"
        type="select"
        required>
        <option value="">— Selecciona una sucursal —</option>
        @foreach (($sucursales ?? collect()) as $s)
            <option value="{{ $s->id }}" @selected(old('sucursal_id', $empleado->sucursal_id ?? null) == $s->id)>
                {{ $s->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field
        name="rol_id"
        label="Rol"
        type="select"
        required>
        <option value="">— Selecciona un rol —</option>
        @foreach (($roles ?? collect()) as $r)
            <option value="{{ $r->id }}"
                data-rol-nombre="{{ $r->nombre }}"
                @selected(old('rol_id', $empleado->rol_id ?? null) == $r->id)>
                {{ $r->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field
        name="cargo"
        label="Cargo (opcional)"
        :value="$empleado->cargo ?? null"
        help="Texto libre para especificar el puesto concreto (opcional)."
        icon="bi-briefcase" />
    <x-admin.form-field
        name="fecha_contratacion"
        type="date"
        label="Fecha de contratación"
        :value="optional($empleado)->fecha_contratacion?->format('Y-m-d')" />
</div>

<div class="admin-form-section" id="mecanico-fields" style="display: none;">
    <h3 class="admin-form-section__title">
        <i class="bi bi-wrench-adjustable-circle me-1" aria-hidden="true"></i>
        Datos de mecánico
    </h3>
    <p class="text-muted small mb-3">Como el rol seleccionado es <strong>Mecánico</strong>, también debes registrar su especialidad y disponibilidad.</p>

    @php
        $rolActual = null;
        $rolId = old('rol_id', $empleado->rol_id ?? null);
        if ($rolId) {
            $rolActual = ($roles ?? collect())->firstWhere('id', (int) $rolId);
        }
        $esMecanicoActual = $rolActual && strcasecmp($rolActual->nombre, 'Mecánico') === 0;
    @endphp

    <x-admin.form-field
        name="especialidad"
        label="Especialidad"
        type="select">
        <option value="">— Selecciona una especialidad —</option>
        @foreach (($especialidades ?? collect()) as $e)
            <option value="{{ $e->id }}" @selected(old('especialidad', optional(optional($empleado)->mecanico)->especialidad_id) == $e->id)>
                {{ $e->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field
        name="disponibilidad"
        label="Disponibilidad"
        type="select">
        <option value="disponible" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad ?? 'disponible') === 'disponible')>Disponible</option>
        <option value="ocupado" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ocupado')>Ocupado</option>
        <option value="ausente" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ausente')>Ausente</option>
    </x-admin.form-field>
    <x-admin.form-field
        name="observaciones_mecanico"
        label="Observaciones"
        type="textarea"
        :value="optional(optional($empleado)->mecanico)->observaciones"
        help="Anotaciones adicionales sobre la disponibilidad o formación del mecánico." />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="empleadoEstado"
            name="estado"
            value="1"
            @checked(old('estado', $empleado->estado ?? true))>
        <label class="form-check-label" for="empleadoEstado">Empleado activo</label>
    </div>
</div>

@once
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
            (function () {
                const selectRol = document.getElementById('field-rol_id');
                const sectionMecanico = document.getElementById('mecanico-fields');
                if (!selectRol || !sectionMecanico) return;

                const esMecanico = () => {
                    const opt = selectRol.options[selectRol.selectedIndex];
                    if (!opt) return false;
                    const nombre = (opt.getAttribute('data-rol-nombre') || opt.text || '').trim().toLowerCase();
                    return nombre === 'mecánico' || nombre === 'mecanico';
                };

                const actualizar = () => {
                    sectionMecanico.style.display = esMecanico() ? '' : 'none';
                };

                selectRol.addEventListener('change', actualizar);
                actualizar();
            })();

            document.addEventListener('DOMContentLoaded', function () {
                const direccionInput = document.getElementById('field-direccion');
                const latInput = document.getElementById('empleado-field-latitud');
                const lngInput = document.getElementById('empleado-field-longitud');
                const searchInput = document.getElementById('empleado-geocoder-input');
                const searchBtn = document.getElementById('empleado-geocoder-btn');
                const searchResult = document.getElementById('empleado-geocoder-result');
                const ubicacionBtn = document.getElementById('empleado-geocoder-ubicacion');
                const mapContainer = document.getElementById('empleado-map');

                if (!mapContainer) return;

                let lastReverseCall = 0;

                function reverseGeocode(lat, lng) {
                    const now = Date.now();
                    if (now - lastReverseCall < 1200) return;
                    lastReverseCall = now;

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=es')
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data && data.display_name) {
                                if (direccionInput) direccionInput.value = data.display_name;
                                if (searchResult) searchResult.textContent = data.display_name;
                            }
                        })
                        .catch(function () {});
                }

                const defaultLat = -17.7838;
                const defaultLng = -63.1823;

                const map = L.map('empleado-map').setView([defaultLat, defaultLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

                function alCambiarUbicacion(lat, lng) {
                    if (latInput) latInput.value = lat.toFixed(7);
                    if (lngInput) lngInput.value = lng.toFixed(7);
                    reverseGeocode(lat, lng);
                }

                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    alCambiarUbicacion(pos.lat, pos.lng);
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    alCambiarUbicacion(e.latlng.lat, e.latlng.lng);
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
                                    if (direccionInput) direccionInput.value = r.display_name;
                                    if (searchResult) searchResult.textContent = r.display_name;
                                    if (latInput) latInput.value = latR.toFixed(7);
                                    if (lngInput) lngInput.value = lngR.toFixed(7);
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
                                alCambiarUbicacion(latR, lngR);
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
@endonce
