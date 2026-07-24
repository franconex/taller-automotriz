<div class="admin-form-section">
    <h3 class="admin-form-section__title">Empleado</h3>
    <p class="text-muted small mb-3">El usuario heredará automáticamente el nombre, email, rol y sucursal del empleado seleccionado.</p>

    <x-admin.form-field name="empleado_id" label="Empleado" type="select" required>
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
    </x-admin.form-field>

    <div id="usuario-resumen" class="p-3 rounded bg-light border d-none">
        <h4 class="h6 fw-bold mb-2">Datos que se asignarán</h4>
        <dl class="admin-meta small mb-0">
            <dt>Nombre</dt><dd id="preview-nombre">—</dd>
            <dt>Email</dt><dd id="preview-email">—</dd>
            <dt>Rol</dt><dd id="preview-rol">—</dd>
            <dt>Sucursal</dt><dd id="preview-sucursal">—</dd>
        </dl>
    </div>
</div>

<input type="hidden" name="nombre" id="hidden-nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}">
<input type="hidden" name="email" id="hidden-email" value="{{ old('email', $usuario->email ?? '') }}">
<input type="hidden" name="rol_id" id="hidden-rol_id" value="{{ old('rol_id', $usuario->rol_id ?? '') }}">
<input type="hidden" name="sucursal_id" id="hidden-sucursal_id" value="{{ old('sucursal_id', $usuario->sucursal_id ?? '') }}">

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Credenciales</h3>
    <x-admin.form-field name="username" label="Nombre de usuario" :value="$usuario->username ?? null" required icon="bi-at" />
    @if (! isset($usuario) || ! $usuario->exists)
        <x-admin.form-field name="password" type="password" label="Contraseña" required icon="bi-lock" autocomplete="new-password" />
        <x-admin.form-field name="password_confirmation" type="password" label="Confirmar contraseña" required icon="bi-lock-fill" autocomplete="new-password" />
    @else
        <p class="cell-muted small mb-2">La contraseña solo se modifica desde "Restablecer contraseña" en la lista.</p>
    @endif
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <x-admin.form-field name="estado" label="Estado" type="select">
        <option value="activo" @selected(old('estado', $usuario->estado ?? 'activo') === 'activo')>Activo</option>
        <option value="inactivo" @selected(old('estado', $usuario->estado ?? 'activo') === 'inactivo')>Inactivo</option>
    </x-admin.form-field>
</div>

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
