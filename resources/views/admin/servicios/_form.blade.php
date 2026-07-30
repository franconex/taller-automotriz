<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-gear" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>
            <div class="mb-3">
                <label for="field-tipo_servicio_id" class="form-label fw-medium">Tipo de servicio <span class="required">*</span></label>
                <select name="tipo_servicio_id" id="field-tipo_servicio_id" required
                        class="form-select{{ $errors->has('tipo_servicio_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un tipo —</option>
                    @foreach (($tipos ?? collect()) as $t)
                        <option value="{{ $t->id }}" @selected(old('tipo_servicio_id', $servicio->tipo_servicio_id ?? null) == $t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @if ($errors->has('tipo_servicio_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('tipo_servicio_id') }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="field-nombre" class="form-label fw-medium">Nombre <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-gear" style="color:#64748b;"></i>
                    </span>
                    <input id="field-nombre" type="text" name="nombre"
                           value="{{ old('nombre', $servicio->nombre ?? '') }}" required
                           class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Cambio de aceite" style="border-left:0;"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');">
                </div>
                @if ($errors->has('nombre'))
                    <div class="invalid-feedback d-block">{{ $errors->first('nombre') }}</div>
                @else
                    <div class="form-text">Solo letras y espacios.</div>
                @endif
            </div>
            <div class="mb-0">
                <label for="field-descripcion" class="form-label fw-medium">Descripción</label>
                <textarea id="field-descripcion" name="descripcion" rows="3"
                          class="form-control{{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
                          placeholder="Describe el servicio...">{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>
                @if ($errors->has('descripcion'))
                    <div class="invalid-feedback d-block">{{ $errors->first('descripcion') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-currency-dollar" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Precio y duración</h3>
            </div>
            <div class="mb-3">
                <label for="field-precio_base" class="form-label fw-medium">Precio base <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-currency-dollar" style="color:#64748b;"></i>
                    </span>
                    <input id="field-precio_base" type="number" name="precio_base"
                           value="{{ old('precio_base', $servicio->precio_base ?? '') }}" required
                           class="form-control{{ $errors->has('precio_base') ? ' is-invalid' : '' }}"
                           placeholder="0.00" step="0.01" min="0" style="border-left:0;"
                           oninput="if(this.value < 0) this.value = 0;">
                </div>
                @if ($errors->has('precio_base'))
                    <div class="invalid-feedback d-block">{{ $errors->first('precio_base') }}</div>
                @endif
            </div>
            <div class="mb-0">
                <label for="field-duracion_estimada_minutos" class="form-label fw-medium">Duración (minutos)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-clock" style="color:#64748b;"></i>
                    </span>
                    <input id="field-duracion_estimada_minutos" type="number" name="duracion_estimada_minutos"
                           value="{{ old('duracion_estimada_minutos', $servicio->duracion_estimada_minutos ?? '') }}"
                           class="form-control{{ $errors->has('duracion_estimada_minutos') ? ' is-invalid' : '' }}"
                           placeholder="60" min="0" style="border-left:0;"
                           oninput="if(this.value < 0) this.value = 0;">
                </div>
                @if ($errors->has('duracion_estimada_minutos'))
                    <div class="invalid-feedback d-block">{{ $errors->first('duracion_estimada_minutos') }}</div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-top">
                <div class="form-check form-switch">
                    <input type="hidden" name="estado" value="0">
                    <input class="form-check-input" type="checkbox" id="servicioEstado" name="estado" value="1"
                        @checked(old('estado', $servicio->estado ?? true))>
                    <label class="form-check-label fw-medium" for="servicioEstado">Servicio activo</label>
                </div>
            </div>
        </div>
    </div>
</div>