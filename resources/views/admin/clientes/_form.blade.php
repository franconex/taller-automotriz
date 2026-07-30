<div class="row g-4">

    <div class="col-12 col-lg-6 d-flex flex-column gap-4">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f3e8ff;color:#8b5cf6;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-vcard" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Información personal</h3>
            </div>

            <div class="mb-3">
                <label for="field-nombre_completo" class="form-label fw-medium">
                    Nombre completo <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-person" style="color:#64748b;"></i>
                    </span>
                    <input id="field-nombre_completo"
                           type="text"
                           name="nombre_completo"
                           value="{{ old('nombre_completo', $cliente->nombre_completo ?? '') }}"
                           required
                           class="form-control{{ $errors->has('nombre_completo') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Juan Pérez"
                           style="border-left:0;"
                           autocomplete="name"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '').replace(/\s{2,}/g, ' ')">
                </div>
                @if ($errors->has('nombre_completo'))
                    <div class="invalid-feedback d-block">{{ $errors->first('nombre_completo') }}</div>
                @else
                    <div class="form-text">Solo letras, sin números ni caracteres especiales. Sin espacios dobles.</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-ci" class="form-label fw-medium">
                    Cédula de identidad
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-card-text" style="color:#64748b;"></i>
                    </span>
                    <input id="field-ci"
                           type="text"
                           name="ci"
                           value="{{ old('ci', $cliente->ci ?? '') }}"
                           class="form-control{{ $errors->has('ci') ? ' is-invalid' : '' }}"
                           placeholder="Ej: 1234567"
                           style="border-left:0;"
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                @if ($errors->has('ci'))
                    <div class="invalid-feedback d-block">{{ $errors->first('ci') }}</div>
                @else
                    <div class="form-text">Solo números, sin letras ni caracteres especiales.</div>
                @endif
            </div>
        </div>

        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-car-front" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">
                    Vehículos
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="btn-agregar-vehiculo" title="Agregar otro vehículo">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </h3>
            </div>
            @php $esCreacion = ! isset($cliente) || ! $cliente->id; @endphp
            @if ($esCreacion)
            <p class="cell-secondary small mb-3">Registra al menos un vehículo para el cliente.</p>
            @else
            <p class="cell-secondary small mb-3">Agrega más vehículos usando el botón <i class="bi bi-plus-lg"></i>.</p>
            @endif
            <div id="vehiculos-container">
                <div class="vehiculo-row border rounded-3 p-3 mb-3 position-relative" data-index="0">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Marca @if($esCreacion)<span class="required">*</span>@endif</label>
                            <input type="text" name="vehiculos[0][marca]" class="form-control" placeholder="Toyota"
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');">
                            <div class="form-text">Solo letras.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Modelo @if($esCreacion)<span class="required">*</span>@endif</label>
                            <input type="text" name="vehiculos[0][modelo]" class="form-control" placeholder="Corolla">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Placa @if($esCreacion)<span class="required">*</span>@endif</label>
                            <input type="text" name="vehiculos[0][placa]" class="form-control" placeholder="1234ABC"
                                   oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();">
                            <div class="form-text">3-4 nº + 3 letras (ej: 1234ABC).</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">N° chasis</label>
                            <input type="text" name="vehiculos[0][numero_chasis]" class="form-control" placeholder="8AP11112222333344">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium small">Año</label>
                            <input type="number" name="vehiculos[0][anio]" class="form-control" placeholder="2024">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium small">Color</label>
                            <input type="text" name="vehiculos[0][color]" class="form-control" placeholder="Blanco"
                                   oninput="if(this.value.startsWith('#')) this.value = '';">
                            <div class="form-text">Nombre del color.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-telephone" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Contacto</h3>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Teléfono <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="d-flex gap-2">
                    <div style="min-width:120px;">
                        <select name="codigo_pais"
                                id="field-codigo_pais"
                                required
                                class="form-select{{ $errors->has('codigo_pais') ? ' is-invalid' : '' }}">
                            <option value="+591" {{ old('codigo_pais', $codigo_pais ?? '+591') == '+591' ? 'selected' : '' }}>+591 (Bolivia)</option>
                        </select>
                        @if ($errors->has('codigo_pais'))
                            <div class="invalid-feedback d-block">{{ $errors->first('codigo_pais') }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;">
                                <i class="bi bi-phone" style="color:#64748b;"></i>
                            </span>
                            <input id="field-telefono_numero"
                                   type="text"
                                   name="telefono_numero"
                                   value="{{ old('telefono_numero', $telefono_numero ?? '') }}"
                                   required
                                   class="form-control{{ $errors->has('telefono_numero') ? ' is-invalid' : '' }}"
                                   placeholder="70000001"
                                   style="border-left:0;"
                                   inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        @if ($errors->has('telefono_numero'))
                            <div class="invalid-feedback d-block">{{ $errors->first('telefono_numero') }}</div>
                        @else
                            <div class="form-text">Solo dígitos.</div>
                        @endif
                    </div>
                </div>
                <div class="mt-1" style="font-size:0.8rem;color:#64748b;">
                    Número completo: <strong id="cliente-telefono-completo">+591 70000001</strong>
                </div>
            </div>

            <div class="mb-3">
                <label for="field-email" class="form-label fw-medium">
                    Correo electrónico
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-envelope" style="color:#64748b;"></i>
                    </span>
                    <input id="field-email"
                           type="email"
                           name="email"
                           value="{{ old('email', $cliente->email ?? '') }}"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           placeholder="ej: correo@dominio.com"
                           style="border-left:0;"
                           autocomplete="email">
                </div>
                @if ($errors->has('email'))
                    <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                @else
                    <div class="form-text">Formato: usuario@dominio.com</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-direccion" class="form-label fw-medium">
                    Dirección
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-geo-alt" style="color:#64748b;"></i>
                    </span>
                    <input id="field-direccion"
                           type="text"
                           name="direccion"
                           value="{{ old('direccion', $cliente->direccion ?? '') }}"
                           class="form-control{{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Av. Principal #123"
                           style="border-left:0;">
                </div>
                @if ($errors->has('direccion'))
                    <div class="invalid-feedback d-block">{{ $errors->first('direccion') }}</div>
                @endif
            </div>

            <div class="mt-3 pt-3 border-top">
                <label class="form-label fw-medium mb-2">Ubicación en el mapa</label>
                <div class="geocoder-wrap" style="position: relative;">
                    <div class="geocoder-box">
                        <input type="text" id="cliente-geocoder-input" placeholder="Buscar zona o dirección en Bolivia..." autocomplete="off" />
                        <button type="button" id="cliente-geocoder-btn">Buscar</button>
                        <button type="button" id="cliente-geocoder-ubicacion" class="btn-ubicacion">
                            <i class="bi bi-crosshair2" style="font-size:0.75rem;"></i> Mi ubicación
                        </button>
                    </div>
                    <div id="cliente-geocoder-suggestions" class="geocoder-suggestions" style="display:none;"></div>
                </div>
                <div class="geocoder-result" id="cliente-geocoder-result"></div>
                <div id="cliente-map" style="height: 220px; border-radius: 8px;"></div>
                <div class="form-text mt-1">Haz clic en el mapa para marcar la ubicación o arrastra el marcador.</div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-toggle-on" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Estado</h3>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="estado" value="0">
                <input class="form-check-input" type="checkbox" id="clienteEstado" name="estado" value="1"
                    @checked(old('estado', $cliente->estado ?? true))>
                <label class="form-check-label fw-medium" for="clienteEstado">Cliente activo</label>
            </div>
        </div>
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
            .geocoder-box .btn-ubicacion { background: #f3e8ff; border-color: #ddd6fe; color: #8b5cf6; }
            .geocoder-box .btn-ubicacion:hover { background: #ede9fe; }
            .geocoder-result { font-size: 0.8rem; color: var(--muted, #64748b); margin-bottom: 0.375rem; min-height: 1.2em; }
            .geocoder-suggestions { position: absolute; z-index: 1000; background: #fff; border: 1px solid #ddd; border-radius: 6px; max-height: 200px; overflow-y: auto; width: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .geocoder-suggestions .suggestion-item { padding: 0.5rem 0.75rem; cursor: pointer; font-size: 0.85rem; border-bottom: 1px solid #f0f0f0; }
            .geocoder-suggestions .suggestion-item:hover { background: #f8fafc; }
            .geocoder-suggestions .suggestion-item .distance-badge { float: right; font-size: 0.7rem; color: #64748b; }
            .geocoder-wrap { position: relative; }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const direccionInput = document.getElementById('field-direccion');
                const searchInput = document.getElementById('cliente-geocoder-input');
                const searchBtn = document.getElementById('cliente-geocoder-btn');
                const searchResult = document.getElementById('cliente-geocoder-result');
                const ubicacionBtn = document.getElementById('cliente-geocoder-ubicacion');
                const suggestionsBox = document.getElementById('cliente-geocoder-suggestions');
                const mapContainer = document.getElementById('cliente-map');

                if (!mapContainer) return;

                const BOLIVIA_BOUNDS = { minLat: -22.9, maxLat: -9.7, minLng: -69.7, maxLng: -57.5 };
                let userLat = null, userLng = null;
                let lastReverseCall = 0;

                function isInBolivia(lat, lng) {
                    return lat >= BOLIVIA_BOUNDS.minLat && lat <= BOLIVIA_BOUNDS.maxLat &&
                           lng >= BOLIVIA_BOUNDS.minLng && lng <= BOLIVIA_BOUNDS.maxLng;
                }

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
                        }).catch(function () {});
                }

                const map = L.map('cliente-map', {
                    minZoom: 4,
                    maxBounds: L.latLngBounds(L.latLng(-24.9, -71.7), L.latLng(-7.7, -55.5)),
                    maxBoundsViscosity: 1.0
                }).setView([-17.7838, -63.1823], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                const marker = L.marker([-17.7838, -63.1823], { draggable: true }).addTo(map);

                function alCambiarUbicacion(lat, lng) { reverseGeocode(lat, lng); }

                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    alCambiarUbicacion(pos.lat, pos.lng);
                });
                map.on('click', function (e) {
                    if (!isInBolivia(e.latlng.lat, e.latlng.lng)) {
                        if (searchResult) searchResult.textContent = 'La ubicación debe estar dentro de Bolivia.';
                        return;
                    }
                    marker.setLatLng(e.latlng);
                    alCambiarUbicacion(e.latlng.lat, e.latlng.lng);
                });

                function getDistanceFromUser(lat1, lng1, lat2, lng2) {
                    if (lat2 === null || lng2 === null) return Infinity;
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLng = (lng2 - lng1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng/2) * Math.sin(dLng/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c;
                }

                function showSuggestions(results) {
                    suggestionsBox.innerHTML = '';
                    if (results.length === 0) { suggestionsBox.style.display = 'none'; return; }
                    if (userLat !== null && userLng !== null) {
                        results.sort(function (a, b) {
                            return getDistanceFromUser(userLat, userLng, parseFloat(a.lat), parseFloat(a.lon)) -
                                   getDistanceFromUser(userLat, userLng, parseFloat(b.lat), parseFloat(b.lon));
                        });
                    }
                    results.forEach(function (r) {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        let label = r.display_name;
                        if (userLat !== null && userLng !== null) {
                            const dist = getDistanceFromUser(userLat, userLng, parseFloat(r.lat), parseFloat(r.lon));
                            label += ' <span class="distance-badge">' + (dist < 1 ? (Math.round(dist * 1000) + ' m') : (dist.toFixed(1) + ' km')) + '</span>';
                        }
                        div.innerHTML = label;
                        div.addEventListener('click', function () {
                            const latR = parseFloat(r.lat), lngR = parseFloat(r.lon);
                            marker.setLatLng([latR, lngR]);
                            map.setView([latR, lngR], 16);
                            if (direccionInput) direccionInput.value = r.display_name;
                            if (searchResult) searchResult.textContent = r.display_name;
                            if (searchInput) searchInput.value = r.display_name.split(',')[0];
                            suggestionsBox.style.display = 'none';
                        });
                        suggestionsBox.appendChild(div);
                    });
                    suggestionsBox.style.display = 'block';
                }

                if (searchBtn && searchInput) {
                    searchBtn.addEventListener('click', function () {
                        const q = searchInput.value.trim();
                        if (!q) return;
                        searchBtn.disabled = true; searchBtn.textContent = 'Buscando...';
                        const vb = BOLIVIA_BOUNDS.minLng + ',' + BOLIVIA_BOUNDS.minLat + ',' + BOLIVIA_BOUNDS.maxLng + ',' + BOLIVIA_BOUNDS.maxLat;
                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&bounded=1&viewbox=' + vb + '&accept-language=es')
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (data && data.length > 0) {
                                    showSuggestions(data);
                                    if (data.length === 1) {
                                        const r = data[0];
                                        marker.setLatLng([parseFloat(r.lat), parseFloat(r.lon)]);
                                        map.setView([parseFloat(r.lat), parseFloat(r.lon)], 16);
                                        if (direccionInput) direccionInput.value = r.display_name;
                                        if (searchResult) searchResult.textContent = r.display_name;
                                    } else {
                                        if (searchResult) searchResult.textContent = 'Selecciona una zona de la lista.';
                                    }
                                } else {
                                    if (searchResult) searchResult.textContent = 'No se encontraron resultados en Bolivia.';
                                }
                            }).catch(function () { if (searchResult) searchResult.textContent = 'Error al buscar.'; })
                            .finally(function () { searchBtn.disabled = false; searchBtn.textContent = 'Buscar'; });
                    });

                    let timer = null;
                    searchInput.addEventListener('input', function () {
                        clearTimeout(timer);
                        const q = this.value.trim();
                        if (q.length < 3) { suggestionsBox.style.display = 'none'; return; }
                        timer = setTimeout(function () {
                            const vb = BOLIVIA_BOUNDS.minLng + ',' + BOLIVIA_BOUNDS.minLat + ',' + BOLIVIA_BOUNDS.maxLng + ',' + BOLIVIA_BOUNDS.maxLat;
                            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&bounded=1&viewbox=' + vb + '&accept-language=es')
                                .then(function (r) { return r.json(); }).then(function (data) {
                                    if (data && data.length > 0) showSuggestions(data);
                                    else suggestionsBox.style.display = 'none';
                                }).catch(function () {});
                        }, 400);
                    });
                    searchInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); searchBtn.click(); } });
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('#cliente-geocoder-input') && !e.target.closest('#cliente-geocoder-suggestions')) suggestionsBox.style.display = 'none';
                    });
                }

                if (ubicacionBtn) {
                    ubicacionBtn.addEventListener('click', function () {
                        if (!navigator.geolocation) { if (searchResult) searchResult.textContent = 'Geolocalización no disponible.'; return; }
                        ubicacionBtn.disabled = true; ubicacionBtn.textContent = 'Obteniendo...';
                        navigator.geolocation.getCurrentPosition(
                            function (pos) {
                                userLat = pos.coords.latitude; userLng = pos.coords.longitude;
                                if (!isInBolivia(userLat, userLng)) {
                                    if (searchResult) searchResult.textContent = 'Tu ubicación actual no se encuentra dentro de Bolivia.';
                                    ubicacionBtn.disabled = false; ubicacionBtn.textContent = 'Mi ubicación';
                                    return;
                                }
                                marker.setLatLng([userLat, userLng]); map.setView([userLat, userLng], 16);
                                alCambiarUbicacion(userLat, userLng);
                                ubicacionBtn.disabled = false; ubicacionBtn.textContent = 'Mi ubicación';
                            },
                            function () {
                                if (searchResult) searchResult.textContent = 'No se pudo obtener la ubicación.';
                                ubicacionBtn.disabled = false; ubicacionBtn.textContent = 'Mi ubicación';
                            }
                        );
                    });
                }

                // Telefono preview
                function actualizarVistaPreviaTelefono() {
                    const codigo = document.getElementById('field-codigo_pais')?.value || '+591';
                    const numero = document.getElementById('field-telefono_numero')?.value || '';
                    const completo = document.getElementById('cliente-telefono-completo');
                    if (completo) completo.textContent = codigo + ' ' + (numero || '');
                }
                document.getElementById('field-codigo_pais')?.addEventListener('change', actualizarVistaPreviaTelefono);
                document.getElementById('field-telefono_numero')?.addEventListener('input', actualizarVistaPreviaTelefono);
                actualizarVistaPreviaTelefono();

                // Vehiculos clone
                const container = document.getElementById('vehiculos-container');
                const btnAgregar = document.getElementById('btn-agregar-vehiculo');
                if (container && btnAgregar) {
                    let idx = container.querySelectorAll('.vehiculo-row').length;
                    btnAgregar.addEventListener('click', function () {
                        const row = container.querySelector('.vehiculo-row');
                        if (!row) return;
                        const clone = row.cloneNode(true);
                        clone.dataset.index = idx;
                        clone.querySelectorAll('input').forEach(function (el) {
                            const name = el.getAttribute('name');
                            if (name) el.name = name.replace(/\d+/, idx);
                            el.value = '';
                            el.classList.remove('is-invalid');
                        });
                        if (!clone.querySelector('.btn-eliminar-vehiculo')) {
                            const btnRemove = document.createElement('button');
                            btnRemove.type = 'button';
                            btnRemove.className = 'btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 btn-eliminar-vehiculo';
                            btnRemove.innerHTML = '<i class="bi bi-x-lg"></i>';
                            btnRemove.title = 'Eliminar vehículo';
                            btnRemove.addEventListener('click', function () { clone.remove(); });
                            clone.style.position = 'relative';
                            clone.appendChild(btnRemove);
                        }
                        container.appendChild(clone);
                        idx++;
                    });
                }
            });
        </script>
    @endpush
@endonce