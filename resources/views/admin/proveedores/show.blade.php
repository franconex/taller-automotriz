@extends('layouts.admin')

@section('title', $proveedor->nombre_empresa)
@section('navbar-title', $proveedor->nombre_empresa)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.proveedores.index') }}">Proveedores</a></li>
    <li class="active" aria-current="page">{{ $proveedor->nombre_empresa }}</li>
@endsection

@section('content')
    <x-admin.page-header :title="$proveedor->nombre_empresa" :description="$proveedor->contacto ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.proveedores.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
            @if (Auth::user()->tienePermiso('proveedores.editar'))
            <a href="{{ route('admin.proveedores.edit', $proveedor) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Editar</a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-building"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Empresa</dt><dd>{{ $proveedor->nombre_empresa }}</dd>
                    <dt>Contacto</dt><dd>{{ $proveedor->contacto ?? '—' }}</dd>
                    <dt>Teléfono</dt><dd>{{ $proveedor->telefono }}</dd>
                    <dt>Email</dt><dd>{{ $proveedor->email ?? '—' }}</dd>
                    <dt>NIT</dt><dd>{{ $proveedor->nit ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $proveedor->direccion ?? '—' }}</dd>
                </dl>
            </div>
            @if ($proveedor->direccion)
            <div class="admin-card-module mt-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-geo-alt"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Ubicación</h2>
                </div>
                <div id="proveedor-show-map" style="height: 250px; border-radius: 8px;"></div>
                <div id="proveedor-show-map-status" class="form-text mt-2"></div>
            </div>
            @endif
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;"><i class="bi bi-box-seam"></i></span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Repuestos</h2>
                </div>
                @if ($proveedor->repuestos->isEmpty())
                    <p class="cell-secondary small mb-0">Aún no hay repuestos asociados a este proveedor.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($proveedor->repuestos as $repuesto)
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;">
                                <span>
                                    <i class="bi bi-box-seam me-1" style="color:#64748b;"></i>
                                    <strong>{{ $repuesto->nombre }}</strong>
                                    <span class="cell-secondary small">({{ $repuesto->codigo }})</span>
                                </span>
                                <span class="badge" style="background:#e2e8f0;color:#475569;">Bs. {{ number_format((float) $repuesto->precio_compra, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const direccion = @js($proveedor->direccion);
            const nombre = @js($proveedor->nombre_empresa);
            const statusEl = document.getElementById('proveedor-show-map-status');
            if (!direccion) return;
            const map = L.map('proveedor-show-map').setView([-17.7838, -63.1823], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19}).addTo(map);
            if (statusEl) statusEl.textContent = 'Buscando dirección…';
            fetch('https://nominatim.openstreetmap.org/search?format=json&q='+encodeURIComponent(direccion)+'&limit=1&accept-language=es')
                .then(r=>r.json()).then(data=>{
                    if (data&&data.length>0) {
                        const r=data[0]; const lat=parseFloat(r.lat), lng=parseFloat(r.lon);
                        map.setView([lat,lng],16);
                        L.marker([lat,lng]).addTo(map).bindPopup('<strong>'+nombre+'</strong><br>'+direccion).openPopup();
                        if (statusEl) statusEl.textContent = 'Ubicación aproximada: '+r.display_name;
                    } else { if (statusEl) statusEl.textContent = 'No se pudo determinar la ubicación.'; }
                }).catch(()=>{ if (statusEl) statusEl.textContent = 'Error al buscar.'; });
        });
    </script>
@endpush