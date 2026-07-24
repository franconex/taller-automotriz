<div class="admin-form-section">
    <h3 class="admin-form-section__title">Identificación</h3>
    <x-admin.form-field name="codigo" label="Código" :value="$repuesto->codigo ?? null" required icon="bi-upc" />
    <x-admin.form-field name="nombre" label="Nombre" :value="$repuesto->nombre ?? null" required icon="bi-box-seam" />
    <x-admin.form-field name="descripcion" label="Descripción" type="textarea" :value="$repuesto->descripcion ?? null" />
    <x-admin.form-field name="proveedor_id" label="Proveedor" type="select">
        <option value="">— Sin proveedor —</option>
        @foreach (($proveedores ?? collect()) as $p)
            <option value="{{ $p->id }}" @selected(old('proveedor_id', $repuesto->proveedor_id ?? null) == $p->id)>{{ $p->nombre_empresa }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Precios y stock</h3>
    <div class="row g-2">
        <div class="col-6">
            <x-admin.form-field name="costo_compra" type="number" label="Costo" :value="$repuesto->costo_compra ?? null" required icon="bi-currency-dollar" />
        </div>
        <div class="col-6">
            <x-admin.form-field name="precio_venta" type="number" label="Precio de venta" :value="$repuesto->precio_venta ?? null" required icon="bi-cash-coin" />
        </div>
    </div>
    <x-admin.form-field name="stock_minimo" type="number" label="Stock mínimo" :value="$repuesto->stock_minimo ?? 0" help="Se notificará cuando el stock sea igual o menor." />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            id="repuestoEstado"
            name="estado"
            value="1"
            @checked(old('estado', $repuesto->estado ?? true))>
        <label class="form-check-label" for="repuestoEstado">Repuesto activo</label>
    </div>
</div>
