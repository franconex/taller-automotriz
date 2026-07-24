<div class="admin-form-section">
    <h3 class="admin-form-section__title">Cliente y vehículo</h3>
    <x-admin.form-field name="cliente_id" label="Cliente" type="select" required>
        <option value="">— Selecciona un cliente —</option>
        @foreach (($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" @selected(old('cliente_id', $orden->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="vehiculo_id" label="Vehículo" type="select" required>
        <option value="">— Selecciona un vehículo —</option>
        @foreach (($vehiculos ?? collect()) as $v)
            <option value="{{ $v->id }}" @selected(old('vehiculo_id', $orden->vehiculo_id ?? null) == $v->id)>{{ $v->placa }} — {{ $v->cliente->nombre_completo ?? '' }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="sucursal_id" label="Sucursal" type="select" required>
        <option value="">— Selecciona una sucursal —</option>
        @foreach (($sucursales ?? collect()) as $s)
            <option value="{{ $s->id }}" @selected(old('sucursal_id', $orden->sucursal_id ?? null) == $s->id)>{{ $s->nombre }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Recepción</h3>
    <x-admin.form-field name="kilometraje_ingreso" type="number" label="Kilometraje de ingreso" :value="$orden->kilometraje_ingreso ?? null" icon="bi-speedometer2" />
    <x-admin.form-field name="descripcion_problema" label="Descripción del problema" type="textarea" :value="$orden->descripcion_problema ?? null" required />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Diagnóstico y notas</h3>
    <x-admin.form-field name="diagnostico_general" label="Diagnóstico general" type="textarea" :value="$orden->diagnostico_general ?? null" />
    <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="$orden->observaciones ?? null" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Descuento y estado</h3>
    <x-admin.form-field name="descuento" type="number" label="Descuento" :value="$orden->descuento ?? 0" help="Descuento aplicado al total" />
    <x-admin.form-field name="estado" label="Estado" type="select">
        <option value="recibida"    @selected(old('estado', $orden->estado ?? 'recibida') === 'recibida')>Recibida</option>
        <option value="diagnostico" @selected(old('estado', $orden->estado ?? null) === 'diagnostico')>En diagnóstico</option>
        <option value="en_proceso"  @selected(old('estado', $orden->estado ?? null) === 'en_proceso')>En proceso</option>
        <option value="finalizada"  @selected(old('estado', $orden->estado ?? null) === 'finalizada')>Finalizada</option>
        <option value="entregada"   @selected(old('estado', $orden->estado ?? null) === 'entregada')>Entregada</option>
        <option value="anulada"     @selected(old('estado', $orden->estado ?? null) === 'anulada')>Anulada</option>
    </x-admin.form-field>
</div>
