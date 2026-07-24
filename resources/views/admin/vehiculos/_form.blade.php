<div class="admin-form-section">
    <h3 class="admin-form-section__title">Propietario</h3>
    <x-admin.form-field name="cliente_id" label="Cliente" type="select" required>
        <option value="">— Selecciona un cliente —</option>
        @foreach (($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" @selected(old('cliente_id', $vehiculo->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Datos del vehículo</h3>
    <x-admin.form-field name="modelo_vehiculo_id" label="Modelo" type="select" required>
        <option value="">— Selecciona un modelo —</option>
        @foreach (($modelos ?? collect()) as $m)
            <option value="{{ $m->id }}" @selected(old('modelo_vehiculo_id', $vehiculo->modelo_vehiculo_id ?? null) == $m->id)>
                {{ optional($m->marca)->nombre }} {{ $m->nombre }}
            </option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="placa" label="Placa" :value="$vehiculo->placa ?? null" required icon="bi-upc" />
    <div class="row g-2">
        <div class="col-6">
            <x-admin.form-field name="anio" type="number" label="Año" :value="$vehiculo->anio ?? null" />
        </div>
        <div class="col-6">
            <x-admin.form-field name="color" label="Color" :value="$vehiculo->color ?? null" icon="bi-droplet" />
        </div>
    </div>
    <x-admin.form-field name="numero_chasis" label="Número de chasis" :value="$vehiculo->numero_chasis ?? null" icon="bi-upc-scan" />
    <x-admin.form-field name="kilometraje_actual" type="number" label="Kilometraje actual" :value="$vehiculo->kilometraje_actual ?? null" icon="bi-speedometer2" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Observaciones</h3>
    <x-admin.form-field name="observaciones" label="Notas" type="textarea" :value="$vehiculo->observaciones ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="vehiculoEstado"
            name="estado"
            value="1"
            @checked(old('estado', $vehiculo->estado ?? true))>
        <label class="form-check-label" for="vehiculoEstado">Vehículo activo</label>
    </div>
</div>
