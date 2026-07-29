@extends('layouts.cliente-sidebar')

@section('title', 'Solicitar cita')
@section('navbar-title', 'Solicitar cita')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.citas') }}" class="text-decoration-none small">&larr; Volver a mis citas</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Nueva solicitud de cita</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.citas.store') }}" id="citaForm">
                @csrf
                <input type="hidden" name="tipo" value="diagnostico">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vehiculo_id" class="form-label small fw-semibold">Vehículo <span class="text-danger">*</span></label>
                        <select name="vehiculo_id" id="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                            <option value="">Seleccionar vehículo</option>
                            @foreach ($vehiculos as $v)
                                <option value="{{ $v->id }}" {{ old('vehiculo_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->placa }} — {{ $v->marca ?? '' }} {{ $v->modelo ?? '' }} ({{ $v->anio ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion_problema" class="form-label small fw-semibold">Descripción del problema</label>
                        <textarea name="descripcion_problema" id="descripcion_problema" class="form-control @error('descripcion_problema') is-invalid @enderror"
                                  rows="3" placeholder="Describe el problema que tiene tu vehículo...">{{ old('descripcion_problema') }}</textarea>
                        @error('descripcion_problema')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Selecciona tu sucursal más cercana <span class="text-danger">*</span></label>
                        <input type="hidden" name="sucursal_id" id="sucursal_id" value="{{ old('sucursal_id') }}">
                        <div id="sucursal-seleccionada" class="small text-success mb-2" style="display:none;">
                            <i class="bi bi-check-circle-fill"></i> Sucursal seleccionada: <strong id="sucursal-nombre"></strong> <span id="sucursal-distancia"></span>
                        </div>
                        <div id="sucursal-loading" class="text-center text-muted small py-4">
                            <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                            <div>Cargando mapa y buscando sucursales cercanas...</div>
                        </div>
                        <div id="cita-map" style="height:350px;border-radius:8px;display:none;"></div>
                        @error('sucursal_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha" class="form-label small fw-semibold">Fecha preferida <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha') }}" min="{{ now()->format('Y-m-d') }}" required>
                        @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Hora preferida <span class="text-danger">*</span></label>
                        <input type="hidden" name="hora" id="hora" value="{{ old('hora') }}">
                        <div class="time-slots-grid" id="timeSlots">
                            <div class="text-muted small">Selecciona una fecha primero</div>
                        </div>
                        @error('hora')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="deja_vehiculo" id="deja_vehiculo" value="1" {{ old('deja_vehiculo') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="deja_vehiculo">Dejaré el vehículo en el taller</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="observaciones" class="form-label small fw-semibold">Observaciones</label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                               value="{{ old('observaciones') }}" placeholder="Ej: llegaré después de las 14:00">
                        @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <hr>
                        <p class="small text-muted">Tu cita se registrará como <strong>solicitada</strong>. El taller la confirmará a la brevedad y, si es necesario, podrá proponer un horario alternativo.</p>
                        <button type="submit" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-calendar-check me-1"></i>Solicitar cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #cita-map { width: 100%; z-index: 1; }

        .time-slots-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px 0;
        }

        .time-slot {
            width: calc(20% - 6px);
            min-width: 70px;
            padding: 8px 4px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            color: #1e293b;
        }

        .time-slot:hover {
            border-color: #E31E24;
            background: #fef2f2;
        }

        .time-slot.selected {
            border-color: #E31E24;
            background: #E31E24;
            color: #fff;
        }

        .time-slot:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .time-slot.pasado {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f8fafc;
            border-color: #e2e8f0;
            text-decoration: line-through;
        }

        .sucursal-marker-popup .fw-semibold {
            font-size: 0.9rem;
        }
        .sucursal-marker-popup .text-muted {
            font-size: 0.8rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sucursales = @json($sucursales);
            const mapContainer = document.getElementById('cita-map');
            const loadingEl = document.getElementById('sucursal-loading');
            const sucursalInput = document.getElementById('sucursal_id');
            const sucursalSeleccionada = document.getElementById('sucursal-seleccionada');
            const sucursalNombre = document.getElementById('sucursal-nombre');
            const sucursalDistancia = document.getElementById('sucursal-distancia');
            const fechaInput = document.getElementById('fecha');
            const timeSlots = document.getElementById('timeSlots');
            const horaInput = document.getElementById('hora');

            let map, routingControl, markers = [];
            let sucursalesConCoord = [];
            let clienteMarker = null;

            /* ---------------------------------------------------------
               FILTRAR SUCURSALES CON COORDENADAS
               --------------------------------------------------------- */
            sucursales.forEach(function (s) {
                var lat = parseFloat(s.latitud);
                var lng = parseFloat(s.longitud);
                if (!isNaN(lat) && !isNaN(lng)) {
                    sucursalesConCoord.push({ id: s.id, nombre: s.nombre, direccion: s.direccion, lat: lat, lng: lng });
                }
            });

            if (sucursalesConCoord.length === 0) {
                loadingEl.innerHTML = '<div class="text-warning py-3"><i class="bi bi-exclamation-triangle-fill"></i> No hay sucursales con ubicación registrada. Por favor, contacta al administrador.</div>';
                return;
            }

            /* ---------------------------------------------------------
               GEOLOCALIZACIÓN
               --------------------------------------------------------- */
            function obtenerUbicacion() {
                if (!navigator.geolocation) {
                    cargarMapa(null);
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var clientLat = pos.coords.latitude;
                        var clientLng = pos.coords.longitude;
                        cargarMapa({ lat: clientLat, lng: clientLng });
                    },
                    function () {
                        cargarMapa(null);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }

            /* ---------------------------------------------------------
               DISTANCIA HAVERSINE
               --------------------------------------------------------- */
            function haversineKm(lat1, lng1, lat2, lng2) {
                var R = 6371;
                var dLat = (lat2 - lat1) * Math.PI / 180;
                var dLng = (lng2 - lng1) * Math.PI / 180;
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLng / 2) * Math.sin(dLng / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            function distanciaTexto(km) {
                if (km < 1) return Math.round(km * 1000) + ' m';
                return km.toFixed(1) + ' km';
            }

            /* ---------------------------------------------------------
               CARGAR MAPA
               --------------------------------------------------------- */
            function cargarMapa(clientePos) {
                loadingEl.style.display = 'none';
                mapContainer.style.display = '';

                var centerLat = -17.7838, centerLng = -63.1823;

                if (clientePos) {
                    centerLat = clientePos.lat;
                    centerLng = clientePos.lng;
                } else if (sucursalesConCoord.length > 0) {
                    centerLat = sucursalesConCoord[0].lat;
                    centerLng = sucursalesConCoord[0].lng;
                }

                map = L.map('cita-map', { attributionControl: false }).setView([centerLat, centerLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);
                L.control.attribution({ prefix: false }).addTo(map);

                /* Marcador del cliente */
                if (clientePos) {
                    clienteMarker = L.marker([clientePos.lat, clientePos.lng], {
                        icon: L.divIcon({
                            className: '',
                            html: '<div style="background:#4361ee;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);font-size:16px;"><i class="bi bi-person-fill"></i></div>',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                            popupAnchor: [0, -16]
                        })
                    }).addTo(map);
                    clienteMarker.bindPopup('<div class="sucursal-marker-popup"><div class="fw-semibold">Tu ubicación</div></div>');
                }

                /* Marcadores de sucursales */
                var iconSucursal = L.divIcon({
                    className: '',
                    html: '<div style="background:#E31E24;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);font-size:16px;"><i class="bi bi-building"></i></div>',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -18]
                });

                sucursalesConCoord.forEach(function (s) {
                    var marker = L.marker([s.lat, s.lng], { icon: iconSucursal }).addTo(map);
                    marker.bindPopup(
                        '<div class="sucursal-marker-popup">' +
                            '<div class="fw-semibold">' + escHtml(s.nombre) + '</div>' +
                            '<div class="text-muted">' + escHtml(s.direccion) + '</div>' +
                            '<button class="btn btn-sm btn-outline-danger mt-2" onclick="seleccionarSucursal(' + s.id + ')">' +
                                '<i class="bi bi-check-circle me-1"></i>Seleccionar' +
                            '</button>' +
                        '</div>'
                    );
                    marker.on('click', function () {
                        seleccionarSucursal(s.id);
                    });
                    markers.push({ sucursal: s, marker: marker });
                });

                /* Seleccionar sucursal: old value o la más cercana */
                var oldSucId = sucursalInput.value ? parseInt(sucursalInput.value, 10) : null;
                var seleccionada = null;

                if (oldSucId) {
                    sucursalesConCoord.forEach(function (s) {
                        if (s.id === oldSucId) seleccionada = s;
                    });
                }

                if (!seleccionada && clientePos) {
                    var minDist = Infinity;
                    sucursalesConCoord.forEach(function (s) {
                        var d = haversineKm(clientePos.lat, clientePos.lng, s.lat, s.lng);
                        if (d < minDist) { minDist = d; seleccionada = s; }
                    });
                }

                if (seleccionada) {
                    seleccionarSucursal(seleccionada.id);
                }
            }

            /* ---------------------------------------------------------
               SELECCIONAR SUCURSAL
               --------------------------------------------------------- */
            window.seleccionarSucursal = function (id) {
                var s = null;
                sucursalesConCoord.forEach(function (suc) {
                    if (suc.id === id) s = suc;
                });
                if (!s) return;

                sucursalInput.value = s.id;
                sucursalNombre.textContent = s.nombre;

                if (clienteMarker) {
                    var d = haversineKm(clienteMarker.getLatLng().lat, clienteMarker.getLatLng().lng, s.lat, s.lng);
                    sucursalDistancia.textContent = '(' + distanciaTexto(d) + ')';
                }

                sucursalSeleccionada.style.display = '';
                sucursalInput.dispatchEvent(new Event('change'));

                if (clienteMarker) {
                    mostrarRuta(clienteMarker.getLatLng(), s);
                }
            };

            /* ---------------------------------------------------------
               MOSTRAR RUTA CON LEAFLET ROUTING MACHINE
               --------------------------------------------------------- */
            function mostrarRuta(origen, destino) {
                if (routingControl) {
                    map.removeControl(routingControl);
                }

                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(origen.lat, origen.lng),
                        L.latLng(destino.lat, destino.lng)
                    ],
                    routeWhileDragging: false,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    showAlternatives: false,
                    fitSelectedRoutes: true,
                    lineOptions: {
                        styles: [{ color: '#E31E24', weight: 4, opacity: 0.8 }]
                    },
                    createMarker: function () { return null; },
                    router: L.Routing.osrmv1({ language: 'es' })
                }).addTo(map);

                routingControl.on('routesfound', function (e) {
                    var route = e.routes[0];
                    var distancia = (route.summary.totalDistance / 1000).toFixed(1);
                    var tiempo = Math.round(route.summary.totalTime / 60);
                    var d = sucursalDistancia;
                    if (d) {
                        d.textContent = '(' + distancia + ' km, ~' + tiempo + ' min)';
                    }
                });
            }

            /* ---------------------------------------------------------
               HELPERS
               --------------------------------------------------------- */
            function escHtml(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            /* ---------------------------------------------------------
               INICIAR
               --------------------------------------------------------- */
            obtenerUbicacion();

            /* ---------------------------------------------------------
               SELECTOR DE HORA TIPO GOOGLE (event delegation)
               --------------------------------------------------------- */
            var slots = [];
            for (var h = 8; h <= 18; h++) {
                slots.push(('0' + h).slice(-2) + ':00');
                if (h < 18) slots.push(('0' + h).slice(-2) + ':30');
            }

            function generarSlots(fechaStr) {
                var hoy = new Date();
                var fechaSel = new Date(fechaStr + 'T00:00:00');
                var esHoy = fechaSel.toDateString() === hoy.toDateString();

                var html = '';
                slots.forEach(function (s) {
                    var deshabilitado = false;
                    var claseExtra = '';

                    if (esHoy) {
                        var partes = s.split(':');
                        var horaSlot = parseInt(partes[0], 10);
                        var minSlot = parseInt(partes[1], 10);
                        var minutosSlot = horaSlot * 60 + minSlot;
                        var minutosAhora = hoy.getHours() * 60 + hoy.getMinutes();

                        if (minutosSlot <= minutosAhora) {
                            deshabilitado = true;
                            claseExtra = 'pasado';
                        }
                    }

                    var sel = horaInput.value === s ? ' selected' : '';
                    html += '<button type="button" class="time-slot' + sel + ' ' + claseExtra + '"' +
                        (deshabilitado ? ' disabled' : '') +
                        ' data-hora="' + s + '">' + s + '</button>';
                });
                timeSlots.innerHTML = html;
            }

            /* Event delegation: un solo listener para todos los slots */
            timeSlots.addEventListener('click', function (e) {
                var btn = e.target.closest('.time-slot');
                if (!btn) return;
                if (btn.disabled) return;

                timeSlots.querySelectorAll('.time-slot').forEach(function (b) {
                    b.classList.remove('selected');
                });
                btn.classList.add('selected');
                horaInput.value = btn.getAttribute('data-hora');
            });

            /* Cuando cambia la fecha, regenerar slots */
            fechaInput.addEventListener('change', function () {
                if (this.value) {
                    generarSlots(this.value);
                } else {
                    timeSlots.innerHTML = '<div class="text-muted small">Selecciona una fecha primero</div>';
                }
            });
            fechaInput.addEventListener('input', function () {
                if (this.value) {
                    generarSlots(this.value);
                }
            });

            /* Si hay fecha preseleccionada (old), generar slots y marcar hora */
            if (fechaInput.value) {
                generarSlots(fechaInput.value);
            }
            if (horaInput.value) {
                timeSlots.querySelectorAll('.time-slot').forEach(function (btn) {
                    if (btn.getAttribute('data-hora') === horaInput.value) {
                        btn.classList.add('selected');
                    }
                });
            }

            /* Validar que se haya seleccionado hora antes de enviar */
            document.getElementById('citaForm').addEventListener('submit', function (e) {
                if (!horaInput.value) {
                    e.preventDefault();
                    alert('Por favor selecciona una hora preferida.');
                    fechaInput.focus();
                    fechaInput.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
@endpush
