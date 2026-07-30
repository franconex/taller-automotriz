<div class="row g-4">

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-vcard" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Cliente y vehículo</h3>
            </div>

            <div class="mb-3">
                <label for="field-cliente_id" class="form-label fw-medium">Cliente <span class="required">*</span></label>
                <select name="cliente_id" id="field-cliente_id" required class="form-select{{ $errors->has('cliente_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un cliente —</option>
                    @foreach (($clientes ?? collect()) as $c)
                        <option value="{{ $c->id }}" @selected(old('cliente_id', $orden->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
                    @endforeach
                </select>
                @if ($errors->has('cliente_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('cliente_id') }}</div>
                @endif
            </div>

            <div class="mb-3">
                <label for="field-vehiculo_id" class="form-label fw-medium">Vehículo <span class="required">*</span></label>
                <select name="vehiculo_id" id="field-vehiculo_id" required class="form-select{{ $errors->has('vehiculo_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un vehículo —</option>
                    @foreach (($vehiculos ?? collect()) as $v)
                        <option value="{{ $v->id }}" @selected(old('vehiculo_id', $orden->vehiculo_id ?? null) == $v->id)>{{ $v->placa }} — {{ $v->cliente->nombre_completo ?? '' }}</option>
                    @endforeach
                </select>
                @if ($errors->has('vehiculo_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('vehiculo_id') }}</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-sucursal_id" class="form-label fw-medium">Sucursal <span class="required">*</span></label>
                <select name="sucursal_id" id="field-sucursal_id" required class="form-select{{ $errors->has('sucursal_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona una sucursal —</option>
                    @foreach (($sucursales ?? collect()) as $s)
                        <option value="{{ $s->id }}" @selected(old('sucursal_id', $orden->sucursal_id ?? null) == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
                @if ($errors->has('sucursal_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('sucursal_id') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-wrench" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Problema y tiempo</h3>
            </div>

            <div class="mb-3">
                <label for="field-descripcion_problema" class="form-label fw-medium">
                    Descripción del problema <span class="required">*</span>
                </label>
                <textarea id="field-descripcion_problema" name="descripcion_problema" rows="4" required
                          class="form-control{{ $errors->has('descripcion_problema') ? ' is-invalid' : '' }}"
                          placeholder="Describe el problema reportado por el cliente...">{{ old('descripcion_problema', $orden->descripcion_problema ?? '') }}</textarea>
                @if ($errors->has('descripcion_problema'))
                    <div class="invalid-feedback d-block">{{ $errors->first('descripcion_problema') }}</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-tiempo_estimado_horas" class="form-label fw-medium">Tiempo estimado (horas)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-clock" style="color:#64748b;"></i>
                    </span>
                    <input id="field-tiempo_estimado_horas" type="number" name="tiempo_estimado_horas"
                           value="{{ old('tiempo_estimado_horas', $orden->tiempo_estimado_horas ?? '') }}"
                           class="form-control{{ $errors->has('tiempo_estimado_horas') ? ' is-invalid' : '' }}"
                           placeholder="2.5" step="0.5" min="0" max="999.9" style="border-left:0;">
                </div>
                @if ($errors->has('tiempo_estimado_horas'))
                    <div class="invalid-feedback d-block">{{ $errors->first('tiempo_estimado_horas') }}</div>
                @else
                    <div class="form-text">Tiempo estimado para completar la reparación.</div>
                @endif
            </div>
        </div>
    </div>

    @if (isset($tiposServicio) && $tiposServicio->isNotEmpty())
    <div class="col-12">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-tools" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Servicios a realizar</h3>
            </div>
            <div class="row g-2">
                @foreach ($tiposServicio as $tipo)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border" style="border-radius:8px;overflow:hidden;">
                            <div class="card-header py-2 px-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                                <strong class="small">{{ $tipo->nombre }}</strong>
                            </div>
                            <div class="card-body py-2 px-3" style="max-height:180px;overflow-y:auto;">
                                @forelse ($tipo->servicios as $servicio)
                                    <div class="form-check py-1">
                                        <input class="form-check-input servicio-checkbox" type="checkbox"
                                            name="servicios_ids[]" value="{{ $servicio->id }}"
                                            id="servicio_{{ $servicio->id }}">
                                        <label class="form-check-label small" for="servicio_{{ $servicio->id }}">
                                            {{ $servicio->nombre }}
                                            <span class="text-muted">(Bs. {{ number_format((float) $servicio->precio_base, 2, ',', '.') }})</span>
                                        </label>
                                    </div>
                                @empty
                                    <span class="text-muted small">Sin servicios disponibles</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="form-text mt-2">Selecciona los servicios que se realizarán. Los montos se agregarán automáticamente al total.</div>
        </div>
    </div>
    @endif

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fffbeb;color:#d97706;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-gear" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Asignación de mecánico</h3>
            </div>

            <div class="mb-0">
                <label for="field-mecanico_id" class="form-label fw-medium">Mecánico asignado <span class="required">*</span></label>
                <select name="mecanico_id" id="field-mecanico_id" required class="form-select{{ $errors->has('mecanico_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un mecánico disponible —</option>
                    @foreach (($mecanicos ?? collect()) as $m)
                        @php
                            $asignado = isset($orden) && $orden->asignaciones ? $orden->asignaciones->where('mecanico_id', $m->id)->first() : null;
                        @endphp
                        <option value="{{ $m->id }}" @selected(old('mecanico_id', optional($asignado)->mecanico_id) == $m->id)>
                            {{ $m->empleado->nombre_completo ?? 'Mecánico #' . $m->id }}
                            @if ($m->disponibilidad === 'ocupado' && !$asignado) (ocupado) @endif
                            @if ($asignado) (asignado actualmente) @endif
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('mecanico_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('mecanico_id') }}</div>
                @else
                    <div class="form-text">Al seleccionar un mecánico, su estado cambiará a "ocupado".</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-sliders" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Ajustes y estado</h3>
            </div>

            <div class="mb-3">
                <label for="field-descuento" class="form-label fw-medium">Descuento</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-percent" style="color:#64748b;"></i>
                    </span>
                    <input id="field-descuento" type="number" name="descuento"
                           value="{{ old('descuento', $orden->descuento ?? 0) }}"
                           class="form-control{{ $errors->has('descuento') ? ' is-invalid' : '' }}"
                           step="0.01" min="0" style="border-left:0;">
                </div>
                @if ($errors->has('descuento'))
                    <div class="invalid-feedback d-block">{{ $errors->first('descuento') }}</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-estado" class="form-label fw-medium">Estado</label>
                <select name="estado" id="field-estado" class="form-select{{ $errors->has('estado') ? ' is-invalid' : '' }}">
                    <option value="recibida" @selected(old('estado', $orden->estado ?? 'recibida') === 'recibida')>Recibida</option>
                    <option value="diagnostico" @selected(old('estado', $orden->estado ?? null) === 'diagnostico')>En diagnóstico</option>
                    <option value="en_proceso" @selected(old('estado', $orden->estado ?? null) === 'en_proceso')>En proceso</option>
                    <option value="finalizada" @selected(old('estado', $orden->estado ?? null) === 'finalizada')>Finalizada</option>
                    <option value="entregada" @selected(old('estado', $orden->estado ?? null) === 'entregada')>Entregada</option>
                    <option value="anulada" @selected(old('estado', $orden->estado ?? null) === 'anulada')>Anulada</option>
                </select>
                @if ($errors->has('estado'))
                    <div class="invalid-feedback d-block">{{ $errors->first('estado') }}</div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sucursalSelect = document.getElementById('field-sucursal_id');
    var mecanicoSelect = document.getElementById('field-mecanico_id');

    if (!sucursalSelect || !mecanicoSelect) return;

    function cargarMecanicos(sucursalId) {
        mecanicoSelect.innerHTML = '<option value="">Cargando mecánicos...</option>';
        mecanicoSelect.disabled = true;

        var url = '{{ route("admin.mecanicos.por-sucursal") }}?sucursal_id=' + (sucursalId || '');
        @if (isset($orden) && $orden->id)
            url += '&excepto_orden_id={{ $orden->id }}';
        @endif

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                mecanicoSelect.innerHTML = '<option value="">— Selecciona un mecánico disponible —</option>';
                data.forEach(function (m) {
                    var opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre + (m.disponibilidad === 'disponible' ? '' : ' (ocupado)');
                    mecanicoSelect.appendChild(opt);
                });
                mecanicoSelect.disabled = false;
            })
            .catch(function () {
                mecanicoSelect.innerHTML = '<option value="">Error al cargar mecánicos</option>';
                mecanicoSelect.disabled = false;
            });
    }

    sucursalSelect.addEventListener('change', function () {
        cargarMecanicos(this.value);
    });

    if (sucursalSelect.value) {
        cargarMecanicos(sucursalSelect.value);
    }
});
</script>
@endpush