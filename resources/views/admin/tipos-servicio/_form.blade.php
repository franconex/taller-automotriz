<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field name="nombre" label="Nombre" :value="$tipo->nombre ?? null" required icon="bi-tag" />
    <x-admin.form-field name="descripcion" label="Descripción" type="textarea" :value="$tipo->descripcion ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="tipoEstado"
            name="estado"
            value="1"
            @checked(old('estado', $tipo->estado ?? true))>
        <label class="form-check-label" for="tipoEstado">Tipo activo</label>
    </div>
</div>
