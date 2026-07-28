@extends('layouts.admin')

@section('title', 'Orden #' . $orden->numero_orden)
@section('navbar-title', 'Orden #' . $orden->numero_orden)

@section('content')
    <div class="mb-3">
        <a href="{{ route('mecanico.ordenes.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Volver a mis órdenes
        </a>
    </div>

    {{-- INFO GENERAL --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">{{ $orden->numero_orden }}</h5>
                    <div class="small text-muted">
                        {{ $orden->descripcion_problema ?? 'Sin descripción' }}
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @php
                        $colores = ['programada'=>'secondary','recibida'=>'info','diagnostico'=>'warning','en_proceso'=>'primary','esperando_repuesto'=>'secondary','pausada'=>'dark','pendiente_autorizacion'=>'danger','finalizada_mecanico'=>'success','lista_entrega'=>'success','entregada'=>'secondary','anulada'=>'danger'];
                    @endphp
                    <a href="{{ route('mecanico.ordenes.cotizar-nueva', $orden) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-cash-coin"></i> Nueva cotización
                    </a>
                    <span class="badge fs-6 px-3 py-1 bg-{{ $colores[$orden->estado] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                    </span>
                </div>
            </div>
            <hr class="my-2">
            <div class="row g-2 small">
                <div class="col-md-3"><span class="text-muted">Cliente:</span> <strong>{{ $orden->cliente?->nombre_completo ?? '—' }}</strong></div>
                <div class="col-md-3"><span class="text-muted">Vehículo:</span> <strong>{{ $orden->vehiculo?->marca ?? '' }} {{ $orden->vehiculo?->modelo ?? '' }} ({{ $orden->vehiculo?->placa ?? '—' }})</strong></div>
                <div class="col-md-3"><span class="text-muted">Ingreso:</span> <strong>{{ $orden->fecha_emision?->format('d/m/Y H:i') }}</strong></div>
                <div class="col-md-3"><span class="text-muted">Sucursal:</span> <strong>{{ $orden->sucursal?->nombre ?? '—' }}</strong></div>
            </div>
            @if ($asignacion)
                <div class="mt-2 small">
                    <span class="text-muted">Avance:</span>
                    <div class="progress" style="height:8px;max-width:300px;">
                        <div class="progress-bar bg-success" style="width:{{ $asignacion->porcentaje_avance ?? 0 }}%;" role="progressbar"></div>
                    </div>
                    <strong>{{ $asignacion->porcentaje_avance ?? 0 }}%</strong>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-lg-7">
            {{-- DIAGNÓSTICO --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <strong class="small"><i class="bi bi-search"></i> Diagnóstico</strong>
                    @if (!$diagnostico)
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if ($diagnostico)
                        <div class="small">
                            <div class="mb-1"><span class="text-muted">Problema:</span> {{ $diagnostico->problema_encontrado ?? '—' }}</div>
                            <div class="mb-1"><span class="text-muted">Causa:</span> {{ $diagnostico->causa_probable ?? '—' }}</div>
                            @if ($diagnostico->recomendacion)<div class="mb-1"><span class="text-muted">Recomendación:</span> {{ $diagnostico->recomendacion }}</div>@endif
                            @if ($diagnostico->observacion_cliente)
                                <div class="mt-2 p-2 rounded" style="background:#fef2f2;">
                                    <small><i class="bi bi-chat-quote text-danger"></i> {{ $diagnostico->observacion_cliente }}</small>
                                </div>
                            @endif
                        </div>
                    @elseif (in_array($orden->estado, ['en_proceso', 'esperando_repuesto', 'pausada', 'pendiente_autorizacion', 'finalizada_mecanico', 'lista_entrega']))
                        <div class="small text-muted">
                            <i class="bi bi-info-circle"></i> El diagnóstico se realizó en la cotización.
                        </div>
                    @endif
                    @if (!$diagnostico && in_array($orden->estado, ['recibida']))
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="toggleForm('formDiagnostico')">
                            <i class="bi bi-pencil"></i> Registrar diagnóstico
                        </button>
                        <form method="POST" action="{{ route('mecanico.ordenes.diagnostico', $orden) }}" id="formDiagnostico" style="display:none;" class="mt-2">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small">Problema encontrado</label>
                                <textarea name="problema_encontrado" class="form-control form-control-sm" rows="2">{{ old('problema_encontrado') }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Causa probable</label>
                                <input name="causa_probable" class="form-control form-control-sm" value="{{ old('causa_probable') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Recomendación</label>
                                <textarea name="recomendacion" class="form-control form-control-sm" rows="2">{{ old('recomendacion') }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Observación para el cliente</label>
                                <textarea name="observacion_cliente" class="form-control form-control-sm" rows="2" placeholder="Esto lo vera el cliente...">{{ old('observacion_cliente') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Guardar diagnóstico</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- AVANCE Y PROGRESO --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3">
                    <strong class="small"><i class="bi bi-graph-up-arrow"></i> Reportar avance</strong>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('mecanico.ordenes.avances', $orden) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Título del avance <span class="text-danger">*</span></label>
                                <input name="titulo" class="form-control form-control-sm" required placeholder="Ej: Iniciando reparación">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">% de avance</label>
                                <input type="number" name="porcentaje" class="form-control form-control-sm" min="0" max="100" value="{{ $asignacion->porcentaje_avance ?? 0 }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="visible_cliente" id="avance_visible" value="1" checked>
                                    <label class="form-check-label small" for="avance_visible">Visible al cliente</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Descripción</label>
                                <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Describe lo que se hizo..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Nota para el cliente</label>
                                <textarea name="nota_cliente" class="form-control form-control-sm" rows="1" placeholder="Mensaje que vera el cliente..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2"></i> Registrar avance</button>
                            </div>
                        </div>
                    </form>

                    @if ($avances->isNotEmpty())
                        <hr>
                        <strong class="small">Línea de tiempo de avances</strong>
                        <div class="mt-2" style="max-height:250px;overflow-y:auto;">
                            @foreach ($avances as $a)
                                <div class="d-flex gap-2 mb-2 p-2 rounded" style="background:#f8fafc;">
                                    <div class="text-center" style="min-width:36px;">
                                        <div class="fw-bold small text-primary">{{ $a->porcentaje ?? '?' }}%</div>
                                    </div>
                                    <div class="small flex-fill">
                                        <strong>{{ $a->titulo }}</strong>
                                        @if ($a->descripcion)<div class="text-muted">{{ $a->descripcion }}</div>@endif
                                        @if ($a->nota_cliente && $a->visible_cliente)
                                            <div class="mt-1 p-1 rounded" style="background:#eef2ff;font-size:.8rem;">
                                                <i class="bi bi-chat-quote text-primary"></i> {{ $a->nota_cliente }}
                                            </div>
                                        @endif
                                        <div style="font-size:.7rem;color:#64748b;margin-top:2px;">
                                            {{ $a->created_at->format('d/m H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- NOTAS --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3">
                    <strong class="small"><i class="bi bi-chat-dots"></i> Notas del taller</strong>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('mecanico.ordenes.estado', $orden) }}" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <div class="row g-1">
                            <div class="col-auto">
                                <select name="estado" class="form-select form-select-sm">
                                    <option value="">Cambiar estado...</option>
                                    <option value="diagnostico" {{ $orden->estado === 'recibida' ? '' : 'disabled' }}>🔍 A diagnóstico</option>
                                    <option value="en_proceso" {{ in_array($orden->estado, ['diagnostico']) ? '' : 'disabled' }}>⚙️ En proceso</option>
                                    <option value="esperando_repuesto" {{ in_array($orden->estado, ['diagnostico','en_proceso']) ? '' : 'disabled' }}>⏳ Esperando repuesto</option>
                                    <option value="pausada" {{ in_array($orden->estado, ['diagnostico','en_proceso']) ? '' : 'disabled' }}>⏸️ Pausar</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Cambiar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-lg-5">
            {{-- TIEMPO ESTIMADO --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <strong class="small"><i class="bi bi-clock"></i> Tiempo estimado</strong>
                </div>
                <div class="card-body p-3">
                    @if ($estimacion)
                        <div class="small mb-2">
                            <div><span class="text-muted">Duración:</span>
                                <strong>{{ $estimacion->duracion_minima_minutos }} - {{ $estimacion->duracion_maxima_minutos }} min</strong>
                            </div>
                            @if ($estimacion->observacion_cliente)
                                <div><span class="text-muted">Nota al cliente:</span> {{ $estimacion->observacion_cliente }}</div>
                            @endif
                        </div>
                    @endif
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formTiempo')">
                        <i class="bi bi-clock"></i> {{ $estimacion ? 'Actualizar estimación' : 'Estimar tiempo' }}
                    </button>
                    <form method="POST" action="{{ route('mecanico.ordenes.tiempo', $orden) }}" id="formTiempo" style="display:none;" class="mt-2">
                        @csrf
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Mínimo (min) <span class="text-danger">*</span></label>
                                <input type="number" name="tiempo_minimo_minutos" class="form-control form-control-sm" min="1" value="{{ old('tiempo_minimo_minutos', $estimacion->duracion_minima_minutos ?? '') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Máximo (min) <span class="text-danger">*</span></label>
                                <input type="number" name="tiempo_maximo_minutos" class="form-control form-control-sm" min="1" value="{{ old('tiempo_maximo_minutos', $estimacion->duracion_maxima_minutos ?? '') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Motivo (opcional)</label>
                                <input name="motivo" class="form-control form-control-sm" placeholder="Ej: Espera de repuesto" value="{{ old('motivo', $estimacion->motivo ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Nota para el cliente</label>
                                <textarea name="nota_cliente" class="form-control form-control-sm" rows="2" placeholder="Lo que vera el cliente...">{{ old('nota_cliente', $estimacion->observacion_cliente ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SERVICIOS --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <strong class="small"><i class="bi bi-tools"></i> Servicios</strong>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formServicio')"><i class="bi bi-plus"></i></button>
                </div>
                <div class="card-body p-3">
                    @if ($servicios->isNotEmpty())
                        <ul class="list-unstyled small mb-2">
                            @foreach ($servicios as $s)
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span>{{ $s->nombre_servicio }} @if($s->nombre_subservicio)/ {{ $s->nombre_subservicio }}@endif</span>
                                    <span class="text-muted">{{ $s->tiempo_estimado_minutos }}min</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="small text-muted">Sin servicios registrados</div>
                    @endif
                    <form method="POST" action="{{ route('mecanico.ordenes.servicios', $orden) }}" id="formServicio" style="display:none;" class="mt-2">
                        @csrf
                        <select name="servicio_id" class="form-select form-select-sm mb-1" required>
                            <option value="">Seleccionar servicio</option>
                            @foreach (\App\Models\Servicio::where('estado', true)->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Agregar</button>
                    </form>
                </div>
            </div>

            {{-- REPUESTOS --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <strong class="small"><i class="bi bi-box-seam"></i> Repuestos solicitados</strong>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formRepuesto')"><i class="bi bi-plus"></i></button>
                </div>
                <div class="card-body p-3">
                    @if ($repuestos->isNotEmpty())
                        <ul class="list-unstyled small mb-2">
                            @foreach ($repuestos as $r)
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span>{{ $r->repuesto?->nombre ?? 'Repuesto #'.$r->repuesto_id }}</span>
                                    <span>x{{ $r->cantidad }} <span class="badge bg-warning text-dark">{{ $r->estado }}</span></span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="small text-muted">Sin repuestos solicitados</div>
                    @endif
                    <form method="POST" action="{{ route('mecanico.ordenes.repuestos', $orden) }}" id="formRepuesto" style="display:none;" class="mt-2">
                        @csrf
                        <select name="repuesto_id" class="form-select form-select-sm mb-1" required>
                            <option value="">Seleccionar repuesto</option>
                            @foreach (\App\Models\Repuesto::where('estado', true)->get() as $r)
                                <option value="{{ $r->id }}">{{ $r->nombre }} ({{ $r->codigo }})</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">Cant.</span>
                            <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                        </div>
                        <input name="motivo" class="form-control form-control-sm mb-1" placeholder="Motivo">
                        <button type="submit" class="btn btn-sm btn-success">Agregar</button>
                    </form>
                </div>
            </div>

            {{-- COTIZACIÓN --}}
            @php
                $totalServicios = $orden->serviciosMecanico()->sum('precio_base');
                $totalRepuestos = $orden->repuestosMecanico()->sum(DB::raw('cantidad * precio_unitario_snapshot'));
                $totalCotizacion = $totalServicios + $totalRepuestos;
                $tieneServicios = $servicios->isNotEmpty();
                $tieneRepuestos = $repuestos->isNotEmpty();
                $estadoPermiteCotizar = in_array($orden->estado, ['programada', 'recibida', 'diagnostico', 'en_proceso', 'esperando_repuesto']);
            @endphp

            @if ($estadoPermiteCotizar)
                <div class="card border-0 shadow-sm mb-3" style="border-radius:10px; border-left:3px solid #0d6efd !important;">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small"><i class="bi bi-calculator"></i> Cotización</strong>
                    </div>
                    <div class="card-body p-3">
                        @if ($tieneServicios || $tieneRepuestos)
                            <div class="mb-2 small">
                                <div class="d-flex justify-content-between">
                                    <span>Servicios:</span>
                                    <span class="fw-semibold">${{ number_format($totalServicios, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Repuestos:</span>
                                    <span class="fw-semibold">${{ number_format($totalRepuestos, 2) }}</span>
                                </div>
                                <hr class="my-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total estimado:</span>
                                    <span class="fw-bold text-primary">${{ number_format($totalCotizacion, 2) }}</span>
                                </div>
                            </div>

                            <button class="btn btn-sm btn-outline-primary w-100" onclick="toggleForm('formCotizacion')">
                                <i class="bi bi-send"></i> Enviar cotización al cliente
                            </button>

                            <form method="POST" action="{{ route('mecanico.ordenes.cotizacion', $orden) }}" id="formCotizacion" style="display:none;" class="mt-2">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Título de la cotización</label>
                                    <input name="titulo" class="form-control form-control-sm" value="Reparación {{ $orden->vehiculo?->placa ?? '' }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Descripción / Detalle</label>
                                    <textarea name="descripcion" class="form-control form-control-sm" rows="3" required>Se requiere la siguiente reparación:
- Servicios: {{ $servicios->count() }} items
- Repuestos: {{ $repuestos->count() }} items
- Tiempo estimado: {{ $estimacion ? $estimacion->duracion_minima_minutos . 'min' : 'Pendiente' }}

Total: ${{ number_format($totalCotizacion, 2) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-check2"></i> Generar y enviar
                                </button>
                            </form>
                        @else
                            <div class="small text-muted text-center">
                                <i class="bi bi-info-circle"></i> Agrega servicios y repuestos para generar la cotización.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- EVIDENCIAS --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <strong class="small"><i class="bi bi-camera"></i> Evidencias</strong>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleForm('formEvidencia')"><i class="bi bi-upload"></i></button>
                </div>
                <div class="card-body p-3">
                    @if ($evidencias->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach ($evidencias as $e)
                                <a href="{{ Storage::url($e->archivo) }}" target="_blank" class="d-inline-block">
                                    <img src="{{ Storage::url($e->archivo) }}" style="width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="small text-muted">Sin evidencias</div>
                    @endif
                    <form method="POST" action="{{ route('mecanico.ordenes.evidencias', $orden) }}" id="formEvidencia" enctype="multipart/form-data" style="display:none;" class="mt-2">
                        @csrf
                        <input type="file" name="archivo" class="form-control form-control-sm mb-1" accept="image/*" required>
                        <input name="descripcion" class="form-control form-control-sm mb-1" placeholder="Descripción (opcional)">
                        <button type="submit" class="btn btn-sm btn-success">Subir</button>
                    </form>
                </div>
            </div>

            {{-- FINALIZAR --}}
            @if (in_array($orden->estado, ['en_proceso', 'diagnostico', 'esperando_repuesto', 'pausada']))
                <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;border-left:3px solid #198754 !important;">
                    <div class="card-body p-3 text-center">
                        <p class="small mb-2">¿Has terminado el trabajo en este vehículo?</p>
                        <form method="POST" action="{{ route('mecanico.ordenes.finalizar', $orden) }}" onsubmit="return confirm('¿Finalizar trabajo? Se marcará como listo para entrega.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-circle"></i> Finalizar trabajo
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleForm(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
        }
    </script>
@endpush
