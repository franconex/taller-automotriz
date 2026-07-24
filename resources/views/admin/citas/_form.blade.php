<div class="admin-form-section">
    <h3 class="admin-form-section__title">Cliente y vehículo</h3>
    <x-admin.form-field name="cliente_id" label="Cliente" type="select" required>
        <option value="">— Selecciona un cliente —</option>
        @foreach (($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" @selected(old('cliente_id', $cita->cliente_id ?? null) == $c->id)>{{ $c->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="vehiculo_id" label="Vehículo" type="select" required>
        <option value="">— Selecciona un vehículo —</option>
        @foreach (($vehiculos ?? collect()) as $v)
            <option value="{{ $v->id }}" @selected(old('vehiculo_id', $cita->vehiculo_id ?? null) == $v->id)>{{ $v->placa }} — {{ $v->cliente->nombre_completo ?? '' }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Programación</h3>
    <div class="row g-2">
        <div class="col-7">
            <x-admin.form-field name="fecha" type="date" label="Fecha" :value="isset($cita) ? $cita->fecha?->format('Y-m-d') : null" required />
        </div>
        <div class="col-5">
            <x-admin.form-field name="hora" type="time" label="Hora" :value="$cita->hora ?? null" required />
        </div>
    </div>
    <x-admin.form-field name="sucursal_id" label="Sucursal" type="select" required>
        <option value="">— Selecciona una sucursal —</option>
        @foreach (($sucursales ?? collect()) as $s)
            <option value="{{ $s->id }}" @selected(old('sucursal_id', $cita->sucursal_id ?? null) == $s->id)>{{ $s->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="tipo" label="Tipo" type="select" required>
        <option value="diagnostico" @selected(old('tipo', $cita->tipo ?? 'diagnostico') === 'diagnostico')>Diagnóstico</option>
        <option value="mantenimiento" @selected(old('tipo', $cita->tipo ?? null) === 'mantenimiento')>Mantenimiento</option>
        <option value="reparacion" @selected(old('tipo', $cita->tipo ?? null) === 'reparacion')>Reparación</option>
        <option value="otro" @selected(old('tipo', $cita->tipo ?? null) === 'otro')>Otro</option>
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Detalle</h3>
    <x-admin.form-field name="descripcion_problema" label="Descripción del problema" type="textarea" :value="$cita->descripcion_problema ?? null" required />
    <x-admin.form-field name="costo_consulta" type="number" label="Costo de consulta" :value="$cita->costo_consulta ?? 0" help="0 si no aplica" />
    <div class="form-check form-switch mt-2">
        <input type="hidden" name="deja_vehiculo" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="citaDeja"
            name="deja_vehiculo"
            value="1"
            @checked(old('deja_vehiculo', $cita->deja_vehiculo ?? false))>
        <label class="form-check-label" for="citaDeja">Deja el vehículo en el taller</label>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <x-admin.form-field name="estado" label="Estado" type="select">
        <option value="pendiente" @selected(old('estado', $cita->estado ?? 'pendiente') === 'pendiente')>Pendiente</option>
        <option value="confirmada" @selected(old('estado', $cita->estado ?? null) === 'confirmada')>Confirmada</option>
        <option value="atendida" @selected(old('estado', $cita->estado ?? null) === 'atendida')>Atendida</option>
        <option value="cancelada" @selected(old('estado', $cita->estado ?? null) === 'cancelada')>Cancelada</option>
    </x-admin.form-field>
</div>
