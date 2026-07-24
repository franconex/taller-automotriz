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
                Volver
            </a>
            <a href="{{ route('admin.citas.edit', $cita) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la cita</h2>
                <dl class="admin-meta">
                    <dt>Cliente</dt>
                    <dd><a href="{{ route('admin.clientes.show', $cita->cliente) }}">{{ $cita->cliente->nombre_completo ?? '—' }}</a></dd>
                    <dt>Vehículo</dt>
                    <dd>
                        @if ($cita->vehiculo)
                            <a href="{{ route('admin.vehiculos.show', $cita->vehiculo) }}">{{ $cita->vehiculo->placa }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dt>Sucursal</dt><dd>{{ $cita->sucursal->nombre ?? '—' }}</dd>
                    <dt>Fecha</dt><dd>{{ $cita->fecha?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Hora</dt><dd>{{ $cita->hora }}</dd>
                    <dt>Tipo</dt><dd>{{ ucfirst($cita->tipo) }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($cita->estado) {
                                'confirmada' => 'info',
                                'atendida' => 'success',
                                'cancelada' => 'danger',
                                default => 'warning',
                            }"
                            :icon="match($cita->estado) {
                                'confirmada' => 'bi-check2-circle',
                                'atendida' => 'bi-check-circle-fill',
                                'cancelada' => 'bi-x-circle-fill',
                                default => 'bi-hourglass-split',
                            }"
                            :label="ucfirst($cita->estado)" />
                    </dd>
                    <dt>Deja vehículo</dt><dd>{{ $cita->deja_vehiculo ? 'Sí' : 'No' }}</dd>
                    <dt>Costo consulta</dt><dd>{{ number_format((float) $cita->costo_consulta, 2, ',', '.') }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Detalle</h2>
                <p class="cell-strong">Descripción del problema</p>
                <p class="cell-muted small mb-3">{{ $cita->descripcion_problema }}</p>

                @if ($cita->observaciones)
                    <p class="cell-strong">Observaciones</p>
                    <p class="cell-muted small mb-3">{{ $cita->observaciones }}</p>
                @endif

                @if ($cita->ordenTrabajo)
                    <hr>
                    <h3 class="h6 fw-bold mb-2">Orden de trabajo asociada</h3>
                    <a href="{{ route('admin.ordenes.show', $cita->ordenTrabajo) }}" class="btn btn-outline-secondary btn-sm">
                        Ver orden {{ $cita->ordenTrabajo->numero_orden }}
                    </a>
                @else
                    <hr>
                    <h3 class="h6 fw-bold mb-2">Acciones</h3>
                    <form method="POST" action="{{ route('admin.citas.convertir-orden', $cita) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm"
                                data-tp-confirm
                                data-tp-confirm-title="¿Convertir cita en orden?"
                                data-tp-confirm-message="Se creará una orden de trabajo a partir de esta cita."
                                data-tp-confirm-text="Convertir"
                                data-tp-confirm-icon="info">
                            <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                            Convertir a orden
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
