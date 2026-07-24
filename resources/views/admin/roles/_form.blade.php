<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field
        name="nombre"
        label="Nombre del rol"
        :value="$rol->nombre ?? null"
        required
        icon="bi-shield-lock" />
    <x-admin.form-field
        name="descripcion"
        label="Descripción"
        type="textarea"
        :value="$rol->descripcion ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="rolEstado"
            name="estado"
            value="1"
            @checked(old('estado', $rol->estado ?? true))>
        <label class="form-check-label" for="rolEstado">Rol activo</label>
    </div>
</div>
