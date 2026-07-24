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
        name="cargo"
        label="Cargo"
        :value="$empleado->cargo ?? null"
        required
        icon="bi-briefcase" />
    <x-admin.form-field
        name="fecha_contratacion"
        type="date"
        label="Fecha de contratación"
        :value="optional($empleado)->fecha_contratacion?->format('Y-m-d')" />
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
