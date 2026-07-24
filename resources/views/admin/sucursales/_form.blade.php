<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field
        name="nombre"
        label="Nombre de la sucursal"
        :value="$sucursal->nombre ?? null"
        required
        icon="bi-building" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Ubicación y contacto</h3>
    <x-admin.form-field
        name="direccion"
        label="Dirección"
        :value="$sucursal->direccion ?? null"
        required
        icon="bi-geo-alt" />
    <x-admin.form-field
        name="telefono"
        label="Teléfono"
        :value="$sucursal->telefono ?? null"
        required
        icon="bi-telephone"
        autocomplete="tel" />
    <x-admin.form-field
        name="horario_atencion"
        label="Horario de atención"
        :value="$sucursal->horario_atencion ?? null"
        icon="bi-clock"
        help="Ej. Lun a Vie 8:00–18:00, Sáb 9:00–13:00" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="sucursalEstado"
            name="estado"
            value="1"
            @checked(old('estado', $sucursal->estado ?? true))>
        <label class="form-check-label" for="sucursalEstado">Sucursal activa</label>
    </div>
</div>
