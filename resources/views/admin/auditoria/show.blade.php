@extends('layouts.admin')

@section('title', 'Detalle de auditoría')
@section('navbar-title', 'Detalle de auditoría')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.auditoria.index') }}">Auditoría</a></li>
    <li class="active" aria-current="page">#{{ $registro->id }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Auditoría #' . $registro->id"
        :description="$registro->fecha_accion?->format('d/m/Y H:i:s')">
        <x-slot:actions>
            <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos</h2>
                <dl class="admin-meta">
                    <dt>Fecha</dt><dd>{{ $registro->fecha_accion?->format('d/m/Y H:i:s') ?? '—' }}</dd>
                    <dt>Usuario</dt><dd>{{ $registro->usuario->nombre ?? '—' }}</dd>
                    <dt>Acción</dt><dd>{{ ucfirst($registro->accion) }}</dd>
                    <dt>Módulo</dt><dd>{{ ucfirst($registro->modulo) }}</dd>
                    <dt>Entidad</dt><dd>{{ $registro->entidad_tipo }} #{{ $registro->entidad_id ?? '—' }}</dd>
                    <dt>IP</dt><dd>{{ $registro->ip_address ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Cambios</h2>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <p class="cell-muted small fw-bold text-uppercase mb-2" style="letter-spacing:.6px;">Datos anteriores</p>
                        @if ($registro->datos_anteriores)
                            <pre class="admin-code">{{ json_encode($registro->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="cell-muted small">Sin datos anteriores.</p>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="cell-muted small fw-bold text-uppercase mb-2" style="letter-spacing:.6px;">Datos nuevos</p>
                        @if ($registro->datos_nuevos)
                            <pre class="admin-code">{{ json_encode($registro->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="cell-muted small">Sin datos nuevos.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
