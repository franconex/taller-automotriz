<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field name="nombre_empresa" label="Nombre de la empresa" :value="$proveedor->nombre_empresa ?? null" required icon="bi-building" />
    <x-admin.form-field name="nit" label="NIT" :value="$proveedor->nit ?? null" icon="bi-upc" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field name="contacto" label="Persona de contacto" :value="$proveedor->contacto ?? null" icon="bi-person" />
    <x-admin.form-field name="telefono" label="Teléfono" :value="$proveedor->telefono ?? null" required icon="bi-telephone" />
    <x-admin.form-field name="email" type="email" label="Correo electrónico" :value="$proveedor->email ?? null" icon="bi-envelope" />
    <x-admin.form-field name="direccion" label="Dirección" :value="$proveedor->direccion ?? null" icon="bi-geo-alt" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Operación</h3>
    <x-admin.form-field name="tiempo_entrega_dias" type="number" label="Tiempo de entrega (días)" :value="$proveedor->tiempo_entrega_dias ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="proveedorEstado"
            name="estado"
            value="1"
            @checked(old('estado', $proveedor->estado ?? true))>
        <label class="form-check-label" for="proveedorEstado">Proveedor activo</label>
    </div>
</div>
