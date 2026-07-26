<div class="row g-3">
    <div class="col-12 col-md-6">
        <x-admin.form-field name="nombre" label="Nombre" :value="$repuesto->nombre ?? null" required />
    </div>
    <div class="col-12 col-md-6">
        <x-admin.form-field name="tipo" label="Tipo" type="select" :value="$repuesto->tipo ?? 'repuesto'" required>
            <option value="repuesto" @selected(($repuesto->tipo ?? 'repuesto') === 'repuesto')>Repuesto</option>
            <option value="herramienta" @selected(($repuesto->tipo ?? '') === 'herramienta')>Herramienta (control interno)</option>
        </x-admin.form-field>
    </div>
</div>

<div id="camposHerramienta" style="display:none;">
    <div class="alert alert-info py-2">
        <i class="bi bi-gear" aria-hidden="true"></i>
        Las herramientas son solo para control interno del taller. No se venden.
    </div>
    <x-admin.form-field name="cantidad_inicial" type="number" label="Cantidad disponible" :value="old('cantidad_inicial', 1)" min="0" help="Cuántas unidades tiene el taller de esta herramienta." />
</div>

<div id="camposRepuesto">
    <div class="row g-2">
        <div class="col-6">
            <x-admin.form-field name="codigo" label="Código" :value="$repuesto->codigo ?? null" icon="bi-upc" />
        </div>
        <div class="col-6">
            <x-admin.form-field name="codigo_barras" label="Código de barras" :value="$repuesto->codigo_barras ?? null" icon="bi-upc-scan" />
        </div>
    </div>
    <x-admin.form-field name="codigo_fabricante" label="Código del fabricante" :value="$repuesto->codigo_fabricante ?? null" />
    <x-admin.form-field name="marca" label="Marca" :value="$repuesto->marca ?? null" />
    <x-admin.form-field name="categoria" label="Categoría" :value="$repuesto->categoria ?? null" list="categoriasList" />
    <datalist id="categoriasList">
        @foreach (($categorias ?? collect()) as $cat)
            <option value="{{ $cat }}">
        @endforeach
    </datalist>
    <x-admin.form-field name="descripcion" label="Descripción" type="textarea" :value="$repuesto->descripcion ?? null" />

    <h3 class="h6 fw-bold mt-4 mb-2">Precios</h3>
    <div class="row g-2">
        <div class="col-6">
            <x-admin.form-field name="costo_compra" type="number" step="0.01" min="0" label="Precio de compra" :value="$repuesto->costo_compra ?? null" />
        </div>
        <div class="col-6">
            <x-admin.form-field name="precio_venta" type="number" step="0.01" min="0" label="Precio de venta" :value="$repuesto->precio_venta ?? null" />
        </div>
    </div>
</div>

<div class="mt-3">
    <div class="form-check form-switch">
        <input type="hidden" name="estado" value="0">
        <input class="form-check-input" type="checkbox" id="repuestoEstado" name="estado" value="1" @checked(old('estado', $repuesto->estado ?? true))>
        <label class="form-check-label" for="repuestoEstado">Activo</label>
    </div>
</div>

@push('scripts')
<script>
function tpToggleTipo() {
    var sel = document.querySelector('select[name="tipo"]');
    var tipo = sel ? sel.value : 'repuesto';
    document.getElementById('camposRepuesto').style.display = tipo === 'herramienta' ? 'none' : '';
    document.getElementById('camposHerramienta').style.display = tipo === 'herramienta' ? '' : 'none';
}
document.querySelector('select[name="tipo"]')?.addEventListener('change', tpToggleTipo);
tpToggleTipo();
</script>
@endpush
