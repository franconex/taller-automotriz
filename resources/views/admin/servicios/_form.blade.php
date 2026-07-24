<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field name="tipo_servicio_id" label="Tipo de servicio" type="select" required>
        <option value="">— Selecciona un tipo —</option>
        @foreach (($tipos ?? collect()) as $t)
            <option value="{{ $t->id }}" @selected(old('tipo_servicio_id', $servicio->tipo_servicio_id ?? null) == $t->id)>{{ $t->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="nombre" label="Nombre" :value="$servicio->nombre ?? null" required icon="bi-gear" />
    <x-admin.form-field name="descripcion" label="Descripción" type="textarea" :value="$servicio->descripcion ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Precio y duración</h3>
    <div class="row g-2">
        <div class="col-7">
            <x-admin.form-field name="precio_base" type="number" label="Precio base" :value="$servicio->precio_base ?? null" required icon="bi-currency-dollar" />
        </div>
        <div class="col-5">
            <x-admin.form-field name="duracion_estimada_minutos" type="number" label="Duración (min)" :value="$servicio->duracion_estimada_minutos ?? null" />
        </div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="servicioEstado"
            name="estado"
            value="1"
            @checked(old('estado', $servicio->estado ?? true))>
        <label class="form-check-label" for="servicioEstado">Servicio activo</label>
    </div>
</div>
