<div class="admin-form-section">
    <h3 class="admin-form-section__title">Orden de trabajo</h3>
    <x-admin.form-field name="orden_trabajo_id" label="Orden de trabajo" type="select" required>
        <option value="">— Selecciona una orden —</option>
        @foreach (($ordenes ?? collect()) as $o)
            <option value="{{ $o->id }}" @selected(old('orden_trabajo_id', $pago->orden_trabajo_id ?? ($ordenId ?? null)) == $o->id)>
                {{ $o->numero_orden }} — {{ $o->cliente->nombre_completo ?? '' }}
            </option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Pago</h3>
    <x-admin.form-field name="metodo_pago_id" label="Método de pago" type="select" required>
        <option value="">— Selecciona un método —</option>
        @foreach (($metodos ?? collect()) as $m)
            <option value="{{ $m->id }}" @selected(old('metodo_pago_id', $pago->metodo_pago_id ?? null) == $m->id)>{{ $m->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <div class="row g-2">
        <div class="col-7">
            <x-admin.form-field name="monto" type="number" label="Monto" :value="$pago->monto ?? null" required icon="bi-currency-dollar" />
        </div>
        <div class="col-5">
            <x-admin.form-field name="fecha_pago" type="datetime-local" label="Fecha y hora" :value="isset($pago) && $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')" required />
        </div>
    </div>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Comprobante</h3>
    <x-admin.form-field name="numero_comprobante" label="N° de comprobante" :value="$pago->numero_comprobante ?? null" icon="bi-upc" />
    <x-admin.form-field name="referencia" label="Referencia" :value="$pago->referencia ?? null" icon="bi-link-45deg" />
    <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="$pago->observaciones ?? null" />
</div>
