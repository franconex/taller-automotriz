<div class="row g-4">

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-gear" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Empleado</h3>
            </div>
            <div class="mb-0">
                <label for="field-empleado_id" class="form-label fw-medium">Empleado <span class="required">*</span></label>
                <select name="empleado_id" id="field-empleado_id" required
                        class="form-select{{ $errors->has('empleado_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un empleado —</option>
                    @foreach (($empleados ?? collect()) as $e)
                        <option value="{{ $e->id }}" @selected(old('empleado_id', $mecanico->empleado_id ?? null) == $e->id)>{{ $e->nombre_completo }}</option>
                    @endforeach
                </select>
                @if ($errors->has('empleado_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('empleado_id') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-tools" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Datos técnicos</h3>
            </div>

            <div class="mb-3">
                <label for="field-especialidad_id" class="form-label fw-medium">Especialidad <span class="required">*</span></label>
                <select name="especialidad_id" id="field-especialidad_id" required
                        class="form-select{{ $errors->has('especialidad_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona una especialidad —</option>
                    @foreach (($especialidades ?? collect()) as $esp)
                        <option value="{{ $esp->id }}" @selected(old('especialidad_id', $mecanico->especialidad_id ?? null) == $esp->id)>{{ $esp->nombre }}</option>
                    @endforeach
                </select>
                @if ($errors->has('especialidad_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('especialidad_id') }}</div>
                @endif
            </div>

            <div class="mb-3">
                <label for="field-disponibilidad" class="form-label fw-medium">Disponibilidad <span class="required">*</span></label>
                <select name="disponibilidad" id="field-disponibilidad" required
                        class="form-select{{ $errors->has('disponibilidad') ? ' is-invalid' : '' }}">
                    <option value="disponible" @selected(old('disponibilidad', $mecanico->disponibilidad ?? 'disponible') === 'disponible')>Disponible</option>
                    <option value="ocupado" @selected(old('disponibilidad', $mecanico->disponibilidad ?? null) === 'ocupado')>Ocupado</option>
                    <option value="ausente" @selected(old('disponibilidad', $mecanico->disponibilidad ?? null) === 'ausente')>Ausente</option>
                </select>
                @if ($errors->has('disponibilidad'))
                    <div class="invalid-feedback d-block">{{ $errors->first('disponibilidad') }}</div>
                @endif
            </div>

            <div class="mb-0">
                <label for="field-observaciones" class="form-label fw-medium">Observaciones</label>
                <textarea name="observaciones" id="field-observaciones" rows="3"
                          class="form-control{{ $errors->has('observaciones') ? ' is-invalid' : '' }}"
                          placeholder="Anotaciones adicionales...">{{ old('observaciones', $mecanico->observaciones ?? '') }}</textarea>
                @if ($errors->has('observaciones'))
                    <div class="invalid-feedback d-block">{{ $errors->first('observaciones') }}</div>
                @endif
            </div>
        </div>
    </div>

</div>