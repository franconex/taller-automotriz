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
    <h3 class="admin-form-section__title">Problema reportado</h3>
    <x-admin.form-field name="descripcion_problema" label="Descripción del problema" type="textarea" :value="$orden->descripcion_problema ?? null" required />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Tiempo estimado</h3>
    <x-admin.form-field name="tiempo_estimado_horas" type="number" label="Tiempo estimado (horas)" :value="$orden->tiempo_estimado_horas ?? null" help="Tiempo estimado para completar la reparación" step="0.5" min="0" max="999.9" />
</div>

@if (isset($tiposServicio) && $tiposServicio->isNotEmpty())
<div class="admin-form-section">
    <h3 class="admin-form-section__title">Servicios a realizar</h3>
    <div class="row g-2">
        @foreach ($tiposServicio as $tipo)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border">
                    <div class="card-header py-2 px-3">
                        <strong class="small">{{ $tipo->nombre }}</strong>
                    </div>
                    <div class="card-body py-2 px-3" style="max-height:180px;overflow-y:auto;">
                        @forelse ($tipo->servicios as $servicio)
                            <div class="form-check">
                                <input class="form-check-input servicio-checkbox" type="checkbox"
                                    name="servicios_ids[]" value="{{ $servicio->id }}"
                                    id="servicio_{{ $servicio->id }}">
                                <label class="form-check-label small" for="servicio_{{ $servicio->id }}">
                                    {{ $servicio->nombre }}
                                    <span class="text-muted">(Bs. {{ number_format((float) $servicio->precio_base, 2, ',', '.') }})</span>
                                </label>
                            </div>
                        @empty
                            <span class="text-muted small">Sin servicios disponibles</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="form-text">Selecciona los servicios que se realizarán en esta orden. Los montos se agregarán automáticamente al total.</div>
</div>
@endif

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Asignación de mecánico</h3>
    <x-admin.form-field name="mecanico_id" label="Mecánico asignado" type="select" required>
        <option value="">— Selecciona un mecánico disponible —</option>
        @foreach (($mecanicos ?? collect()) as $m)
            @php
                $asignado = $orden->asignaciones ? $orden->asignaciones->where('mecanico_id', $m->id)->first() : null;
            @endphp
            <option value="{{ $m->id }}" @selected(old('mecanico_id', optional($asignado)->mecanico_id) == $m->id)>
                {{ $m->empleado->nombre_completo ?? 'Mecánico #' . $m->id }}
                @if ($m->disponibilidad === 'ocupado' && !$asignado) (ocupado) @endif
                @if ($asignado) (asignado actualmente) @endif
            </option>
        @endforeach
    </x-admin.form-field>
    <div class="form-text">Solo se muestran mecánicos disponibles. Al asignar, su estado cambia a "ocupado".</div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Descuento y estado</h3>
    <x-admin.form-field name="descuento" type="number" label="Descuento" :value="$orden->descuento ?? 0" help="Descuento aplicado al total" />
    <x-admin.form-field name="estado" label="Estado" type="select">
        <option value="recibida" @selected(old('estado', $orden->estado ?? 'recibida') === 'recibida')>Recibida</option>
        <option value="diagnostico" @selected(old('estado', $orden->estado ?? null) === 'diagnostico')>En diagnóstico</option>
        <option value="en_proceso" @selected(old('estado', $orden->estado ?? null) === 'en_proceso')>En proceso</option>
        <option value="finalizada" @selected(old('estado', $orden->estado ?? null) === 'finalizada')>Finalizada</option>
        <option value="entregada" @selected(old('estado', $orden->estado ?? null) === 'entregada')>Entregada</option>
        <option value="anulada" @selected(old('estado', $orden->estado ?? null) === 'anulada')>Anulada</option>
    </x-admin.form-field>
</div>
