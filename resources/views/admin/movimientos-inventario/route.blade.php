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

        $origenData = $origen ? [
            'id' => $origen->id,
            'nombre' => $origen->nombre,
            'lat' => (float) $origen->latitud,
            'lng' => (float) $origen->longitud,
        ] : null;

        $destinoData = $destino && $destino->id !== ($origen->id ?? null) ? [
            'id' => $destino->id,
            'nombre' => $destino->nombre,
            'lat' => (float) $destino->latitud,
            'lng' => (float) $destino->longitud,
        ] : null;

        $sucursalesData = $sucursales->map(fn ($s) => [
            'id' => $s->id,
            'nombre' => $s->nombre,
            'direccion' => $s->direccion,
            'lat' => (float) $s->latitud,
            'lng' => (float) $s->longitud,
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

    <div class="admin-table-wrap p-0 overflow-hidden">
        <div id="route-map"></div>
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
            const map = L.map('route-map').setView([-17.78629, -63.18117], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const sucursalOrigen = @json($origenData);
            const sucursalDestino = @json($destinoData);

            const todasLasSucursales = @json($sucursalesData);

            const blueIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            });

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

            todasLasSucursales.forEach(function (s) {
                if (!s.lat || !s.lng) return;
                let icon = blueIcon;
                if (sucursalOrigen && s.id === sucursalOrigen.id) icon = greenIcon;
                if (sucursalDestino && s.id === sucursalDestino.id) icon = redIcon;
                L.marker([s.lat, s.lng], { icon })
                    .addTo(map)
                    .bindPopup(`<strong>${s.nombre}</strong><br>${s.direccion || ''}`);
            });

            if (sucursalOrigen && sucursalDestino) {
                L.Routing.control({
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
            } else if (sucursalOrigen) {
                map.setView([sucursalOrigen.lat, sucursalOrigen.lng], 15);
            }
        });
    </script>
@endpush
