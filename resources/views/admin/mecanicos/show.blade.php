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
            <a href="{{ route('admin.mecanicos.edit', $mecanico) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos personales</h2>
                <dl class="admin-meta">
                    <dt>Empleado</dt>
                    <dd>
                        @if ($mecanico->empleado)
                            <a href="{{ route('admin.empleados.show', $mecanico->empleado) }}">{{ $mecanico->empleado->nombre_completo }}</a>
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
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos técnicos</h2>
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
                    <h3 class="h6 fw-bold mt-3 mb-2">Observaciones</h3>
                    <p class="cell-muted small mb-0">{{ $mecanico->observaciones }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
