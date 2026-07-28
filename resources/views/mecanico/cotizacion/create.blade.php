@extends('layouts.admin')

@section('title', 'Cotización')
@section('navbar-title', 'Cotización')

@section('content')

<div class="row">
    {{-- COLUMNA IZQUIERDA: datos + formularios --}}
    <div class="col-lg-8">
        {{-- DATOS DEL CLIENTE --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person"></i> Cotización para {{ $cita->cliente?->nombre_completo ?? '—' }}</h6>
            </div>
            <div class="card-body">
                <div class="row small">
                    <div class="col-md-6"><strong>Vehículo:</strong> {{ $cita->vehiculo?->marca ?? '' }} {{ $cita->vehiculo?->modelo ?? '' }} · {{ $cita->vehiculo?->placa ?? '—' }}</div>
                    <div class="col-md-6"><strong>Teléfono:</strong> {{ $cita->cliente?->telefono ?? '—' }}</div>
                    <div class="col-md-6"><strong>Servicio solicitado:</strong> {{ $cita->servicio?->nombre ?? $cita->tipo ?? '—' }}</div>
                    <div class="col-md-6"><strong>Problema:</strong> {{ $cita->descripcion_problema ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- SERVICIOS --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <strong class="small"><i class="bi bi-tools"></i> Servicios</strong>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formServicio')"><i class="bi bi-plus"></i></button>
            </div>
            <div class="card-body p-3">
                @if ($servicios->isNotEmpty())
                    <ul class="list-unstyled small mb-2">
                        @foreach ($servicios as $s)
                            <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span>{{ $s->nombre_servicio }}</span>
                                <span><span class="text-muted me-2">{{ $s->tiempo_estimado_minutos ?? '?' }}min</span><span class="fw-semibold">Bs {{ number_format($s->precio_base, 2) }}</span></span>
                            </li>
                        @endforeach
                        <li class="d-flex justify-content-between py-1 fw-bold text-primary">
                            <span>Total servicios</span>
                            <span>{{ $servicios->sum('tiempo_estimado_minutos') }}min · Bs {{ number_format($totalServicios, 2) }}</span>
                        </li>
                    </ul>
                @else
                    <div class="small text-muted mb-2">Sin servicios agregados</div>
                @endif
                <form method="POST" action="{{ route('mecanico.cotizacion.servicios', $autorizacion) }}" id="formServicio" style="display:none;" class="mt-2">
                    @csrf
                    <select name="servicio_id" class="form-select form-select-sm mb-1" required>
                        <option value="">Seleccionar servicio</option>
                        @foreach (\App\Models\Servicio::where('estado', true)->get() as $s)
                            @php $dur = $s->duracion_estimada_minutos; @endphp
                            <option value="{{ $s->id }}" data-precio="{{ $s->precio_base ?? 0 }}" data-tiempo="{{ $dur ?? 0 }}">
                                {{ $s->nombre }} — Bs {{ number_format($s->precio_base ?? 0, 2) }} · {{ $dur ? $dur . 'min' : '?' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-success">Agregar</button>
                </form>
            </div>
        </div>

        {{-- REPUESTOS --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <strong class="small"><i class="bi bi-box-seam"></i> Repuestos</strong>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formRepuesto')"><i class="bi bi-plus"></i></button>
            </div>
            <div class="card-body p-3">
                @if ($repuestos->isNotEmpty())
                    <ul class="list-unstyled small mb-2">
                        @foreach ($repuestos as $r)
                            <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span>{{ $r->repuesto?->nombre ?? 'Repuesto #'.$r->repuesto_id }} x{{ $r->cantidad }}</span>
                                <span class="fw-semibold">Bs {{ number_format($r->cantidad * $r->precio_unitario_snapshot, 2) }}</span>
                            </li>
                        @endforeach
                        <li class="d-flex justify-content-between py-1 fw-bold text-primary">
                            <span>Total repuestos</span>
                            <span>Bs {{ number_format($totalRepuestos, 2) }}</span>
                        </li>
                    </ul>
                @else
                    <div class="small text-muted mb-2">Sin repuestos agregados</div>
                @endif
                <form method="POST" action="{{ route('mecanico.cotizacion.repuestos', $autorizacion) }}" id="formRepuesto" style="display:none;" class="mt-2">
                    @csrf
                    <select name="repuesto_id" class="form-select form-select-sm mb-1" required>
                        <option value="">Seleccionar repuesto</option>
                        @foreach (\App\Models\Repuesto::where('estado', true)->get() as $r)
                            <option value="{{ $r->id }}" data-precio="{{ $r->precio_venta ?? 0 }}">{{ $r->nombre }} ({{ $r->codigo }}) — Bs {{ number_format($r->precio_venta ?? 0, 2) }}</option>
                        @endforeach
                    </select>
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text">Cant.</span>
                        <input type="number" name="cantidad" class="form-control" min="0.01" step="0.01" value="1" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success">Agregar</button>
                </form>
            </div>
        </div>

        {{-- ENVIAR --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                @php $tieneItems = $servicios->isNotEmpty() || $repuestos->isNotEmpty(); @endphp
                <form method="POST" action="{{ route('mecanico.cotizacion.enviar', $autorizacion) }}" id="formEnviar">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Título de la cotización</label>
                        <input name="titulo" class="form-control form-control-sm" value="{{ $autorizacion->titulo }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Descripción / Detalle para el cliente</label>
                        <textarea name="descripcion" class="form-control form-control-sm" rows="3">{{ $autorizacion->descripcion }}</textarea>
                    </div>

                    {{-- TIEMPO ESTIMADO --}}
                    <div class="row g-2 mb-2">
                        <div class="col-5">
                            <label class="form-label small">Tiempo estimado</label>
                            <input type="number" name="tiempo_estimado_valor" class="form-control form-control-sm" min="0.1" step="0.1" value="{{ old('tiempo_estimado_valor', $tiempoValor) }}" id="inputTiempoValor">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">&nbsp;</label>
                            <select name="tiempo_estimado_unidad" class="form-select form-select-sm" id="selectTiempoUnidad">
                                <option value="minutos" {{ $tiempoUnidad === 'minutos' ? 'selected' : '' }}>Minutos</option>
                                <option value="horas" {{ $tiempoUnidad === 'horas' ? 'selected' : '' }}>Horas</option>
                                <option value="dias" {{ $tiempoUnidad === 'dias' ? 'selected' : '' }}>Días</option>
                            </select>
                        </div>
                        <div class="col-3 d-flex align-items-end pb-1">
                            @php
                                $totalMin = $minutosDesdeServicios ?? 0;
                                $preview = '';
                                if ($totalMin >= 1440) $preview = round($totalMin / 1440, 1) . ' día(s)';
                                elseif ($totalMin >= 60) $preview = round($totalMin / 60, 1) . ' hora(s)';
                                elseif ($totalMin > 0) $preview = $totalMin . ' min';
                            @endphp
                            <small class="text-muted">
                                @if ($autoCalculado && $servicios->isNotEmpty())
                                    <i class="bi bi-arrow-repeat"></i> {{ $preview }} (según servicios)
                                @elseif ($autorizacion->tiempo_estimado_minutos)
                                    ≈ {{ $autorizacion->tiempo_estimado_label }}
                                @endif
                                &nbsp;
                                <a href="#" onclick="document.getElementById('inputTiempoValor').focus(); return false;" class="small">Ajustar</a>
                            </small>
                        </div>
                    </div>

                    {{-- MANO DE OBRA --}}
                    <div class="mb-2">
                        <label class="form-label small">Mano de obra <small class="text-muted">(opcional)</small></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bs</span>
                            <input type="number" name="mano_de_obra" class="form-control" min="0" step="0.01" value="{{ old('mano_de_obra', $autorizacion->mano_de_obra ?? 0) }}" placeholder="0.00" id="inputManoObra">
                        </div>
                    </div>

                    @if ($tieneItems)
                        <button type="submit" class="btn btn-primary w-100" id="btnEnviar">
                            <i class="bi bi-send"></i> Enviar cotización al cliente (Bs <span id="totalPreview">{{ number_format($totalServicios + $totalRepuestos + ($autorizacion->mano_de_obra ?? 0), 2) }}</span>)
                        </button>
                    @else
                        <div class="alert alert-info mb-0 small text-center">
                            <i class="bi bi-info-circle"></i> Agregá servicios o repuestos para poder enviar la cotización.
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- COLUMNA DERECHA: Resumen --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="position:sticky;top:20px;">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Resumen</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2 small">
                    <span>Servicios <small class="text-muted">({{ $servicios->sum('tiempo_estimado_minutos') }}min)</small>:</span>
                    <span class="fw-semibold">Bs {{ number_format($totalServicios, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span>Repuestos:</span>
                    <span class="fw-semibold">Bs {{ number_format($totalRepuestos, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small" id="resumenManoObra" @if (!($autorizacion->mano_de_obra ?? 0)) style="display:none;" @endif>
                    <span>Mano de obra:</span>
                    <span class="fw-semibold" id="resumenManoObraValor">Bs {{ number_format($autorizacion->mano_de_obra ?? 0, 2) }}</span>
                </div>
                @if ($autorizacion->tiempo_estimado_minutos)
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Tiempo estimado:</span>
                        <span class="fw-semibold">{{ $autorizacion->tiempo_estimado_label }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary" style="font-size:1.2rem;" id="resumenTotal">
                        Bs {{ number_format($totalServicios + $totalRepuestos + ($autorizacion->mano_de_obra ?? 0), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
}

// Actualizar total en tiempo real
document.getElementById('inputManoObra')?.addEventListener('input', function() {
    const serv = {{ $totalServicios }};
    const rep  = {{ $totalRepuestos }};
    const mo   = parseFloat(this.value) || 0;
    const total = serv + rep + mo;
    document.getElementById('totalPreview').textContent = total.toFixed(2);
    document.getElementById('resumenTotal').textContent = 'Bs ' + total.toFixed(2);

    const row = document.getElementById('resumenManoObra');
    if (mo > 0) {
        row.style.display = '';
        document.getElementById('resumenManoObraValor').textContent = 'Bs ' + mo.toFixed(2);
    } else {
        row.style.display = 'none';
    }
});
</script>

@endsection
