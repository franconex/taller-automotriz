@extends('layouts.admin')

@section('title', 'Vacaciones')
@section('navbar-title', 'Vacaciones')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Vacaciones</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Vacaciones"
        description="Solicita y gestiona períodos de vacaciones del personal.">
        @php
            $puedeSolicitar = Auth::user()->tieneRol('Gerente')
                || Auth::user()->tieneRol('Recepcionista')
                || Auth::user()->tieneRol('Mecánico');
        @endphp
        @if($puedeSolicitar)
            <x-slot:actions>
                <a href="{{ route('admin.vacaciones.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Solicitar vacaciones
                </a>
            </x-slot:actions>
        @endif
    </x-admin.page-header>

    @if ($vacaciones->isEmpty())
        <x-admin.empty-state
            icon="bi-sun"
            title="No hay solicitudes de vacaciones"
            message="Aún no se han registrado solicitudes de vacaciones." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de vacaciones">
                <thead>
                    <tr>
                        <th>Fecha solicitud</th>
                        @if ($esAdmin)<th>Solicitante</th>@endif
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Días</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        @if ($esAdmin)<th class="col-actions">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vacaciones as $v)
                        <tr>
                            <td class="cell-muted">{{ $v->created_at->format('d/m/Y') }}</td>
                            @if ($esAdmin)
                                <td>{{ $v->solicitante->nombre ?? '—' }}</td>
                            @endif
                            <td>{{ $v->fecha_inicio->format('d/m/Y') }}</td>
                            <td>{{ $v->fecha_fin->format('d/m/Y') }}</td>
                            <td>{{ $v->fecha_inicio->diffInDays($v->fecha_fin) + 1 }}</td>
                            <td class="cell-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $v->motivo }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($v->estado) {
                                        'pendiente' => 'warning',
                                        'aprobada' => 'success',
                                        'rechazada' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :label="match($v->estado) {
                                        'pendiente' => 'Pendiente',
                                        'aprobada' => 'Aprobada',
                                        'rechazada' => 'Rechazada',
                                        default => $v->estado,
                                    }" />
                            </td>
                            @if ($esAdmin)
                                <td>
                                    <div class="row-actions">
                                        @if ($v->estado === 'pendiente')
                                            <form method="POST" action="{{ route('admin.vacaciones.aprobar', $v) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-icon text-success" title="Aprobar" aria-label="Aprobar">
                                                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn-icon text-danger" title="Rechazar" aria-label="Rechazar"
                                                    data-bs-toggle="modal" data-bs-target="#rechazarModal{{ $v->id }}">
                                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                                            </button>
                                        @elseif ($v->estado === 'aprobada' && $v->solicitante->estaEnVacaciones())
                                            <form method="POST" action="{{ route('admin.vacaciones.finalizar', $v) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-icon text-primary" title="Finalizar vacaciones" aria-label="Finalizar">
                                                    <i class="bi bi-arrow-return-left" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$vacaciones" />
        </div>
    @endif

    @foreach ($vacaciones as $v)
        @if ($esAdmin && $v->estado === 'pendiente')
            <div class="modal fade" id="rechazarModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.vacaciones.rechazar', $v) }}">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">Rechazar vacaciones</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="respuesta-{{ $v->id }}" class="form-label">Motivo del rechazo</label>
                                    <textarea name="respuesta_admin" id="respuesta-{{ $v->id }}" class="form-control" rows="3" required maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Rechazar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
