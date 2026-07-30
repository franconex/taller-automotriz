@extends('layouts.admin')

@section('title', $mecanico->empleado->nombre_completo ?? 'Mecánico')
@section('navbar-title', $mecanico->empleado->nombre_completo ?? 'Mecánico')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.mecanicos.index') }}">Mecánicos</a></li>
    <li class="active" aria-current="page">{{ $mecanico->empleado->nombre_completo ?? '—' }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$mecanico->empleado->nombre_completo ?? 'Mecánico'"
        :description="$mecanico->especialidad->nombre ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.mecanicos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (Auth::user()->tienePermiso('mecanicos.editar'))
            <a href="{{ route('admin.mecanicos.edit', $mecanico) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-person"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos personales</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Empleado</dt>
                    <dd>
                        @if ($mecanico->empleado)
                            <a href="{{ route('admin.empleados.show', $mecanico->empleado) }}" class="text-decoration-none fw-semibold">{{ $mecanico->empleado->nombre_completo }}</a>
                        @else — @endif
                    </dd>
                    <dt>Rol</dt><dd>{{ $mecanico->empleado->rol->nombre ?? '—' }}</dd>
                    @if ($mecanico->empleado?->cargo)
                        <dt>Cargo</dt><dd>{{ $mecanico->empleado->cargo }}</dd>
                    @endif
                    <dt>Sucursal</dt><dd>{{ $mecanico->empleado->sucursal->nombre ?? '—' }}</dd>
                    <dt>Teléfono</dt><dd>{{ $mecanico->empleado->telefono ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-card-module">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;">
                        <i class="bi bi-tools"></i>
                    </span>
                    <h2 class="fw-bold mb-0" style="font-size:1rem;">Datos técnicos</h2>
                </div>
                <dl class="admin-meta">
                    <dt>Especialidad</dt><dd>{{ $mecanico->especialidad->nombre ?? '—' }}</dd>
                    <dt>Disponibilidad</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($mecanico->disponibilidad) {
                                'disponible' => 'success',
                                'ocupado' => 'warning',
                                'ausente' => 'danger',
                                default => 'neutral',
                            }"
                            :icon="match($mecanico->disponibilidad) {
                                'disponible' => 'bi-check-circle-fill',
                                'ocupado' => 'bi-hourglass-split',
                                'ausente' => 'bi-x-circle-fill',
                                default => 'bi-circle',
                            }"
                            :label="ucfirst($mecanico->disponibilidad)" />
                    </dd>
                    <dt>Asignaciones activas</dt><dd>{{ $mecanico->asignaciones->where('estado', '!=', 'finalizada')->count() }}</dd>
                </dl>
                @if ($mecanico->observaciones)
                    <div class="mt-3 pt-3 border-top">
                        <div class="fw-semibold mb-1" style="font-size:0.85rem;">Observaciones</div>
                        <p class="cell-secondary small mb-0">{{ $mecanico->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection