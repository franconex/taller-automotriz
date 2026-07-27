<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field
        name="nombre_completo"
        label="Nombre completo"
        :value="$empleado->nombre_completo ?? null"
        required
        icon="bi-person" />
    <x-admin.form-field
        name="ci"
        label="Cédula de identidad"
        :value="$empleado->ci ?? null"
        required
        icon="bi-card-text" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field
        name="telefono"
        label="Teléfono"
        :value="$empleado->telefono ?? null"
        required
        icon="bi-telephone" />
    <x-admin.form-field
        name="email"
        type="email"
        label="Correo electrónico"
        :value="$empleado->email ?? null"
        icon="bi-envelope" />
    <x-admin.form-field
        name="direccion"
        label="Dirección"
        :value="$empleado->direccion ?? null"
        icon="bi-geo-alt" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Asignación</h3>
    <x-admin.form-field
        name="sucursal_id"
        label="Sucursal"
        type="select"
        required>
        <option value="">— Selecciona una sucursal —</option>
        @foreach (($sucursales ?? collect()) as $s)
            <option value="{{ $s->id }}" @selected(old('sucursal_id', $empleado->sucursal_id ?? null) == $s->id)>
                {{ $s->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field
        name="rol_id"
        label="Rol"
        type="select"
        required>
        <option value="">— Selecciona un rol —</option>
        @foreach (($roles ?? collect()) as $r)
            <option value="{{ $r->id }}"
                data-rol-nombre="{{ $r->nombre }}"
                @selected(old('rol_id', $empleado->rol_id ?? null) == $r->id)>
                {{ $r->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field
        name="cargo"
        label="Cargo (opcional)"
        :value="$empleado->cargo ?? null"
        help="Texto libre para especificar el puesto concreto (opcional)."
        icon="bi-briefcase" />
    <x-admin.form-field
        name="fecha_contratacion"
        type="date"
        label="Fecha de contratación"
        :value="optional($empleado)->fecha_contratacion?->format('Y-m-d')" />
</div>

<div class="admin-form-section" id="mecanico-fields" style="display: none;">
    <h3 class="admin-form-section__title">
        <i class="bi bi-wrench-adjustable-circle me-1" aria-hidden="true"></i>
        Datos de mecánico
    </h3>
    <p class="text-muted small mb-3">Como el rol seleccionado es <strong>Mecánico</strong>, también debes registrar su especialidad y disponibilidad.</p>

    @php
        $rolActual = null;
        $rolId = old('rol_id', $empleado->rol_id ?? null);
        if ($rolId) {
            $rolActual = ($roles ?? collect())->firstWhere('id', (int) $rolId);
        }
        $esMecanicoActual = $rolActual && strcasecmp($rolActual->nombre, 'Mecánico') === 0;
    @endphp

    <x-admin.form-field
        name="especialidad"
        label="Especialidad (opcional)"
        :value="optional(optional($empleado)->mecanico)->especialidad?->nombre ?? old('especialidad')"
        help="Ej: Mecánica General, Electricidad Automotriz, Motores Diesel..."
        icon="bi-wrench" />
    <x-admin.form-field
        name="disponibilidad"
        label="Disponibilidad"
        type="select">
        <option value="disponible" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad ?? 'disponible') === 'disponible')>Disponible</option>
        <option value="ocupado" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ocupado')>Ocupado</option>
        <option value="ausente" @selected(old('disponibilidad', optional(optional($empleado)->mecanico)->disponibilidad) === 'ausente')>Ausente</option>
    </x-admin.form-field>
    <x-admin.form-field
        name="observaciones_mecanico"
        label="Observaciones"
        type="textarea"
        :value="optional(optional($empleado)->mecanico)->observaciones"
        help="Anotaciones adicionales sobre la disponibilidad o formación del mecánico." />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="empleadoEstado"
            name="estado"
            value="1"
            @checked(old('estado', $empleado->estado ?? true))>
        <label class="form-check-label" for="empleadoEstado">Empleado activo</label>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const selectRol = document.getElementById('field-rol_id');
                const sectionMecanico = document.getElementById('mecanico-fields');
                if (!selectRol || !sectionMecanico) return;

                const esMecanico = () => {
                    const opt = selectRol.options[selectRol.selectedIndex];
                    if (!opt) return false;
                    const nombre = (opt.getAttribute('data-rol-nombre') || opt.text || '').trim().toLowerCase();
                    return nombre === 'mecánico' || nombre === 'mecanico';
                };

                const actualizar = () => {
                    sectionMecanico.style.display = esMecanico() ? '' : 'none';
                };

                selectRol.addEventListener('change', actualizar);
                actualizar();
            })();
        </script>
    @endpush
@endonce
