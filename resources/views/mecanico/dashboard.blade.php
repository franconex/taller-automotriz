@extends('layouts.mecanico')

@section('title', 'Mi Panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Bienvenido, {{ Auth::user()->nombre }}</h4>
            <p class="text-muted mb-0">Panel de trabajo del mecánico</p>
        </div>
    </div>

    {{-- TARJETAS DE RESUMEN --}}
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #0d6efd !important;">
                <div class="h5 mb-0 fw-bold">{{ $programadas }}</div>
                <small class="text-muted">Programadas</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #0dcaf0 !important;">
                <div class="h5 mb-0 fw-bold">{{ $recibidas }}</div>
                <small class="text-muted">Recibidas</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #ffc107 !important;">
                <div class="h5 mb-0 fw-bold">{{ $enDiagnostico }}</div>
                <small class="text-muted">En diagnóstico</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #fd7e14 !important;">
                <div class="h5 mb-0 fw-bold">{{ $enProceso }}</div>
                <small class="text-muted">En proceso</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #6f42c1 !important;">
                <div class="h5 mb-0 fw-bold">{{ $esperandoRepuesto }}</div>
                <small class="text-muted">Esp. repuesto</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #dc3545 !important;">
                <div class="h5 mb-0 fw-bold">{{ $pendienteAutorizacion }}</div>
                <small class="text-muted">Pend. autorización</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm text-center p-2 h-100" style="border-left:3px solid #198754 !important;">
                <div class="h5 mb-0 fw-bold">{{ $finalizados }}</div>
                <small class="text-muted">Finalizados</small>
            </div>
        </div>
    </div>

    {{-- MIS TRABAJOS ACTUALES --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Mis trabajos actuales</h6>
        </div>
        <div class="card-body p-0">
            @if ($trabajosActuales->isEmpty())
                <div class="p-4 text-center text-muted small">No tienes trabajos asignados en este momento.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Orden</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Placa</th>
                                <th>Ingreso</th>
                                <th>Estado</th>
                                <th>Tiempo est.</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajosActuales as $a)
                                @php $o = $a->ordenTrabajo; $e = $o->estimaciones->first(); @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                                    <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                                    <td class="small">{{ $o->fecha_emision?->format('d/m H:i') }}</td>
                                    <td>
                                        @php
                                            $colores = ['programada'=>'secondary','recibida'=>'info','en_diagnostico'=>'warning','en_proceso'=>'primary','esperando_repuesto'=>'purple','pausada'=>'dark','pendiente_autorizacion'=>'danger'];
                                        @endphp
                                        <span class="badge bg-{{ $colores[$o->estado] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $o->estado)) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $e ? $e->duracion_minima_minutos . '-' . $e->duracion_maxima_minutos . 'min' : '—' }}</td>
                                    <td>
                                        <a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- TRABAJOS TERMINADOS --}}
    @if ($terminados->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Trabajos terminados</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Orden</th><th>Cliente</th><th>Vehículo</th><th>Fin</th><th>Estado</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($terminados as $a)
                                @php $o = $a->ordenTrabajo; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $o->numero_orden }}</td>
                                    <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                                    <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                                    <td class="small">{{ $a->fecha_finalizacion?->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge bg-success">{{ $o->estado === 'entregada' ? 'Entregada' : 'Listo' }}</span></td>
                                    <td><a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
