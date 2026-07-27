@extends('layouts.admin')

@section('title', 'Solicitud #' . $solicitud->id)
@section('navbar-title', 'Solicitud #' . $solicitud->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-permiso.index') }}">Solicitudes de permiso</a></li>
    <li class="active" aria-current="page">#{{ $solicitud->id }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Solicitud #' . $solicitud->id"
        :description="$solicitud->created_at->format('d/m/Y H:i')">
        <x-slot:actions>
            <a href="{{ route('admin.solicitudes-permiso.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la solicitud</h2>
                <dl class="admin-meta">
                    <dt>Solicitante</dt>
                    <dd>{{ $solicitud->solicitante->nombre ?? '—' }}</dd>
                    <dt>Rol</dt>
                    <dd>{{ $solicitud->solicitante->rol->nombre ?? '—' }}</dd>
                    <dt>Permiso solicitado</dt>
                    <dd>
                        <strong>{{ $solicitud->permiso->nombre ?? '—' }}</strong>
                        <br><small class="text-muted">{{ $solicitud->permiso->codigo ?? '' }}</small>
                    </dd>
                    <dt>Motivo</dt>
                    <dd>{{ $solicitud->motivo }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($solicitud->estado) {
                                'pendiente' => 'warning',
                                'aprobada' => 'success',
                                'rechazada' => 'danger',
                                default => 'neutral',
                            }"
                            :label="match($solicitud->estado) {
                                'pendiente' => 'Pendiente',
                                'aprobada' => 'Aprobada',
                                'rechazada' => 'Rechazada',
                                default => $solicitud->estado,
                            }" />
                    </dd>
                    @if ($solicitud->estado === 'aprobada')
                        <dt>Permiso asignado</dt>
                        <dd>
                            @if ($yaTienePermiso)
                                <span class="text-success"><i class="bi bi-check2" aria-hidden="true"></i> Sí — el permiso ya está activo en el rol</span>
                            @else
                                <span class="text-warning"><i class="bi bi-hourglass" aria-hidden="true"></i> Pendiente de asignación</span>
                            @endif
                        </dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            @if ($solicitud->estado === 'pendiente' && Auth::user()->tienePermiso('solicitudes-permiso.aprobar'))
                <div class="admin-table-wrap p-4 mb-3">
                    <h2 class="h6 fw-bold mb-3">Responder solicitud</h2>
                    <form method="POST" action="{{ route('admin.solicitudes-permiso.aprobar', $solicitud) }}" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="respuesta_aprobar" class="form-label">Mensaje (opcional)</label>
                            <textarea name="respuesta_admin" id="respuesta_aprobar" class="form-control" rows="2" maxlength="500" placeholder="Ej: Permiso concedido."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Aprobar
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.solicitudes-permiso.rechazar', $solicitud) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="respuesta_rechazar" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                            <textarea name="respuesta_admin" id="respuesta_rechazar" class="form-control" rows="2" maxlength="500" placeholder="Ej: No tienes permisos suficientes para esta función." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            Rechazar
                        </button>
                    </form>
                </div>
            @endif

            @if ($solicitud->estado !== 'pendiente')
                <div class="admin-table-wrap p-4">
                    <h2 class="h6 fw-bold mb-3">Respuesta del administrador</h2>
                    <dl class="admin-meta">
                        <dt>Revisado por</dt>
                        <dd>{{ $solicitud->admin->nombre ?? '—' }}</dd>
                        <dt>Fecha de respuesta</dt>
                        <dd>{{ $solicitud->fecha_respuesta?->format('d/m/Y H:i') ?? '—' }}</dd>
                        @if ($solicitud->respuesta_admin)
                            <dt>Mensaje</dt>
                            <dd>{{ $solicitud->respuesta_admin }}</dd>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>
@endsection
