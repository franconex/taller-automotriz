<div class="row g-4">

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-building" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>

            <div class="mb-3">
                <label for="field-nombre" class="form-label fw-medium">
                    Nombre de la sucursal <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-building" style="color:#64748b;"></i>
                    </span>
                    <input id="field-nombre"
                           type="text"
                           name="nombre"
                           value="{{ old('nombre', $sucursal->nombre ?? '') }}"
                           required
                           class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Sucursal Centro"
                           style="border-left:0;"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')">
                </div>
                @if ($errors->has('nombre'))
                    <div class="invalid-feedback d-block">{{ $errors->first('nombre') }}</div>
                @else
                    <div class="form-text">Solo se permiten letras y espacios, no números ni caracteres especiales.</div>
                @endif
            </div>

            <div class="mb-3">
                <label for="field-direccion" class="form-label fw-medium">
                    Dirección <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-geo-alt" style="color:#64748b;"></i>
                    </span>
                    <input id="field-direccion"
                           type="text"
                           name="direccion"
                           value="{{ old('direccion', $sucursal->direccion ?? '') }}"
                           required
                           class="form-control{{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Av. Principal #123"
                           style="border-left:0;">
                </div>
                @if ($errors->has('direccion'))
                    <div class="invalid-feedback d-block">{{ $errors->first('direccion') }}</div>
                @endif
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
                    Número completo: <strong id="sucursal-telefono-completo">+591 70000001</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-clock" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Horario de atención</h3>
            </div>

            <input type="hidden" name="horario_atencion" id="field-horario_atencion" value="{{ old('horario_atencion', '') }}">
            @if ($errors->has('horario_atencion'))
                <div class="invalid-feedback d-block mb-2">{{ $errors->first('horario_atencion') }}</div>
            @endif

            @php
                $horario = [];
                if (old('horario_atencion')) {
                    $horario = json_decode(old('horario_atencion'), true) ?? [];
                } elseif (isset($sucursal->horario_atencion) && is_array($sucursal->horario_atencion)) {
                    $horario = $sucursal->horario_atencion;
                }
                $wOpen = $horario['weekday']['open'] ?? '08:00';
                $wClose = $horario['weekday']['close'] ?? '18:00';
                $sOpen = $horario['saturday']['open'] ?? '09:00';
                $sClose = $horario['saturday']['close'] ?? '13:00';
            @endphp

            <div class="bg-light rounded p-3 mb-3">
                <label class="fw-medium d-block mb-2" style="font-size:0.85rem;">Lunes a Viernes</label>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <div>
                        <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:2px;">Apertura</label>
                        <input type="time" id="weekday-open" class="form-control horario-time" style="width:150px;" value="{{ $wOpen }}">
                    </div>
                    <span style="color:#94a3b8;margin-top:1.2rem;">—</span>
                    <div>
                        <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:2px;">Cierre</label>
                        <input type="time" id="weekday-close" class="form-control horario-time" style="width:150px;" value="{{ $wClose }}">
                    </div>
                </div>
            </div>

            <div class="bg-light rounded p-3 mb-3">
                <label class="fw-medium d-block mb-2" style="font-size:0.85rem;">Sábado</label>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <div>
                        <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:2px;">Apertura</label>
                        <input type="time" id="saturday-open" class="form-control horario-time" style="width:150px;" value="{{ $sOpen }}">
                    </div>
                    <span style="color:#94a3b8;margin-top:1.2rem;">—</span>
                    <div>
                        <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:2px;">Cierre</label>
                        <input type="time" id="saturday-close" class="form-control horario-time" style="width:150px;" value="{{ $sClose }}">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Día</th>
                            <th>Horario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lunes a Viernes</td>
                            <td id="preview-weekday">{{ $wOpen }} — {{ $wClose }}</td>
                        </tr>
                        <tr>
                            <td>Sábado</td>
                            <td id="preview-saturday">{{ $sOpen }} — {{ $sClose }}</td>
                        </tr>
                        <tr>
                            <td>Domingo</td>
                            <td class="text-muted">Cerrado</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-map" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Ubicación en el mapa</h3>
            </div>

            <input type="hidden" name="latitud" id="field-latitud" value="{{ old('latitud', $sucursal->latitud ?? '') }}">
            <input type="hidden" name="longitud" id="field-longitud" value="{{ old('longitud', $sucursal->longitud ?? '') }}">

            <div class="geocoder-wrap" style="position: relative;">
                <div class="geocoder-box">
                    <input type="text" id="geocoder-input" placeholder="Buscar zona o dirección en Bolivia..." autocomplete="off" />
                    <button type="button" id="geocoder-btn">Buscar</button>
                    <button type="button" id="geocoder-ubicacion" class="btn-ubicacion">
                        <i class="bi bi-crosshair2" style="font-size:0.75rem;"></i> Mi ubicación
                    </button>
                </div>
                <div id="geocoder-suggestions" class="geocoder-suggestions" style="display:none;"></div>
            </div>
            <div class="geocoder-result" id="geocoder-result"></div>
            <div id="sucursal-map" style="height: 300px; border-radius: 8px;"></div>
            <div class="form-text mt-1">Haz clic en el mapa para marcar la ubicación o arrastra el marcador.</div>

            <div class="d-flex align-items-center gap-3 mt-2">
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="estado" value="0">
                    <input class="form-check-input" type="checkbox" id="sucursalEstado" name="estado" value="1"
                        @checked(old('estado', $sucursal->estado ?? true))>
                    <label class="form-check-label fw-medium" for="sucursalEstado">Sucursal activa</label>
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .admin-card-modern { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .admin-card-module { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.25rem; height:100%; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .badge-module { flex-shrink:0; }
        .geocoder-box { display: flex; gap: 0.375rem; margin-bottom: 0.5rem; }
        .geocoder-box input { flex: 1; padding: 0.375rem 0.75rem; font-size: 0.875rem; border: 1px solid var(--border, #ced4da); border-radius: 6px; outline: none; }
        .geocoder-box input:focus { border-color: var(--primary, #4361ee); box-shadow: 0 0 0 2px rgba(67,97,238,0.15); }
        .geocoder-box button { padding: 0.375rem 0.75rem; font-size: 0.8rem; border: 1px solid var(--border, #ced4da); border-radius: 6px; background: var(--surface, #fff); cursor: pointer; white-space: nowrap; }
        .geocoder-box button:hover { background: var(--bg-subtle, #f1f5f9); }
        .geocoder-box .btn-ubicacion { background: #e8f4fd; border-color: #bfdbfe; color: #2563eb; }
        .geocoder-box .btn-ubicacion:hover { background: #dbeafe; }
        .geocoder-result { font-size: 0.8rem; color: var(--muted, #64748b); margin-bottom: 0.375rem; min-height: 1.2em; }
        .geocoder-suggestions { position: absolute; z-index: 1000; background: #fff; border: 1px solid #ddd; border-radius: 6px; max-height: 200px; overflow-y: auto; width: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .geocoder-suggestions .suggestion-item { padding: 0.5rem 0.75rem; cursor: pointer; font-size: 0.85rem; border-bottom: 1px solid #f0f0f0; }
        .geocoder-suggestions .suggestion-item:hover { background: #f1f5f9; }
        .geocoder-suggestions .suggestion-item .distance-badge { float: right; font-size: 0.7rem; color: #64748b; }
        .geocoder-wrap { position: relative; }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('field-latitud');
            const lngInput = document.getElementById('field-longitud');
            const direccionInput = document.getElementById('field-direccion');
            const searchInput = document.getElementById('geocoder-input');
            const searchBtn = document.getElementById('geocoder-btn');
            const searchResult = document.getElementById('geocoder-result');
            const ubicacionBtn = document.getElementById('geocoder-ubicacion');
            const suggestionsBox = document.getElementById('geocoder-suggestions');

            const BOLIVIA_BOUNDS = {
                minLat: -22.9, maxLat: -9.7,
                minLng: -69.7, maxLng: -57.5
            };

            let userLat = null;
            let userLng = null;
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
                            if (direccionInput) {
                                direccionInput.value = data.display_name;
                            }
                            if (searchResult) searchResult.textContent = data.display_name;
                        }
                    })
                    .catch(function () {});
            }

            const defaultLat = -17.7838;
            const defaultLng = -63.1823;
            const lat = parseFloat(latInput?.value) || defaultLat;
            const lng = parseFloat(lngInput?.value) || defaultLng;

            const map = L.map('sucursal-map', {
                minZoom: 4,
                maxBounds: L.latLngBounds(
                    L.latLng(BOLIVIA_BOUNDS.minLat - 2, BOLIVIA_BOUNDS.minLng - 2),
                    L.latLng(BOLIVIA_BOUNDS.maxLat + 2, BOLIVIA_BOUNDS.maxLng + 2)
                ),
                maxBoundsViscosity: 1.0
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            function actualizarCoordenadas(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            function alCambiarUbicacion(lat, lng) {
                actualizarCoordenadas(lat, lng);
                reverseGeocode(lat, lng);
            }

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

            function getDistanceFromUser(lat1, lng1, lat2, lng2) {
                if (lat2 === null || lng2 === null) return Infinity;
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLng/2) * Math.sin(dLng/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            function showSuggestions(results) {
                suggestionsBox.innerHTML = '';
                if (results.length === 0) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                if (userLat !== null && userLng !== null) {
                    results.sort(function (a, b) {
                        const distA = getDistanceFromUser(userLat, userLng, parseFloat(a.lat), parseFloat(a.lon));
                        const distB = getDistanceFromUser(userLat, userLng, parseFloat(b.lat), parseFloat(b.lon));
                        return distA - distB;
                    });
                }

                results.forEach(function (r) {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    let label = r.display_name;
                    if (userLat !== null && userLng !== null) {
                        const dist = getDistanceFromUser(userLat, userLng, parseFloat(r.lat), parseFloat(r.lon));
                        const distText = dist < 1 ? (Math.round(dist * 1000) + ' m') : (dist.toFixed(1) + ' km');
                        label += ' <span class="distance-badge">' + distText + '</span>';
                    }
                    div.innerHTML = label;
                    div.addEventListener('click', function () {
                        const latR = parseFloat(r.lat);
                        const lngR = parseFloat(r.lon);
                        marker.setLatLng([latR, lngR]);
                        map.setView([latR, lngR], 16);
                        if (direccionInput) direccionInput.value = r.display_name;
                        if (searchResult) searchResult.textContent = r.display_name;
                        if (searchInput) searchInput.value = r.display_name.split(',')[0];
                        actualizarCoordenadas(latR, lngR);
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
                    searchBtn.disabled = true;
                    searchBtn.textContent = 'Buscando...';
                    const viewbox = BOLIVIA_BOUNDS.minLng + ',' + BOLIVIA_BOUNDS.minLat + ',' + BOLIVIA_BOUNDS.maxLng + ',' + BOLIVIA_BOUNDS.maxLat;
                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&bounded=1&viewbox=' + viewbox + '&accept-language=es')
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data && data.length > 0) {
                                showSuggestions(data);
                                if (data.length === 1) {
                                    const r = data[0];
                                    const latR = parseFloat(r.lat);
                                    const lngR = parseFloat(r.lon);
                                    marker.setLatLng([latR, lngR]);
                                    map.setView([latR, lngR], 16);
                                    if (direccionInput) direccionInput.value = r.display_name;
                                    if (searchResult) searchResult.textContent = r.display_name;
                                    actualizarCoordenadas(latR, lngR);
                                } else {
                                    if (searchResult) searchResult.textContent = 'Selecciona una zona de la lista.';
                                }
                            } else {
                                if (searchResult) searchResult.textContent = 'No se encontraron resultados en Bolivia.';
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

                let autocompleteTimer = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(autocompleteTimer);
                    const q = this.value.trim();
                    if (q.length < 3) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }
                    autocompleteTimer = setTimeout(function () {
                        const viewbox = BOLIVIA_BOUNDS.minLng + ',' + BOLIVIA_BOUNDS.minLat + ',' + BOLIVIA_BOUNDS.maxLng + ',' + BOLIVIA_BOUNDS.maxLat;
                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&bounded=1&viewbox=' + viewbox + '&accept-language=es')
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (data && data.length > 0) {
                                    showSuggestions(data);
                                } else {
                                    suggestionsBox.style.display = 'none';
                                }
                            })
                            .catch(function () {});
                    }, 400);
                });

                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchBtn.click();
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!e.target.closest('#geocoder-input') && !e.target.closest('#geocoder-suggestions')) {
                        suggestionsBox.style.display = 'none';
                    }
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
                            userLat = pos.coords.latitude;
                            userLng = pos.coords.longitude;
                            if (!isInBolivia(userLat, userLng)) {
                                if (searchResult) searchResult.textContent = 'Tu ubicación actual no se encuentra dentro de Bolivia.';
                                ubicacionBtn.disabled = false;
                                ubicacionBtn.textContent = 'Mi ubicación';
                                return;
                            }
                            marker.setLatLng([userLat, userLng]);
                            map.setView([userLat, userLng], 16);
                            alCambiarUbicacion(userLat, userLng);
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

            function actualizarVistaPreviaTelefono() {
                const codigo = document.getElementById('field-codigo_pais')?.value || '+591';
                const numero = document.getElementById('field-telefono_numero')?.value || '70000001';
                const completo = document.getElementById('sucursal-telefono-completo');
                if (completo) {
                    completo.textContent = codigo + ' ' + (numero || '');
                }
            }

            document.getElementById('field-codigo_pais')?.addEventListener('change', actualizarVistaPreviaTelefono);
            document.getElementById('field-telefono_numero')?.addEventListener('input', actualizarVistaPreviaTelefono);
            actualizarVistaPreviaTelefono();

            function actualizarHorarioJSON() {
                const wOpen = document.getElementById('weekday-open')?.value || '08:00';
                const wClose = document.getElementById('weekday-close')?.value || '18:00';
                const sOpen = document.getElementById('saturday-open')?.value || '09:00';
                const sClose = document.getElementById('saturday-close')?.value || '13:00';

                const horario = {
                    weekday: { open: wOpen, close: wClose },
                    saturday: { open: sOpen, close: sClose }
                };

                document.getElementById('field-horario_atencion').value = JSON.stringify(horario);

                document.getElementById('preview-weekday').textContent = wOpen + ' — ' + wClose;
                document.getElementById('preview-saturday').textContent = sOpen + ' — ' + sClose;
            }

            document.querySelectorAll('.horario-time').forEach(function (el) {
                el.addEventListener('change', actualizarHorarioJSON);
            });
            actualizarHorarioJSON();
        });
    </script>
@endpush