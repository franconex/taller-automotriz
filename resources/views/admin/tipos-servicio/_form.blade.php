<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-tags" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>
            <div class="mb-3">
                <label for="field-nombre" class="form-label fw-medium">Nombre <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-tag" style="color:#64748b;"></i>
                    </span>
                    <input id="field-nombre" type="text" name="nombre"
                           value="{{ old('nombre', $tipo->nombre ?? '') }}" required
                           class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                           placeholder="Ej: Mantenimiento" style="border-left:0;"
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
                          placeholder="Describe el tipo de servicio...">{{ old('descripcion', $tipo->descripcion ?? '') }}</textarea>
                @if ($errors->has('descripcion'))
                    <div class="invalid-feedback d-block">{{ $errors->first('descripcion') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="admin-card-module d-flex flex-column">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-toggle-on" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Estado</h3>
            </div>
            <div class="mt-auto">
                <div class="form-check form-switch">
                    <input type="hidden" name="estado" value="0">
                    <input class="form-check-input" type="checkbox" id="tipoEstado" name="estado" value="1"
                        @checked(old('estado', $tipo->estado ?? true))>
                    <label class="form-check-label fw-medium" for="tipoEstado">Tipo activo</label>
                </div>
            </div>
        </div>
    </div>
</div>