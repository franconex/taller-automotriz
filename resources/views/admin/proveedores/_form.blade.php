<div class="row g-4">
    <div class="col-12 col-lg-6 d-flex flex-column gap-4">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-building" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>
            <div class="mb-3">
                <label for="field-nombre_empresa" class="form-label fw-medium">Nombre de la empresa <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-building" style="color:#64748b;"></i></span>
                    <input id="field-nombre_empresa" type="text" name="nombre_empresa"
                           value="{{ old('nombre_empresa', $proveedor->nombre_empresa ?? '') }}" required
                           class="form-control{{ $errors->has('nombre_empresa') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Repuestos ABC" style="border-left:0;"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');">
                </div>
                @if ($errors->has('nombre_empresa'))<div class="invalid-feedback d-block">{{ $errors->first('nombre_empresa') }}</div>
                @else<div class="form-text">Solo letras y espacios.</div>@endif
            </div>
            <div class="mb-0">
                <label for="field-nit" class="form-label fw-medium">NIT</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-upc" style="color:#64748b;"></i></span>
                    <input id="field-nit" type="text" name="nit"
                           value="{{ old('nit', $proveedor->nit ?? '') }}"
                           class="form-control{{ $errors->has('nit') ? ' is-invalid' : '' }}"
                           placeholder="1234567890" style="border-left:0;"
                           inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                @if ($errors->has('nit'))<div class="invalid-feedback d-block">{{ $errors->first('nit') }}</div>
                @else<div class="form-text">Solo dígitos.</div>@endif
            </div>
        </div>
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-person" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Contacto</h3>
            </div>
            <div class="mb-3">
                <label for="field-contacto" class="form-label fw-medium">Persona de contacto <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-person" style="color:#64748b;"></i></span>
                    <input id="field-contacto" type="text" name="contacto"
                           value="{{ old('contacto', $proveedor->contacto ?? '') }}" required
                           class="form-control{{ $errors->has('contacto') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Juan Pérez" style="border-left:0;"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');">
                </div>
                @if ($errors->has('contacto'))<div class="invalid-feedback d-block">{{ $errors->first('contacto') }}</div>
                @else<div class="form-text">Solo letras y espacios.</div>@endif
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Teléfono <span class="required">*</span></label>
                <div class="d-flex gap-2">
                    <div style="min-width:120px;">
                        <select name="codigo_pais" id="field-codigo_pais" required class="form-select{{ $errors->has('codigo_pais') ? ' is-invalid' : '' }}">
                            <option value="+591" {{ old('codigo_pais', $codigo_pais ?? '+591') == '+591' ? 'selected' : '' }}>+591</option>
                        </select>
                        @if ($errors->has('codigo_pais'))<div class="invalid-feedback d-block">{{ $errors->first('codigo_pais') }}</div>@endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-phone" style="color:#64748b;"></i></span>
                            <input id="field-telefono_numero" type="text" name="telefono_numero"
                                   value="{{ old('telefono_numero', $telefono_numero ?? '') }}" required
                                   class="form-control{{ $errors->has('telefono_numero') ? ' is-invalid' : '' }}"
                                   placeholder="70000001" style="border-left:0;"
                                   inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        @if ($errors->has('telefono_numero'))<div class="invalid-feedback d-block">{{ $errors->first('telefono_numero') }}</div>
                        @else<div class="form-text">Solo dígitos.</div>@endif
                    </div>
                </div>
                <div class="mt-1" style="font-size:0.8rem;color:#64748b;">Número completo: <strong id="proveedor-telefono-completo">+591 70000001</strong></div>
            </div>
            <div class="mb-3">
                <label for="field-email" class="form-label fw-medium">Correo electrónico <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-envelope" style="color:#64748b;"></i></span>
                    <input id="field-email" type="email" name="email"
                           value="{{ old('email', $proveedor->email ?? '') }}" required
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           placeholder="correo@empresa.com" style="border-left:0;">
                </div>
                @if ($errors->has('email'))<div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                @else<div class="form-text">Formato: usuario@dominio.com</div>@endif
            </div>
            <div class="mb-0">
                <label for="field-direccion" class="form-label fw-medium">Dirección <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-geo-alt" style="color:#64748b;"></i></span>
                    <input id="field-direccion" type="text" name="direccion"
                           value="{{ old('direccion', $proveedor->direccion ?? '') }}" required
                           class="form-control{{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Av. Principal #123" style="border-left:0;">
                </div>
                @if ($errors->has('direccion'))<div class="invalid-feedback d-block">{{ $errors->first('direccion') }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-map" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Ubicación</h3>
            </div>
            <div class="geocoder-wrap" style="position:relative;">
                <div class="geocoder-box">
                    <input type="text" id="proveedor-geocoder-input" placeholder="Buscar dirección en Bolivia..." autocomplete="off" />
                    <button type="button" id="proveedor-geocoder-btn">Buscar</button>
                    <button type="button" id="proveedor-geocoder-ubicacion" class="btn-ubicacion"><i class="bi bi-crosshair2" style="font-size:0.75rem;"></i> Mi ubicación</button>
                </div>
                <div id="proveedor-geocoder-suggestions" class="geocoder-suggestions" style="display:none;"></div>
            </div>
            <div class="geocoder-result" id="proveedor-geocoder-result"></div>
            <div id="proveedor-map" style="height: 260px; border-radius: 8px;"></div>
            <div class="form-text mt-1">Haz clic en el mapa para marcar la ubicación.</div>
        </div>
        <div class="admin-card-module mt-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-toggle-on" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Estado</h3>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="estado" value="0">
                <input class="form-check-input" type="checkbox" id="proveedorEstado" name="estado" value="1" @checked(old('estado', $proveedor->estado ?? true))>
                <label class="form-check-label fw-medium" for="proveedorEstado">Proveedor activo</label>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .geocoder-box { display:flex; gap:0.375rem; margin-bottom:0.5rem; }
            .geocoder-box input { flex:1; padding:0.375rem 0.75rem; font-size:0.875rem; border:1px solid #ced4da; border-radius:6px; outline:none; }
            .geocoder-box input:focus { border-color:#4361ee; box-shadow:0 0 0 2px rgba(67,97,238,0.15); }
            .geocoder-box button { padding:0.375rem 0.75rem; font-size:0.8rem; border:1px solid #ced4da; border-radius:6px; background:#fff; cursor:pointer; white-space:nowrap; }
            .geocoder-box button:hover { background:#f1f5f9; }
            .geocoder-box .btn-ubicacion { background:#fef2f2; border-color:#fecaca; color:#dc2626; }
            .geocoder-box .btn-ubicacion:hover { background:#fee2e2; }
            .geocoder-result { font-size:0.8rem; color:#64748b; margin-bottom:0.375rem; min-height:1.2em; }
            .geocoder-suggestions { position:absolute; z-index:1000; background:#fff; border:1px solid #ddd; border-radius:6px; max-height:200px; overflow-y:auto; width:100%; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
            .geocoder-suggestions .suggestion-item { padding:0.5rem 0.75rem; cursor:pointer; font-size:0.85rem; border-bottom:1px solid #f0f0f0; }
            .geocoder-suggestions .suggestion-item:hover { background:#f8fafc; }
            .geocoder-suggestions .suggestion-item .distance-badge { float:right; font-size:0.7rem; color:#64748b; }
            .geocoder-wrap { position:relative; }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dirInput = document.getElementById('field-direccion');
            const searchInput = document.getElementById('proveedor-geocoder-input');
            const searchBtn = document.getElementById('proveedor-geocoder-btn');
            const searchResult = document.getElementById('proveedor-geocoder-result');
            const ubicacionBtn = document.getElementById('proveedor-geocoder-ubicacion');
            const suggestionsBox = document.getElementById('proveedor-geocoder-suggestions');
            const mapContainer = document.getElementById('proveedor-map');
            if (!mapContainer) return;

            const BOLIVIA_BOUNDS = { minLat:-22.9, maxLat:-9.7, minLng:-69.7, maxLng:-57.5 };
            let userLat=null, userLng=null, lastReverseCall=0;
            function isInBolivia(lat,lng) { return lat>=-22.9&&lat<=-9.7&&lng>=-69.7&&lng<=-57.5; }
            function reverseGeocode(lat,lng) {
                const now=Date.now(); if(now-lastReverseCall<1200) return; lastReverseCall=now;
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+lat+'&lon='+lng+'&accept-language=es')
                    .then(r=>r.json()).then(d=>{ if(d&&d.display_name){ if(dirInput) dirInput.value=d.display_name; if(searchResult) searchResult.textContent=d.display_name; }}).catch(()=>{});
            }
            const map = L.map('proveedor-map',{minZoom:4,maxBounds:L.latLngBounds(L.latLng(-24.9,-71.7),L.latLng(-7.7,-55.5)),maxBoundsViscosity:1}).setView([-17.7838,-63.1823],14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
            const marker = L.marker([-17.7838,-63.1823],{draggable:true}).addTo(map);
            function alCambiarUbicacion(lat,lng){ reverseGeocode(lat,lng); }
            marker.on('dragend',function(){const p=marker.getLatLng(); alCambiarUbicacion(p.lat,p.lng);});
            map.on('click',function(e){ if(!isInBolivia(e.latlng.lat,e.latlng.lng)){ if(searchResult) searchResult.textContent='Debe estar dentro de Bolivia.'; return; } marker.setLatLng(e.latlng); alCambiarUbicacion(e.latlng.lat,e.latlng.lng); });
            function getDistanceFromUser(lat1,lng1,lat2,lng2){ if(lat2===null||lng2===null) return Infinity; const R=6371; const dLat=(lat2-lat1)*Math.PI/180, dLng=(lng2-lng1)*Math.PI/180; const a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)*Math.sin(dLng/2); return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a)); }
            function showSuggestions(results){
                suggestionsBox.innerHTML=''; if(results.length===0){ suggestionsBox.style.display='none'; return; }
                if(userLat!==null&&userLng!==null) results.sort((a,b)=>getDistanceFromUser(userLat,userLng,parseFloat(a.lat),parseFloat(a.lon))-getDistanceFromUser(userLat,userLng,parseFloat(b.lat),parseFloat(b.lon)));
                results.forEach(function(r){const div=document.createElement('div');div.className='suggestion-item';let label=r.display_name;if(userLat!==null&&userLng!==null){const d=getDistanceFromUser(userLat,userLng,parseFloat(r.lat),parseFloat(r.lon));label+=' <span class="distance-badge">'+(d<1?Math.round(d*1000)+' m':d.toFixed(1)+' km')+'</span>';}div.innerHTML=label;div.addEventListener('click',function(){const latR=parseFloat(r.lat),lngR=parseFloat(r.lon);marker.setLatLng([latR,lngR]);map.setView([latR,lngR],16);if(dirInput) dirInput.value=r.display_name;if(searchResult) searchResult.textContent=r.display_name;if(searchInput) searchInput.value=r.display_name.split(',')[0];suggestionsBox.style.display='none';});suggestionsBox.appendChild(div);});
                suggestionsBox.style.display='block';
            }
            if(searchBtn&&searchInput){
                searchBtn.addEventListener('click',function(){const q=searchInput.value.trim();if(!q)return;searchBtn.disabled=true;searchBtn.textContent='Buscando...';const vb=BOLIVIA_BOUNDS.minLng+','+BOLIVIA_BOUNDS.minLat+','+BOLIVIA_BOUNDS.maxLng+','+BOLIVIA_BOUNDS.maxLat;fetch('https://nominatim.openstreetmap.org/search?format=json&q='+encodeURIComponent(q)+'&limit=5&bounded=1&viewbox='+vb+'&accept-language=es').then(r=>r.json()).then(d=>{if(d&&d.length>0){showSuggestions(d);if(d.length===1){const r=d[0];marker.setLatLng([parseFloat(r.lat),parseFloat(r.lon)]);map.setView([parseFloat(r.lat),parseFloat(r.lon)],16);if(dirInput) dirInput.value=r.display_name;if(searchResult) searchResult.textContent=r.display_name;}else{if(searchResult) searchResult.textContent='Selecciona una zona.';}}else{if(searchResult) searchResult.textContent='Sin resultados en Bolivia.';}}).catch(()=>{if(searchResult) searchResult.textContent='Error.';}).finally(()=>{searchBtn.disabled=false;searchBtn.textContent='Buscar';});});
                let timer=null;searchInput.addEventListener('input',function(){clearTimeout(timer);const q=this.value.trim();if(q.length<3){suggestionsBox.style.display='none';return;}timer=setTimeout(function(){const vb=BOLIVIA_BOUNDS.minLng+','+BOLIVIA_BOUNDS.minLat+','+BOLIVIA_BOUNDS.maxLng+','+BOLIVIA_BOUNDS.maxLat;fetch('https://nominatim.openstreetmap.org/search?format=json&q='+encodeURIComponent(q)+'&limit=5&bounded=1&viewbox='+vb+'&accept-language=es').then(r=>r.json()).then(d=>{if(d&&d.length>0) showSuggestions(d);else suggestionsBox.style.display='none';}).catch(()=>{});},400);});
                searchInput.addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();searchBtn.click();}});
                document.addEventListener('click',function(e){if(!e.target.closest('#proveedor-geocoder-input')&&!e.target.closest('#proveedor-geocoder-suggestions')) suggestionsBox.style.display='none';});
            }
            if(ubicacionBtn){
                ubicacionBtn.addEventListener('click',function(){if(!navigator.geolocation){if(searchResult) searchResult.textContent='Geolocalización no disponible.';return;}ubicacionBtn.disabled=true;ubicacionBtn.textContent='Obteniendo...';navigator.geolocation.getCurrentPosition(function(pos){userLat=pos.coords.latitude;userLng=pos.coords.longitude;if(!isInBolivia(userLat,userLng)){if(searchResult) searchResult.textContent='Tu ubicación no está en Bolivia.';ubicacionBtn.disabled=false;ubicacionBtn.textContent='Mi ubicación';return;}marker.setLatLng([userLat,userLng]);map.setView([userLat,userLng],16);alCambiarUbicacion(userLat,userLng);ubicacionBtn.disabled=false;ubicacionBtn.textContent='Mi ubicación';},function(){if(searchResult) searchResult.textContent='No se pudo obtener ubicación.';ubicacionBtn.disabled=false;ubicacionBtn.textContent='Mi ubicación';});});
            }
            function actualizarPreview(){const c=document.getElementById('field-codigo_pais')?.value||'+591';const n=document.getElementById('field-telefono_numero')?.value||'';const e=document.getElementById('proveedor-telefono-completo');if(e) e.textContent=c+' '+(n||'');}
            document.getElementById('field-codigo_pais')?.addEventListener('change',actualizarPreview);
            document.getElementById('field-telefono_numero')?.addEventListener('input',actualizarPreview);
            actualizarPreview();
        });
        </script>
    @endpush
@endonce