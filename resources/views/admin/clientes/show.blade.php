@extends('layouts.admin')

@section('title', $cliente->nombre_completo)
@section('navbar-title', $cliente->nombre_completo)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
    <li class="active" aria-current="page">{{ $cliente->nombre_completo }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$cliente->nombre_completo"
        :description="$cliente->telefono">
        <x-slot:actions>
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f3e8ff;color:#8b5cf6;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person-vcard"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos de contacto</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Nombre completo</dt><dd>{{ $cliente->nombre_completo }}</dd>
                    <dt>Cédula de identidad</dt><dd>{{ $cliente->ci ?? '—' }}</dd>
                    <dt>Teléfono</dt><dd>{{ $cliente->telefono }}</dd>
                    <dt>Correo electrónico</dt><dd>{{ $cliente->email ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $cliente->direccion ?? '—' }}</dd>
                    <dt>Fecha de registro</dt><dd>{{ $cliente->fecha_registro?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$cliente->estado ? 'success' : 'neutral'"
                            :icon="$cliente->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$cliente->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-graph-up"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Resumen</h2>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;text-align:center;">
                            <div class="text-muted small fw-medium">Vehículos</div>
                            <div class="fw-bold" style="font-size:1.4rem;">{{ $cliente->vehiculos->count() }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;text-align:center;">
                            <div class="text-muted small fw-medium">Citas</div>
                            <div class="fw-bold" style="font-size:1.4rem;">{{ $cliente->citas->count() }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;text-align:center;">
                            <div class="text-muted small fw-medium">Órdenes</div>
                            <div class="fw-bold" style="font-size:1.4rem;">{{ $cliente->ordenesTrabajo->count() }}</div>
                        </div>
                    </div>
                </div>
                @if ($cliente->vehiculos->isNotEmpty())
                    <h3 class="fw-bold mb-2" style="font-size:0.9rem;">Vehículos registrados</h3>
                    <div class="list-group list-group-flush">
                        @foreach ($cliente->vehiculos as $vehiculo)
                            <a href="{{ route('admin.vehiculos.show', $vehiculo) }}" class="list-group-item list-group-item-action px-3 py-2 d-flex justify-content-between align-items-center" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;">
                                <span>
                                    <i class="bi bi-car-front me-1" style="color:#64748b;"></i>
                                    <strong>{{ $vehiculo->placa }}</strong>
                                    <span class="cell-secondary small">— {{ $vehiculo->modelo?->marca?->nombre ?? $vehiculo->marca ?? '' }} {{ $vehiculo->modelo?->nombre ?? $vehiculo->modelo ?? '' }}</span>
                                </span>
                                <i class="bi bi-chevron-right" style="color:#94a3b8;font-size:0.8rem;"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($cliente->direccion)
    <div class="admin-card-module mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                <i class="bi bi-geo-alt"></i>
            </span>
            <h2 class="fw-bold mb-0" style="font-size:1rem;">Ubicación</h2>
        </div>
        <div id="cliente-show-map" style="height: 300px; border-radius: 8px;"></div>
        <div id="cliente-show-map-status" class="form-text mt-2"></div>
    </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const direccion = @js($cliente->direccion);
            const nombre = @js($cliente->nombre_completo);
            const statusEl = document.getElementById('cliente-show-map-status');
            if (!direccion) return;

            const map = L.map('cliente-show-map').setView([-17.7838, -63.1823], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            if (statusEl) statusEl.textContent = 'Buscando dirección…';

            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(direccion) + '&limit=1&accept-language=es')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.length > 0) {
                        const r = data[0];
                        const lat = parseFloat(r.lat);
                        const lng = parseFloat(r.lon);
                        map.setView([lat, lng], 16);
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup('<strong>' + nombre + '</strong><br>' + direccion)
                            .openPopup();
                        if (statusEl) statusEl.textContent = 'Ubicación aproximada: ' + r.display_name;
                    } else {
                        if (statusEl) statusEl.textContent = 'No se pudo determinar la ubicación en el mapa.';
                    }
                })
                .catch(function () {
                    if (statusEl) statusEl.textContent = 'Error al buscar la ubicación.';
                });
        });
    </script>
@endpush