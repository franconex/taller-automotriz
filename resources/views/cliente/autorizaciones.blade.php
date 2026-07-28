@extends('layouts.cliente-sidebar')

@section('title', 'Autorizaciones')
@section('navbar-title', 'Autorizaciones')

@section('content')
    @if ($autorizaciones->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-file-check display-4 d-block mb-3"></i>
            <p>No tienes solicitudes de autorización pendientes.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($autorizaciones as $a)
                @php
                    $vehiculo = $a->ordenTrabajo?->vehiculo ?? $a->cita?->vehiculo;
                    $t = $a->tiempo_estimado_minutos;
                @endphp
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $a->titulo }}</h6>
                                <small class="text-muted">{{ $vehiculo?->placa ?? '—' }}</small>
                            </div>
                            <span class="badge fs-6 bg-{{ $a->estado === 'autorizada' ? 'success' : ($a->estado === 'rechazada' || $a->estado === 'cancelada' ? 'danger' : ($a->estado === 'pendiente' ? 'warning' : 'info')) }}">
                                {{ $a->estado_label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <p>{{ $a->descripcion }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Importe:</strong> <span style="color:#E31E24;">Bs {{ number_format($a->importe, 2) }}</span>
                                    @if ($t)
                                        &middot;
                                        <strong>Tiempo est.:</strong> {{ $a->tiempo_estimado_label }}
                                    @endif
                                    &middot;
                                    <small class="text-muted">{{ $a->fecha_solicitud?->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>

                            @if ($a->comentario_cliente)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small class="text-muted">Tu comentario:</small>
                                    <p class="mb-0 small">{{ $a->comentario_cliente }}</p>
                                </div>
                            @endif

                            @if ($a->esRespondible())
                                <hr>
                                <form method="POST" action="{{ route('cliente.autorizaciones.responder', $a) }}" class="row g-2">
                                    @csrf @method('PATCH')
                                    <div class="col-12">
                                        <textarea name="comentario_cliente" class="form-control form-control-sm" rows="2" placeholder="Comentario (opcional)">{{ old('comentario_cliente') }}</textarea>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="accion" value="autorizada" class="btn btn-sm text-white" style="background:#E31E24;">
                                            <i class="bi bi-check-lg me-1"></i>Autorizar
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="accion" value="requiere_informacion" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-question-lg me-1"></i>Más información
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="accion" value="rechazada" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Rechazar este trabajo adicional?')">
                                            <i class="bi bi-x-lg me-1"></i>Rechazar
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
