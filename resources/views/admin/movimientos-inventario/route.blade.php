@extends('layouts.admin')

@section('title', 'Ruta del movimiento #' . $movimiento->id)
@section('navbar-title', 'Ruta del movimiento #' . $movimiento->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.movimientos-inventario.index') }}">Movimientos de inventario</a></li>
    <li><a href="{{ route('admin.movimientos-inventario.show', $movimiento) }}">#{{ $movimiento->id }}</a></li>
    <li class="active" aria-current="page">Ruta</li>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #route-map { height: 500px; border-radius: 8px; z-index: 1; }
        .route-info-card { background: var(--surface, #fff); border: 1px solid var(--border, #e2e8f0); border-radius: 8px; padding: 1.25rem; }
        .route-info-card h3 { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted, #64748b); margin-bottom: 0.5rem; }
        .route-info-card .value { font-size: 1rem; font-weight: 500; }
        .route-info-card .address { font-size: 0.85rem; color: var(--muted, #64748b); }
        .route-instructions { max-height: 380px; overflow-y: auto; }
        .route-instructions ol { padding-left: 1.25rem; margin: 0; }
        .route-instructions li { padding: 0.375rem 0; font-size: 0.9rem; border-bottom: 1px solid var(--border, #e2e8f0); }
        .route-instructions li:last-child { border-bottom: none; }
        .route-stat { text-align: center; padding: 0.75rem; border-radius: 8px; background: var(--bg-subtle, #f8fafc); }
        .route-stat .stat-value { font-size: 1.25rem; font-weight: 700; line-height: 1.2; }
        .route-stat .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted, #64748b); margin-top: 0.25rem; }
        .sucursal-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0; font-size: 0.875rem; }
        .sucursal-item .color-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .leaflet-routing-container { display: none; }
    </style>
@endpush

@section('content')
    <x-admin.page-header
        :title="'Ruta del movimiento #' . $movimiento->id"
        :description="$movimiento->fecha_movimiento?->format('d/m/Y H:i')">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.show', $movimiento) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver al detalle
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @php
        $origen = $movimiento->sucursalOrigen ?? $movimiento->inventario->sucursal;
        $destino = $movimiento->sucursalDestino;

        $origenData = ($origen && $origen->latitud !== null && $origen->longitud !== null) ? [
            'id' => $origen->id,
            'nombre' => $origen->nombre,
            'direccion' => $origen->direccion,
            'lat' => (float) $origen->latitud,
            'lng' => (float) $origen->longitud,
        ] : null;

        $destinoData = ($destino && $destino->latitud !== null && $destino->longitud !== null && $destino->id !== ($origen->id ?? null)) ? [
            'id' => $destino->id,
            'nombre' => $destino->nombre,
            'direccion' => $destino->direccion,
            'lat' => (float) $destino->latitud,
            'lng' => (float) $destino->longitud,
        ] : null;

        $sucursalesData = $sucursales->map(fn ($s) => [
            'id' => $s->id,
            'nombre' => $s->nombre,
            'direccion' => $s->direccion,
            'lat' => $s->latitud !== null ? (float) $s->latitud : null,
            'lng' => $s->longitud !== null ? (float) $s->longitud : null,
        ]);
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <div class="route-info-card">
                <h3><i class="bi bi-box-seam" aria-hidden="true"></i> Repuesto</h3>
                <div class="value">{{ optional($movimiento->inventario->repuesto)->nombre ?? '—' }}</div>
                <div class="address">{{ optional($movimiento->inventario->repuesto)->codigo ?? '' }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="route-info-card">
                <h3><i class="bi bi-geo-alt" aria-hidden="true"></i> Origen</h3>
                @if ($origen)
                    <div class="value">{{ $origen->nombre }}</div>
                    <div class="address">{{ $origen->direccion }}</div>
                @else
                    <div class="value text-muted">—</div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="route-info-card">
                <h3><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Destino</h3>
                @if ($destino)
                    <div class="value">{{ $destino->nombre }}</div>
                    <div class="address">{{ $destino->direccion }}</div>
                @else
                    <div class="value text-muted">—</div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="admin-table-wrap p-0 overflow-hidden">
                <div id="route-map"></div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="route-info-card mb-3" id="panel-estadisticas">
                <h3><i class="bi bi-graph-up" aria-hidden="true"></i> Información de la ruta</h3>
                <div class="row g-2 mt-1" id="ruta-estadisticas-content">
                    <div class="col-6">
                        <div class="route-stat">
                            <div class="stat-value" id="ruta-distancia">—</div>
                            <div class="stat-label">Distancia</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="route-stat">
                            <div class="stat-value" id="ruta-tiempo">—</div>
                            <div class="stat-label">Tiempo estimado</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="route-info-card mb-3" id="panel-instrucciones">
                <h3><i class="bi bi-sign-turn-right" aria-hidden="true"></i> Calles a seguir</h3>
                <div class="route-instructions" id="ruta-instrucciones-lista">
                    <ol></ol>
                </div>
            </div>

            <div class="route-info-card">
                <h3><i class="bi bi-building" aria-hidden="true"></i> Sucursales en el mapa</h3>
                <div id="lista-sucursales">
                    @forelse ($sucursales as $s)
                        <div class="sucursal-item">
                            <span class="color-dot" style="background: {{ $origen && $s->id === $origen->id ? '#22c55e' : ($destino && $s->id === $destino->id ? '#ef4444' : ($s->latitud !== null && $s->longitud !== null ? '#3b82f6' : '#cbd5e1')) }}"></span>
                            <span>{{ $s->nombre }}</span>
                            @if ($s->latitud === null || $s->longitud === null)
                                <span class="text-muted small">(sin coordenadas)</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted small">No hay sucursales registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="route-info-card">
                <h3><i class="bi bi-info-circle" aria-hidden="true"></i> Detalles del movimiento</h3>
                <dl class="admin-meta mb-0">
                    <dt>Tipo</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($movimiento->tipo) {
                                'entrada' => 'success',
                                'salida' => 'warning',
                                'ajuste' => 'info',
                                'transferencia' => 'primary',
                                default => 'neutral',
                            }"
                            :label="match($movimiento->tipo) {
                                'transferencia' => 'Transferencia',
                                default => ucfirst($movimiento->tipo),
                            }" />
                    </dd>
                    <dt>Cantidad</dt><dd>{{ $movimiento->cantidad }}</dd>
                    <dt>Motivo</dt><dd>{{ $movimiento->motivo }}</dd>
                    <dt>Registrado por</dt><dd>{{ $movimiento->usuario->nombre ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof L === 'undefined') {
                console.error('Leaflet no se cargó');
                return;
            }

            const map = L.map('route-map').setView([-17.7838, -63.1823], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const sucursalOrigen = @json($origenData);
            const sucursalDestino = @json($destinoData);
            const todasLasSucursales = @json($sucursalesData);

            const greenIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            });

            const redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            });

            const blueIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            });

            const markersBounds = [];

            todasLasSucursales.forEach(function (s) {
                if (!s.lat || !s.lng) return;
                let icon = blueIcon;
                if (sucursalOrigen && s.id === sucursalOrigen.id) icon = greenIcon;
                if (sucursalDestino && s.id === sucursalDestino.id) icon = redIcon;
                L.marker([s.lat, s.lng], { icon })
                    .addTo(map)
                    .bindPopup('<strong>' + s.nombre + '</strong><br>' + (s.direccion || ''));
                markersBounds.push([s.lat, s.lng]);
            });

            if (sucursalOrigen && sucursalDestino) {
                const routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(sucursalOrigen.lat, sucursalOrigen.lng),
                        L.latLng(sucursalDestino.lat, sucursalDestino.lng),
                    ],
                    routeWhileDragging: false,
                    showAlternatives: false,
                    fitSelectedRoutes: true,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    language: 'es',
                    lineOptions: {
                        styles: [{ color: '#3b82f6', weight: 5, opacity: 0.8 }],
                    },
                    createMarker: function () { return null; },
                }).addTo(map);

                document.getElementById('ruta-distancia').textContent = 'Calculando...';
                document.getElementById('ruta-tiempo').textContent = 'Calculando...';

                routingControl.on('routesfound', function (e) {
                    const route = e.routes[0];
                    if (!route) return;

                    const summary = route.summary;
                    const distanciaMetros = summary.totalDistance || 0;
                    const tiempoSegundos = summary.totalTime || 0;
                    const instrucciones = route.instructions || [];

                    const distanciaKm = (distanciaMetros / 1000).toFixed(2);
                    const horas = Math.floor(tiempoSegundos / 3600);
                    const minutos = Math.round((tiempoSegundos % 3600) / 60);
                    const tiempoTexto = horas > 0 ? horas + 'h ' + minutos + 'min' : minutos + ' min';

                    document.getElementById('ruta-distancia').textContent = distanciaKm + ' km';
                    document.getElementById('ruta-tiempo').textContent = tiempoTexto;

                    const ol = document.querySelector('#ruta-instrucciones-lista ol');
                    if (ol) {
                        ol.innerHTML = '';
                        instrucciones.forEach(function (inst) {
                            const li = document.createElement('li');
                            li.textContent = inst.text;
                            if (inst.distance) {
                                li.textContent += ' (' + (inst.distance / 1000).toFixed(2) + ' km)';
                            }
                            ol.appendChild(li);
                        });
                    }
                });

                routingControl.on('routingerror', function () {
                    document.getElementById('ruta-distancia').textContent = 'Error';
                    document.getElementById('ruta-tiempo').textContent = 'Error';
                    const ol = document.querySelector('#ruta-instrucciones-lista ol');
                    if (ol) {
                        ol.innerHTML = '<li class="text-muted">No se pudo calcular la ruta.</li>';
                    }
                });

                setTimeout(function () {
                    if (document.getElementById('ruta-distancia').textContent === 'Calculando...') {
                        document.getElementById('ruta-distancia').textContent = 'Sin respuesta';
                        document.getElementById('ruta-tiempo').textContent = 'Sin respuesta';
                    }
                }, 30000);
            } else {
                document.getElementById('ruta-distancia').textContent = '—';
                document.getElementById('ruta-tiempo').textContent = '—';
                const ol = document.querySelector('#ruta-instrucciones-lista ol');
                if (ol) {
                    if (!sucursalOrigen && !sucursalDestino) {
                        ol.innerHTML = '<li class="text-muted">Este movimiento no tiene sucursal de origen ni destino con coordenadas.</li>';
                    } else if (!sucursalOrigen) {
                        ol.innerHTML = '<li class="text-muted">La sucursal de origen no tiene coordenadas. Edítala para agregarlas.</li>';
                    } else {
                        ol.innerHTML = '<li class="text-muted">La sucursal de destino no tiene coordenadas. Edítala para agregarlas.</li>';
                    }
                }
                if (markersBounds.length > 0) {
                    map.fitBounds(markersBounds);
                }
            }
        });
    </script>
@endpush
