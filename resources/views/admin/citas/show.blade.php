@extends('layouts.admin')

@section('title', 'Cita #' . $cita->id)
@section('navbar-title', 'Cita #' . $cita->id)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.citas.index') }}">Citas</a></li>
    <li class="active" aria-current="page">#{{ $cita->id }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Cita #' . $cita->id"
        :description="$cita->fecha?->format('d/m/Y') . ' a las ' . $cita->hora">
        <x-slot:actions>
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver al calendario
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <h2 class="h6 fw-bold mb-3">Datos de la cita</h2>
        <dl class="admin-meta">
            <dt>Cliente</dt>
            <dd>
                @if ($cita->cliente)
                    <a href="{{ route('admin.clientes.show', $cita->cliente) }}">{{ $cita->cliente->nombre_completo }}</a>
                @else — @endif
            </dd>
            <dt>Vehículo</dt>
            <dd>
                @if ($cita->vehiculo)
                    <a href="{{ route('admin.vehiculos.show', $cita->vehiculo) }}">{{ $cita->vehiculo->placa }}</a>
                @else — @endif
            </dd>
            <dt>Sucursal</dt><dd>{{ $cita->sucursal->nombre ?? '—' }}</dd>
            <dt>Fecha</dt><dd>{{ $cita->fecha?->format('d/m/Y') ?? '—' }}</dd>
            <dt>Hora</dt><dd>{{ $cita->hora }}@if ($cita->hora_fin) - {{ $cita->hora_fin }}@endif</dd>
            <dt>Tipo</dt><dd>{{ ucfirst($cita->tipo) }}</dd>
            <dt>Estado</dt>
            <dd>
                <x-admin.status-badge
                    :tone="match($cita->estado) {
                        'confirmada' => 'info',
                        'atendida' => 'success',
                        'cancelada' => 'danger',
                        'no_asistio' => 'danger',
                        default => 'warning',
                    }"
                    :icon="match($cita->estado) {
                        'confirmada' => 'bi-check2-circle',
                        'atendida' => 'bi-check-circle-fill',
                        'cancelada' => 'bi-x-circle-fill',
                        'no_asistio' => 'bi-person-x',
                        default => 'bi-hourglass-split',
                    }"
                    :label="ucfirst(str_replace('_',' ',$cita->estado))" />
            </dd>
            <dt>Deja vehículo</dt><dd>{{ $cita->deja_vehiculo ? 'Sí' : 'No' }}</dd>
            <dt>Costo consulta</dt><dd>{{ number_format((float) $cita->costo_consulta, 2, ',', '.') }}</dd>
        </dl>

        <h2 class="h6 fw-bold mt-4 mb-2">Detalle</h2>
        <p class="cell-strong">Descripción del problema</p>
        <p class="cell-muted small mb-3">{{ $cita->descripcion_problema }}</p>

        @if ($cita->observaciones)
            <p class="cell-strong">Observaciones</p>
            <p class="cell-muted small mb-3">{{ $cita->observaciones }}</p>
        @endif

        @if ($cita->motivo_reprogramacion)
            <hr>
            <h3 class="h6 fw-bold mb-2">Reprogramación</h3>
            <p class="cell-muted small">Motivo: {{ $cita->motivo_reprogramacion }}</p>
        @endif

        @if ($cita->cancelado_motivo)
            <hr>
            <h3 class="h6 fw-bold mb-2">Cancelación</h3>
            <p class="cell-muted small">Motivo: {{ $cita->cancelado_motivo }}</p>
        @endif

        @if ($cita->ordenTrabajo)
            <hr>
            <h3 class="h6 fw-bold mb-2">Orden de trabajo asociada</h3>
            <a href="{{ route('admin.ordenes.show', $cita->ordenTrabajo) }}" class="btn btn-outline-secondary btn-sm">
                Ver orden {{ $cita->ordenTrabajo->numero_orden }}
            </a>
        @endif
    </div>
@endsection
