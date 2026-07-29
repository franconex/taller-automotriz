@extends('layouts.admin')

@section('title', 'Cotización')
@section('navbar-title', 'Cotización #' . $autorizacione->id)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.autorizaciones.index') }}" class="text-decoration-none small">&larr; Volver</a>
    </div>

    @php
        $cliente = $autorizacione->ordenTrabajo?->cliente ?? $autorizacione->cita?->cliente;
        $vehiculo = $autorizacione->ordenTrabajo?->vehiculo ?? $autorizacione->cita?->vehiculo;
        $mecanico = $autorizacione->cita?->mecanico;
        $orden = $autorizacione->ordenTrabajo;
    @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            {{-- DATOS GENERALES --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $autorizacione->titulo }}</h5>
                    <span class="badge fs-6 bg-{{ $autorizacione->estado === 'autorizada' ? 'success' : ($autorizacione->estado === 'rechazada' || $autorizacione->estado === 'cancelada' ? 'danger' : ($autorizacione->estado === 'pendiente' ? 'warning' : 'info')) }}">
                        {{ $autorizacione->estado_label }}
                    </span>
                </div>
                <div class="card-body">
                    <p>{{ $autorizacione->descripcion }}</p>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Importe</h6>
                            <p class="fw-bold fs-5" style="color:#E31E24;">Bs {{ number_format($autorizacione->importe, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Tiempo estimado</h6>
                            <p>{{ $autorizacione->tiempo_estimado_label }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Solicitado por</h6>
                            <p>{{ $autorizacione->usuarioSolicitante?->nombre ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Fecha</h6>
                            <p>{{ $autorizacione->fecha_solicitud?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Cliente</h6>
                            <p>{{ $cliente?->nombre_completo ?? '—' }} <small class="text-muted">{{ $cliente?->telefono ?? '' }}</small></p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Vehículo</h6>
                            <p>{{ $vehiculo?->marca ?? '' }} {{ $vehiculo?->modelo ?? '' }} · {{ $vehiculo?->placa ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Mecánico</h6>
                            <p>{{ $mecanico?->empleado?->nombre_completo ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small text-uppercase">Orden</h6>
                            <p>
                                @if ($orden)
                                    <a href="{{ route('admin.ordenes.show', $orden) }}">{{ $orden->numero_orden }}</a>
                                @else
                                    <span class="text-muted">No generada</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SERVICIOS --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tools"></i> Servicios incluidos</h6>
                </div>
                <div class="card-body p-0">
                    @php $servicios = $autorizacione->servicios; @endphp
                    @if ($servicios->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light"><tr><th>Servicio</th><th class="text-end">Precio</th></tr></thead>
                                <tbody>
                                    @foreach ($servicios as $s)
                                        <tr>
                                            <td>{{ $s->nombre_servicio }}</td>
                                            <td class="text-end">Bs {{ number_format($s->precio_base, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-muted small">Sin servicios</div>
                    @endif
                </div>
            </div>

            {{-- REPUESTOS --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam"></i> Repuestos incluidos</h6>
                </div>
                <div class="card-body p-0">
                    @php $repuestos = $autorizacione->repuestos; @endphp
                    @if ($repuestos->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light"><tr><th>Repuesto</th><th class="text-center">Cant.</th><th class="text-end">P. Unit.</th><th class="text-end">Subtotal</th></tr></thead>
                                <tbody>
                                    @foreach ($repuestos as $r)
                                        <tr>
                                            <td>{{ $r->repuesto?->nombre ?? 'Repuesto #'.$r->repuesto_id }}</td>
                                            <td class="text-center">{{ $r->cantidad }}</td>
                                            <td class="text-end">Bs {{ number_format($r->precio_unitario_snapshot, 2) }}</td>
                                            <td class="text-end">Bs {{ number_format($r->cantidad * $r->precio_unitario_snapshot, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-muted small">Sin repuestos</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- TOTAL --}}
            <div class="card border-0 shadow-sm mb-3" style="position:sticky;top:20px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Resumen</h6>
                </div>
                <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Servicios:</span>
                    <span class="fw-semibold">Bs {{ number_format($servicios->sum('precio_base'), 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Repuestos:</span>
                    <span class="fw-semibold">Bs {{ number_format($repuestos->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot), 2) }}</span>
                </div>
                @if ($autorizacione->mano_de_obra)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Mano de obra:</span>
                        <span class="fw-semibold">Bs {{ number_format($autorizacione->mano_de_obra, 2) }}</span>
                    </div>
                @endif
                @if ($autorizacione->tiempo_estimado_minutos)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tiempo est.:</span>
                        <span class="fw-semibold">{{ $autorizacione->tiempo_estimado_label }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary fs-5">Bs {{ number_format($autorizacione->importe, 2) }}</span>
                </div>

                    @if ($autorizacione->comentario_cliente)
                        <hr>
                        <h6 class="text-muted small text-uppercase">Comentario del cliente</h6>
                        <div class="p-2 bg-light rounded small">
                            <p class="mb-0">{{ $autorizacione->comentario_cliente }}</p>
                        </div>
                    @endif

                    @if ($autorizacione->respondidoPor)
                        <hr>
                        <h6 class="text-muted small text-uppercase">Respondido</h6>
                        <p class="small">{{ $autorizacione->respondidoPor?->nombre }} — {{ $autorizacione->fecha_respuesta?->format('d/m/Y H:i') }}</p>
                    @endif

                    @if (! $autorizacione->esFinal())
                        <hr>
                        <form method="POST" action="{{ route('admin.autorizaciones.cancelar', $autorizacione) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('¿Cancelar esta cotización?')">
                                <i class="bi bi-x-lg"></i> Cancelar cotización
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
