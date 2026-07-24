<div class="admin-form-section">
    <h3 class="admin-form-section__title">Información personal</h3>
    <x-admin.form-field name="nombre_completo" label="Nombre completo" :value="$cliente->nombre_completo ?? null" required icon="bi-person" autocomplete="name" />
    <x-admin.form-field name="ci" label="Cédula de identidad" :value="$cliente->ci ?? null" icon="bi-card-text" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field name="telefono" label="Teléfono" :value="$cliente->telefono ?? null" required icon="bi-telephone" autocomplete="tel" />
    <x-admin.form-field name="email" label="Correo electrónico" type="email" :value="$cliente->email ?? null" icon="bi-envelope" autocomplete="email" />
    <x-admin.form-field name="direccion" label="Dirección" :value="$cliente->direccion ?? null" icon="bi-geo-alt" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="clienteEstado"
            name="estado"
            value="1"
            @checked(old('estado', $cliente->estado ?? true))>
        <label class="form-check-label" for="clienteEstado">Cliente activo</label>
    </div>
</div>
