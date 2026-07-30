<div class="row g-4">

    <div class="col-12 col-lg-7">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-person-lines-fill" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Empleado</h3>
            </div>
            <p class="cell-secondary small mb-3">El usuario heredará automáticamente el nombre, email, rol y sucursal del empleado seleccionado.</p>

            <div class="mb-3">
                <label for="field-empleado_id" class="form-label fw-medium">
                    Empleado <span class="required" aria-hidden="true">*</span>
                </label>
                <select name="empleado_id" id="field-empleado_id" required
                        class="form-select{{ $errors->has('empleado_id') ? ' is-invalid' : '' }}">
                    <option value="">— Selecciona un empleado —</option>
                    @foreach (($empleados ?? collect()) as $e)
                        <option value="{{ $e->id }}"
                            @selected(old('empleado_id', $usuario->empleado_id ?? null) == $e->id)>
                            {{ $e->nombre_completo }} — {{ $e->rol->nombre ?? 'Sin rol' }}
                            @if ($e->sucursal)
                                ({{ $e->sucursal->nombre }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('empleado_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('empleado_id') }}</div>
                @endif
            </div>

            <div id="usuario-resumen" class="rounded p-3 d-none" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-info-circle" style="color:#2563eb;"></i>
                    <span class="fw-semibold" style="font-size:0.85rem;">Datos que se asignarán</span>
                </div>
                <div class="row g-2 small">
                    <div class="col-6">
                        <span class="cell-secondary">Nombre:</span>
                        <span class="fw-medium d-block" id="preview-nombre">—</span>
                    </div>
                    <div class="col-6">
                        <span class="cell-secondary">Email:</span>
                        <span class="fw-medium d-block" id="preview-email">—</span>
                    </div>
                    <div class="col-6">
                        <span class="cell-secondary">Rol:</span>
                        <span class="fw-medium d-block" id="preview-rol">—</span>
                    </div>
                    <div class="col-6">
                        <span class="cell-secondary">Sucursal:</span>
                        <span class="fw-medium d-block" id="preview-sucursal">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <i class="bi bi-key" style="font-size:1rem;"></i>
                </span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Credenciales</h3>
            </div>

            <div class="mb-3">
                <label for="field-username" class="form-label fw-medium">
                    Nombre de usuario <span class="required" aria-hidden="true">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light" style="border-right:0;">
                        <i class="bi bi-at" style="color:#64748b;"></i>
                    </span>
                    <input id="field-username"
                           type="text"
                           name="username"
                           value="{{ old('username', $usuario->username ?? '') }}"
                           required
                           class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                           style="border-left:0;">
                </div>
                @if ($errors->has('username'))
                    <div class="invalid-feedback d-block">{{ $errors->first('username') }}</div>
                @endif
            </div>

            @if (! isset($usuario) || ! $usuario->exists)
                <div class="mb-3">
                    <label for="field-password" class="form-label fw-medium">
                        Contraseña <span class="required" aria-hidden="true">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">
                            <i class="bi bi-lock" style="color:#64748b;"></i>
                        </span>
                        <input id="field-password"
                               type="password"
                               name="password"
                               required
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               style="border-left:0;"
                               autocomplete="new-password">
                    </div>
                    @if ($errors->has('password'))
                        <div class="invalid-feedback d-block">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="field-password_confirmation" class="form-label fw-medium">
                        Confirmar contraseña <span class="required" aria-hidden="true">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">
                            <i class="bi bi-lock-fill" style="color:#64748b;"></i>
                        </span>
                        <input id="field-password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required
                               class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                               style="border-left:0;"
                               autocomplete="new-password">
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <div class="invalid-feedback d-block">{{ $errors->first('password_confirmation') }}</div>
                    @endif
                </div>
            @else
                <div class="rounded p-3 mb-3" style="background:#fffbeb;border:1px solid #fde68a;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle" style="color:#d97706;"></i>
                        <span class="small">La contraseña solo se modifica desde <strong>"Restablecer contraseña"</strong> en la lista de usuarios.</span>
                    </div>
                </div>
            @endif

            <div class="mb-0">
                <label for="field-estado" class="form-label fw-medium">Estado</label>
                <select name="estado" id="field-estado"
                        class="form-select{{ $errors->has('estado') ? ' is-invalid' : '' }}">
                    <option value="activo" @selected(old('estado', $usuario->estado ?? 'activo') === 'activo')>Activo</option>
                    <option value="inactivo" @selected(old('estado', $usuario->estado ?? 'activo') === 'inactivo')>Inactivo</option>
                </select>
                @if ($errors->has('estado'))
                    <div class="invalid-feedback d-block">{{ $errors->first('estado') }}</div>
                @endif
            </div>
        </div>
    </div>

</div>

<input type="hidden" name="nombre" id="hidden-nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}">
<input type="hidden" name="email" id="hidden-email" value="{{ old('email', $usuario->email ?? '') }}">
<input type="hidden" name="rol_id" id="hidden-rol_id" value="{{ old('rol_id', $usuario->rol_id ?? '') }}">
<input type="hidden" name="sucursal_id" id="hidden-sucursal_id" value="{{ old('sucursal_id', $usuario->sucursal_id ?? '') }}">

@once
    @push('scripts')
        <script>
            (function () {
                var empleadosData = @json($empleadosData ?? []);

                var selectEl = document.getElementById('field-empleado_id');
                var resumenEl = document.getElementById('usuario-resumen');
                var previews = {
                    nombre: document.getElementById('preview-nombre'),
                    email: document.getElementById('preview-email'),
                    rol: document.getElementById('preview-rol'),
                    sucursal: document.getElementById('preview-sucursal'),
                };
                var hiddenFields = {
                    nombre: document.getElementById('hidden-nombre'),
                    email: document.getElementById('hidden-email'),
                    rol_id: document.getElementById('hidden-rol_id'),
                    sucursal_id: document.getElementById('hidden-sucursal_id'),
                };

                function actualizar() {
                    var id = parseInt(selectEl.value, 10);
                    var data = null;
                    if (empleadosData && Array.isArray(empleadosData)) {
                        data = empleadosData.find(function (e) { return e.id === id; }) || null;
                    }

                    if (data) {
                        resumenEl.classList.remove('d-none');
                        previews.nombre.textContent = data.nombre_completo || '—';
                        previews.email.textContent = data.email || '—';
                        previews.rol.textContent = data.rol_nombre || '—';
                        previews.sucursal.textContent = data.sucursal_nombre || '—';

                        hiddenFields.nombre.value = data.nombre_completo || '';
                        hiddenFields.email.value = data.email || '';
                        hiddenFields.rol_id.value = data.rol_id || '';
                        hiddenFields.sucursal_id.value = data.sucursal_id || '';
                    } else {
                        resumenEl.classList.add('d-none');
                        previews.nombre.textContent = '—';
                        previews.email.textContent = '—';
                        previews.rol.textContent = '—';
                        previews.sucursal.textContent = '—';

                        hiddenFields.nombre.value = '';
                        hiddenFields.email.value = '';
                        hiddenFields.rol_id.value = '';
                        hiddenFields.sucursal_id.value = '';
                    }
                }

                selectEl.addEventListener('change', actualizar);

                if (selectEl.value) {
                    actualizar();
                }
            })();
        </script>
    @endpush
@endonce