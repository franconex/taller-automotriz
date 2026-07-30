<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-box-seam" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Identificación</h3>
            </div>
            <div class="mb-3">
                <label for="field-nombre" class="form-label fw-medium">Nombre <span class="required">*</span></label>
                <input id="field-nombre" type="text" name="nombre" value="{{ old('nombre', $repuesto->nombre ?? '') }}" required
                       class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}" placeholder="Ej: Filtro de aceite">
                @if ($errors->has('nombre'))<div class="invalid-feedback d-block">{{ $errors->first('nombre') }}</div>@endif
            </div>
            <div class="mb-3">
                <label for="field-tipo" class="form-label fw-medium">Tipo <span class="required">*</span></label>
                <select name="tipo" id="field-tipo" class="form-select{{ $errors->has('tipo') ? ' is-invalid' : '' }}">
                    <option value="repuesto" @selected(($repuesto->tipo ?? 'repuesto') === 'repuesto')>Repuesto</option>
                    <option value="herramienta" @selected(($repuesto->tipo ?? '') === 'herramienta')>Herramienta (control interno)</option>
                </select>
                @if ($errors->has('tipo'))<div class="invalid-feedback d-block">{{ $errors->first('tipo') }}</div>@endif
            </div>
            <div id="camposHerramienta" style="display:none;">
                <div class="rounded p-3 mb-3" style="background:#fffbeb;border:1px solid #fde68a;">
                    <i class="bi bi-gear" style="color:#d97706;"></i>
                    <span class="small">Las herramientas son solo para control interno del taller. No se venden.</span>
                </div>
                <div class="mb-3">
                    <label for="field-cantidad_inicial" class="form-label fw-medium">Cantidad disponible</label>
                    <input id="field-cantidad_inicial" type="number" name="cantidad_inicial" value="{{ old('cantidad_inicial', 1) }}"
                           class="form-control{{ $errors->has('cantidad_inicial') ? ' is-invalid' : '' }}" min="0">
                    @if ($errors->has('cantidad_inicial'))<div class="invalid-feedback d-block">{{ $errors->first('cantidad_inicial') }}</div>
                    @else<div class="form-text">Cuántas unidades tiene el taller de esta herramienta.</div>@endif
                </div>
            </div>
            <div id="camposRepuesto">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label for="field-codigo" class="form-label fw-medium">Código</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-upc" style="color:#64748b;"></i></span>
                            <input id="field-codigo" type="text" name="codigo" value="{{ old('codigo', $repuesto->codigo ?? '') }}"
                                   class="form-control{{ $errors->has('codigo') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="REP-001">
                        </div>
                        @if ($errors->has('codigo'))<div class="invalid-feedback d-block">{{ $errors->first('codigo') }}</div>@endif
                    </div>
                    <div class="col-6">
                        <label for="field-codigo_barras" class="form-label fw-medium">Código de barras</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;"><i class="bi bi-upc-scan" style="color:#64748b;"></i></span>
                            <input id="field-codigo_barras" type="text" name="codigo_barras" value="{{ old('codigo_barras', $repuesto->codigo_barras ?? '') }}"
                                   class="form-control{{ $errors->has('codigo_barras') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="7791234567890">
                        </div>
                        @if ($errors->has('codigo_barras'))<div class="invalid-feedback d-block">{{ $errors->first('codigo_barras') }}</div>@endif
                    </div>
                </div>
                <div class="mb-3">
                    <label for="field-codigo_fabricante" class="form-label fw-medium">Código del fabricante</label>
                    <input id="field-codigo_fabricante" type="text" name="codigo_fabricante" value="{{ old('codigo_fabricante', $repuesto->codigo_fabricante ?? '') }}"
                           class="form-control{{ $errors->has('codigo_fabricante') ? ' is-invalid' : '' }}" placeholder="Ej: OEM-12345">
                    @if ($errors->has('codigo_fabricante'))<div class="invalid-feedback d-block">{{ $errors->first('codigo_fabricante') }}</div>@endif
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label for="field-marca" class="form-label fw-medium">Marca</label>
                        <input id="field-marca" type="text" name="marca" value="{{ old('marca', $repuesto->marca ?? '') }}"
                               class="form-control{{ $errors->has('marca') ? ' is-invalid' : '' }}" placeholder="Ej: Bosch">
                        @if ($errors->has('marca'))<div class="invalid-feedback d-block">{{ $errors->first('marca') }}</div>@endif
                    </div>
                    <div class="col-6">
                        <label for="field-categoria_id" class="form-label fw-medium">Categoría</label>
                        <select name="categoria_id" id="field-categoria_id" class="form-select{{ $errors->has('categoria_id') ? ' is-invalid' : '' }}">
                            <option value="">— Sin categoría —</option>
                            @foreach (($categorias ?? collect()) as $cat)
                                <option value="{{ $cat->id }}" @selected(($repuesto->categoria_id ?? null) == $cat->id)>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('categoria_id'))<div class="invalid-feedback d-block">{{ $errors->first('categoria_id') }}</div>@endif
                    </div>
                </div>
                <div class="mb-0">
                    <label for="field-descripcion" class="form-label fw-medium">Descripción</label>
                    <textarea id="field-descripcion" name="descripcion" rows="3"
                              class="form-control{{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
                              placeholder="Especificaciones, compatibilidad...">{{ old('descripcion', $repuesto->descripcion ?? '') }}</textarea>
                    @if ($errors->has('descripcion'))<div class="invalid-feedback d-block">{{ $errors->first('descripcion') }}</div>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="admin-card-module">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#fef2f2;color:#dc2626;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-currency-dollar" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Precios</h3>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="field-costo_compra" class="form-label fw-medium">Precio de compra</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">Bs</span>
                        <input id="field-costo_compra" type="number" step="0.01" min="0" name="costo_compra"
                               value="{{ old('costo_compra', $repuesto->costo_compra ?? '') }}"
                               class="form-control{{ $errors->has('costo_compra') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="0.00">
                    </div>
                    @if ($errors->has('costo_compra'))<div class="invalid-feedback d-block">{{ $errors->first('costo_compra') }}</div>@endif
                </div>
                <div class="col-6">
                    <label for="field-precio_venta" class="form-label fw-medium">Precio de venta</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="border-right:0;">Bs</span>
                        <input id="field-precio_venta" type="number" step="0.01" min="0" name="precio_venta"
                               value="{{ old('precio_venta', $repuesto->precio_venta ?? '') }}"
                               class="form-control{{ $errors->has('precio_venta') ? ' is-invalid' : '' }}" style="border-left:0;" placeholder="0.00">
                    </div>
                    @if ($errors->has('precio_venta'))<div class="invalid-feedback d-block">{{ $errors->first('precio_venta') }}</div>@endif
                </div>
            </div>
        </div>
        <div class="admin-card-module mt-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-image" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Imagen del producto</h3>
            </div>
            @if (!empty($repuesto->imagen))
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $repuesto->imagen) }}" alt="{{ $repuesto->nombre }}" style="max-height:120px;border-radius:6px;border:1px solid #e2e8f0;">
                    <div class="form-text mt-1">
                        <label><input type="checkbox" name="eliminar_imagen" value="1"> Eliminar imagen actual</label>
                    </div>
                </div>
            @endif
            <input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG o WebP. Máximo 2MB.</div>
        </div>
        <div class="admin-card-module mt-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-module" style="background:#f0fdf4;color:#16a34a;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;"><i class="bi bi-toggle-on" style="font-size:1rem;"></i></span>
                <h3 class="fw-bold mb-0" style="font-size:1rem;">Estado</h3>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="estado" value="0">
                <input class="form-check-input" type="checkbox" id="repuestoEstado" name="estado" value="1" @checked(old('estado', $repuesto->estado ?? true))>
                <label class="form-check-label fw-medium" for="repuestoEstado">Activo</label>
            </div>
        </div>
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