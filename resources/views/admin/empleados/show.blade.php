@extends('layouts.admin')

@section('title', $empleado->nombre_completo)
@section('navbar-title', $empleado->nombre_completo)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.empleados.index') }}">Empleados</a></li>
    <li class="active" aria-current="page">{{ $empleado->nombre_completo }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$empleado->nombre_completo"
        :description="($empleado->rol->nombre ?? 'Sin rol') . ' — ' . ($empleado->sucursal->nombre ?? 'Sin sucursal')">
        <x-slot:actions>
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (Auth::user()->tienePermiso('empleados.editar'))
            <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos personales</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Nombre completo</dt><dd>{{ $empleado->nombre_completo }}</dd>
                    <dt>Cédula de identidad</dt><dd>{{ $empleado->ci }}</dd>
                    <dt>Teléfono</dt><dd>{{ $empleado->telefono }}</dd>
                    <dt>Correo electrónico</dt><dd>{{ $empleado->email ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $empleado->direccion ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-briefcase"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos laborales</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Rol</dt>
                    <dd>
                        <x-admin.status-badge
                            tone="info"
                            icon="bi-shield-lock"
                            :label="$empleado->rol->nombre ?? '—'" />
                    </dd>
                    @if ($empleado->cargo)
                        <dt>Cargo</dt><dd>{{ $empleado->cargo }}</dd>
                    @endif
                    <dt>Sucursal</dt><dd>{{ $empleado->sucursal->nombre ?? '—' }}</dd>
                    <dt>Fecha contratación</dt><dd>{{ $empleado->fecha_contratacion?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$empleado->estado ? 'success' : 'neutral'"
                            :icon="$empleado->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                            :label="$empleado->estado ? 'Activo' : 'Inactivo'" />
                    </dd>
                    <dt>Cuenta de acceso</dt>
                    <dd>
                        @if ($empleado->user)
                            <span class="cell-strong">{{ $empleado->user->username }}</span>
                            <span class="d-block cell-muted small">{{ $empleado->user->rol->nombre ?? '—' }}</span>
                        @else
                            <span class="cell-muted">Sin cuenta</span>
                        @endif
                    </dd>
                </dl>
            </div>

            @if ($empleado->mecanico)
            <div class="admin-card-module mt-3" style="border-left:4px solid #f59e0b;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-wrench-adjustable-circle"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos de mecánico</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Especialidad</dt><dd>{{ $empleado->mecanico->especialidad->nombre ?? '—' }}</dd>
                    <dt>Disponibilidad</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="$empleado->mecanico->disponibilidad === 'disponible' ? 'success' : ($empleado->mecanico->disponibilidad === 'ocupado' ? 'warning' : 'neutral')"
                            :label="ucfirst($empleado->mecanico->disponibilidad)" />
                    </dd>
                    @if ($empleado->mecanico->observaciones)
                        <dt>Observaciones</dt><dd>{{ $empleado->mecanico->observaciones }}</dd>
                    @endif
                </dl>
            </div>
            @endif
        </div>
    </div>

    @if ($empleado->direccion)
    <div class="admin-card-module mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                <i class="bi bi-geo-alt"></i>
            </span>
            <h2 class="fw-bold mb-0" style="font-size:1rem;">Ubicación</h2>
        </div>
        <div id="empleado-show-map" style="height: 300px; border-radius: 8px;"></div>
        <div id="empleado-show-map-status" class="form-text mt-2"></div>
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
            const direccion = @js($empleado->direccion);
            const nombre = @js($empleado->nombre_completo);
            const statusEl = document.getElementById('empleado-show-map-status');
            if (!direccion) return;

            const map = L.map('empleado-show-map').setView([-17.7838, -63.1823], 13);
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