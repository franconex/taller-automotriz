<div class="row g-4">

    <div class="col-12 col-lg-6 d-flex flex-column gap-4">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-badge" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
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
                           value="{{ old('nombre_completo', $empleado->nombre_completo ?? '') }}"
                           required
                           class="form-control{{ $errors->has('nombre_completo') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Juan Pérez"
                           style="border-left:0;"
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
                    Cédula de identidad <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-card-text" style="color:#64748b;"></i>
                    </span>
                    <input id="field-ci"
                           type="text"
                           name="ci"
                           value="{{ old('ci', $empleado->ci ?? '') }}"
                           required
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
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-briefcase" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Asignación</h3>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="field-sucursal_id" class="form-label fw-medium">
                        Sucursal <span class="required" aria-hidden="true">*</span>
                    </label>
                    <select name="sucursal_id"
                            id="field-sucursal_id"
                            required
                            class="form-select{{ $errors->has('sucursal_id') ? ' is-invalid' : '' }}">
                        <option value="">— Selecciona una sucursal —</option>
                        @foreach (($sucursales ?? collect()) as $s)
                            <option value="{{ $s->id }}" @selected(old('sucursal_id', $empleado->sucursal_id ?? null) == $s->id)>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('sucursal_id'))
                        <div class="invalid-feedback d-block">{{ $errors->first('sucursal_id') }}</div>
                    @endif
                </div>
                <div class="col-12 col-md-6">
                    <label for="field-rol_id" class="form-label fw-medium">
                        Rol <span class="required" aria-hidden="true">*</span>
                    </label>
                    <select name="rol_id"
                            id="field-rol_id"
                            required
                            class="form-select{{ $errors->has('rol_id') ? ' is-invalid' : '' }}">
                        <option value="">— Selecciona un rol —</option>
                        @foreach (($roles ?? collect()) as $r)
                            <option value="{{ $r->id }}"
                                data-rol-nombre="{{ $r->nombre }}"
                                @selected(old('rol_id', $empleado->rol_id ?? null) == $r->id)>
                                {{ $r->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('rol_id'))
                        <div class="invalid-feedback d-block">{{ $errors->first('rol_id') }}</div>
                    @endif
                </div>
                <div class="col-12 col-md-6">
                    <label for="field-cargo" class="form-label fw-medium">
                        Cargo (opcional)
                    </label>
                    <input id="field-cargo"
                           type="text"
                           name="cargo"
                           value="{{ old('cargo', $empleado->cargo ?? '') }}"
                           class="form-control{{ $errors->has('cargo') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Mecánico Senior">
                    @if ($errors->has('cargo'))
                        <div class="invalid-feedback d-block">{{ $errors->first('cargo') }}</div>
                    @else
                        <div class="form-text">Especifica el puesto concreto.</div>
                    @endif
                </div>
                <div class="col-12 col-md-6">
                    <label for="field-fecha_contratacion" class="form-label fw-medium">
                        Fecha de contratación
                    </label>
                    <input id="field-fecha_contratacion"
                           type="date"
                           name="fecha_contratacion"
                           value="{{ old('fecha_contratacion', optional($empleado)->fecha_contratacion?->format('Y-m-d')) }}"
                           class="form-control{{ $errors->has('fecha_contratacion') ? ' is-invalid' : '' }}">
                    @if ($errors->has('fecha_contratacion'))
                        <div class="invalid-feedback d-block">{{ $errors->first('fecha_contratacion') }}</div>
                    @endif
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="estado" value="0">
                        <input class="form-check-input" type="checkbox" id="empleadoEstado" name="estado" value="1"
                            @checked(old('estado', $empleado->estado ?? true))>
                        <label class="form-check-label fw-medium" for="empleadoEstado">Empleado activo</label>
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
                    Número completo: <strong id="empleado-telefono-completo">+591 70000001</strong>
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
                           value="{{ old('email', $empleado->email ?? '') }}"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           placeholder="ej: correo@dominio.com"
                           style="border-left:0;">
                </div>
                @if ($errors->has('email'))
                    <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                @else
                    <div class="form-text">Formato: usuario@dominio.com</div>
                @endif
            </div>

            <div class="mb-3">
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
                           value="{{ old('direccion', $empleado->direccion ?? '') }}"
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
                        <input type="text" id="empleado-geocoder-input" placeholder="Buscar zona o dirección en Bolivia..." autocomplete="off" />
                        <button type="button" id="empleado-geocoder-btn">Buscar</button>
                        <button type="button" id="empleado-geocoder-ubicacion" class="btn-ubicacion">
                            <i class="bi bi-crosshair2" style="font-size:0.75rem;"></i> Mi ubicación
                        </button>
                    </div>
                    <div id="empleado-geocoder-suggestions" class="geocoder-suggestions" style="display:none;"></div>
                </div>
                <div class="geocoder-result" id="empleado-geocoder-result"></div>
                <div id="empleado-map" style="height: 260px; border-radius: 8px;"></div>
                <div class="form-text mt-1">Haz clic en el mapa para marcar la ubicación o arrastra el marcador.</div>
            </div>
        </div>
    </div>

    <div class="col-12" id="mecanico-fields" style="display: none;">
        <div class="admin-card-module" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-wrench-adjustable-circle" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Datos de mecánico</h3>
            </div>
            <p class="text-muted small mb-3">Como el rol seleccionado es <strong>Mecánico</strong>, también debes registrar su especialidad y disponibilidad.</p>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="field-especialidad" class="form-label fw-medium">Especialidad</label>
                    <select name="especialidad"
                            id="field-especialidad"
                            class="form-select{{ $errors->has('especialidad') ? ' is-invalid' : '' }}">
                        <option value="">— Selecciona una especialidad —</option>
                        @foreach (($especialidades ?? collect()) as $e)
                            <option value="{{ $e->id }}" @selected(old('especialidad', optional(optional($empleado)->mecanico)->especialidad_id) == $e->id)>
                                {{ $e->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('especialidad'))
                        <div class="invalid-feedback d-block">{{ $errors->first('especialidad') }}</div>
                    @endif
                </div>
                <div class="col-12 col-md-4">
                    <label for="field-disponibilidad" class="form-label fw-medium">Disponibilidad</label>
                    <select name="disponibilidad"
                            id="field-disponibilidad"
                            class="form-select{{ $errors->has('disponibilidad') ? ' is-invalid' : '' }}">
                        <option value="disponible" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad ?? 'disponible') === 'disponible')>Disponible</option>
                        <option value="ocupado" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ocupado')>Ocupado</option>
                        <option value="ausente" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ausente')>Ausente</option>
                    </select>
                    @if ($errors->has('disponibilidad'))
                        <div class="invalid-feedback d-block">{{ $errors->first('disponibilidad') }}</div>
                    @endif
                </div>
                <div class="col-12 col-md-4">
                    <label for="field-observaciones_mecanico" class="form-label fw-medium">Observaciones</label>
                    <textarea name="observaciones_mecanico"
                              id="field-observaciones_mecanico"
                              rows="1"
                              class="form-control{{ $errors->has('observaciones_mecanico') ? ' is-invalid' : '' }}"
                              placeholder="Anotaciones adicionales...">{{ old('observaciones_mecanico', optional(optional($empleado)->mecanico)->observaciones) }}</textarea>
                    @if ($errors->has('observaciones_mecanico'))
                        <div class="invalid-feedback d-block">{{ $errors->first('observaciones_mecanico') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@once
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
                function actualizarVistaPreviaTelefono() {
                    const codigo = document.getElementById('field-codigo_pais')?.value || '+591';
                    const numero = document.getElementById('field-telefono_numero')?.value || '';
                    const completo = document.getElementById('empleado-telefono-completo');
                    if (completo) {
                        completo.textContent = codigo + ' ' + (numero || '');
                    }
                }
                document.getElementById('field-codigo_pais')?.addEventListener('change', actualizarVistaPreviaTelefono);
                document.getElementById('field-telefono_numero')?.addEventListener('input', actualizarVistaPreviaTelefono);
                actualizarVistaPreviaTelefono();
            });

            document.addEventListener('DOMContentLoaded', function () {
                const direccionInput = document.getElementById('field-direccion');
                const searchInput = document.getElementById('empleado-geocoder-input');
                const searchBtn = document.getElementById('empleado-geocoder-btn');
                const searchResult = document.getElementById('empleado-geocoder-result');
                const ubicacionBtn = document.getElementById('empleado-geocoder-ubicacion');
                const suggestionsBox = document.getElementById('empleado-geocoder-suggestions');
                const mapContainer = document.getElementById('empleado-map');

                if (!mapContainer) return;

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
                                if (direccionInput) direccionInput.value = data.display_name;
                                if (searchResult) searchResult.textContent = data.display_name;
                            }
                        })
                        .catch(function () {});
                }

                const defaultLat = -17.7838;
                const defaultLng = -63.1823;

                const map = L.map('empleado-map', {
                    minZoom: 4,
                    maxBounds: L.latLngBounds(
                        L.latLng(BOLIVIA_BOUNDS.minLat - 2, BOLIVIA_BOUNDS.minLng - 2),
                        L.latLng(BOLIVIA_BOUNDS.maxLat + 2, BOLIVIA_BOUNDS.maxLng + 2)
                    ),
                    maxBoundsViscosity: 1.0
                }).setView([defaultLat, defaultLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

                function alCambiarUbicacion(lat, lng) {
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
                        if (!e.target.closest('#empleado-geocoder-input') && !e.target.closest('#empleado-geocoder-suggestions')) {
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
            });
        </script>
    @endpush
@endonce
