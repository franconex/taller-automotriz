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
                                    <option value="finalizada_mecanico" {{ in_array($orden->estado, ['diagnostico','en_proceso','esperando_repuesto','pausada']) ? '' : 'disabled' }}>✅ Finalizado</option>
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
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control search-select-input" placeholder="Buscar servicio…" data-target="selectServicio">
                        </div>
                        <select name="servicio_id" id="selectServicio" class="form-select form-select-sm mb-1" required size="5">
                            <option value="">— Seleccionar servicio —</option>
                            @foreach (\App\Models\Servicio::where('estado', true)->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-plus-lg"></i> Agregar servicio</button>
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
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control search-select-input" placeholder="Buscar repuesto…" data-target="selectRepuesto">
                        </div>
                        <select name="repuesto_id" id="selectRepuesto" class="form-select form-select-sm mb-1" required size="5">
                            <option value="">— Seleccionar repuesto —</option>
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


        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleForm(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
}
document.querySelectorAll('.search-select-input').forEach(input => {
    input.addEventListener('keyup', function() {
        const targetId = this.dataset.target;
        const select = document.getElementById(targetId);
        if (!select) return;
        const q = this.value.toLowerCase();
        Array.from(select.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = opt.text.toLowerCase().includes(q) ? '' : 'none';
        });
    });
});
</script>
@endpush
