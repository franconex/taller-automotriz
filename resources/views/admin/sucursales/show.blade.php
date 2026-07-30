@extends('layouts.admin')

@section('title', $sucursal->nombre)
@section('navbar-title', $sucursal->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.sucursales.index') }}">Sucursales</a></li>
    <li class="active" aria-current="page">{{ $sucursal->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$sucursal->nombre"
        :description="$sucursal->direccion">
        <x-slot:actions>
            <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-building"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos generales</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Nombre</dt><dd>{{ $sucursal->nombre }}</dd>
                    <dt>Dirección</dt><dd>{{ $sucursal->direccion }}</dd>
                    <dt>Teléfono</dt><dd>{{ $sucursal->telefono }}</dd>
                    <dt>Horario</dt>
                    <dd>
                        @php
                            $h = $sucursal->horario_atencion;
                            $isArr = is_array($h);
                        @endphp
                        @if ($isArr)
                            <div style="font-size:0.9rem;">
                                <div><strong>Lun-Vie:</strong> {{ $h['weekday']['open'] ?? '—' }} — {{ $h['weekday']['close'] ?? '—' }}</div>
                                <div><strong>Sáb:</strong> {{ $h['saturday']['open'] ?? '—' }} — {{ $h['saturday']['close'] ?? '—' }}</div>
                                <div><strong>Dom:</strong> Cerrado</div>
                            </div>
                        @else
                            {{ $h ?? '—' }}
                        @endif
                    </dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$sucursal->estado ? 'success' : 'neutral'"
                            :icon="$sucursal->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$sucursal->estado ? 'Activa' : 'Inactiva'" />
                    </dd>
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="admin-card-module d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-graph-up"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Resumen</h2>
                </div>
                <div class="row g-3 mt-0">
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <div class="text-muted small fw-medium">Empleados</div>
                            <div class="fw-bold" style="font-size:1.5rem;">{{ $sucursal->empleados->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <div class="text-muted small fw-medium">Repuestos</div>
                            <div class="fw-bold" style="font-size:1.5rem;">{{ $sucursal->inventarios->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($sucursal->latitud && $sucursal->longitud)
    <div class="admin-card-module mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                <i class="bi bi-geo-alt"></i>
            </span>
            <h2 class="fw-bold mb-0" style="font-size:1rem;">Ubicación</h2>
        </div>
        <div id="sucursal-show-map" style="height: 350px; border-radius: 8px;"></div>
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
            const lat = {{ $sucursal->latitud ?? 'null' }};
            const lng = {{ $sucursal->longitud ?? 'null' }};
            if (lat !== null && lng !== null) {
                const map = L.map('sucursal-show-map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<strong>{{ $sucursal->nombre }}</strong><br>{{ $sucursal->direccion }}');
            }
        });
    </script>
@endpush