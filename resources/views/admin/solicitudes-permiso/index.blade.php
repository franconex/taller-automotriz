@extends('layouts.admin')

@section('title', 'Solicitudes de permiso')
@section('navbar-title', 'Solicitudes de permiso')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Solicitudes de permiso</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Solicitudes de permiso"
        description="Gestiona las solicitudes de permisos especiales.">
        @if(Auth::user()->tienePermiso('solicitudes-permiso.crear'))
            <x-slot:actions>
                <a href="{{ route('admin.solicitudes-permiso.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Realizar una solicitud de permiso
                </a>
            </x-slot:actions>
        @endif
    </x-admin.page-header>

    @if ($solicitudes->isEmpty())
        <x-admin.empty-state
            icon="bi-shield-exclamation"
            title="No hay solicitudes"
            message="Aún no se han realizado solicitudes de permiso." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de solicitudes">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        @if ($esAdmin)<th>Solicitante</th>@endif
                        <th>Permiso</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        @if ($esAdmin)<th class="col-actions">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $s)
                        <tr>
                            <td class="cell-muted">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            @if ($esAdmin)
                                <td>{{ $s->solicitante->nombre ?? '—' }}</td>
                            @endif
                            <td>
                                <div class="cell-strong">{{ $s->permiso->nombre ?? '—' }}</div>
                                <div class="cell-muted small">{{ $s->permiso->codigo ?? '' }}</div>
                            </td>
                            <td class="cell-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->motivo }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="match($s->estado) {
                                        'pendiente' => 'warning',
                                        'aprobada' => 'success',
                                        'rechazada' => 'danger',
                                        default => 'neutral',
                                    }"
                                    :label="match($s->estado) {
                                        'pendiente' => 'Pendiente',
                                        'aprobada' => 'Aprobada',
                                        'rechazada' => 'Rechazada',
                                        default => $s->estado,
                                    }" />
                            </td>
                            @if ($esAdmin)
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('admin.solicitudes-permiso.show', $s) }}"
                                           class="btn-icon"
                                           title="Ver detalle"
                                           aria-label="Ver detalle">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$solicitudes" />
        </div>
    @endif
@endsection
