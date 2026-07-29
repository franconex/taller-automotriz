@php $sub = $subservicio ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Servicio <span class="text-danger">*</span></label>
        <select name="servicio_id" class="form-select @error('servicio_id') is-invalid @enderror" required>
            <option value="">Seleccionar servicio</option>
            @foreach ($servicios as $s)
                <option value="{{ $s->id }}" {{ old('servicio_id', $sub?->servicio_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }} ({{ $s->tipoServicio?->nombre ?? '—' }})</option>
            @endforeach
        </select>
        @error('servicio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $sub?->nombre) }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold">Descripción</label>
        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion', $sub?->descripcion) }}</textarea>
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold">Precio base <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" min="0" name="precio_base" class="form-control @error('precio_base') is-invalid @enderror" value="{{ old('precio_base', $sub?->precio_base) }}" required>
            @error('precio_base')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold">Duración estimada (minutos)</label>
        <input type="number" min="1" max="1440" name="duracion_estimada_minutos" class="form-control @error('duracion_estimada_minutos') is-invalid @enderror" value="{{ old('duracion_estimada_minutos', $sub?->duracion_estimada_minutos) }}">
        @error('duracion_estimada_minutos')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 d-flex align-items-center pt-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="requiere_diagnostico" id="reqDiag" value="1" {{ old('requiere_diagnostico', $sub?->requiere_diagnostico) ? 'checked' : '' }}>
            <label class="form-check-label small" for="reqDiag">Requiere diagnóstico</label>
        </div>
    </div>
</div>

<hr>
<h6 class="fw-bold mb-3">Repuestos sugeridos</h6>
<div id="repuestos-container">
    @if ($sub && $sub->relationLoaded('repuestos'))
        @foreach ($sub->repuestos as $i => $r)
            <div class="row g-2 mb-2 repuesto-fila">
                <div class="col-md-8">
                    <select name="repuestos[{{ $i }}][repuesto_id]" class="form-select form-select-sm">
                        <option value="">Seleccionar repuesto</option>
                        @foreach ($repuestos as $rep)
                            <option value="{{ $rep->id }}" {{ $r->id == $rep->id ? 'selected' : '' }}>{{ $rep->nombre }} ({{ $rep->codigo }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" min="0.01" name="repuestos[{{ $i }}][cantidad]" class="form-control form-control-sm" placeholder="Cant." value="{{ $r->pivot->cantidad_sugerida }}">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.repuesto-fila').remove()"><i class="bi bi-x"></i></button>
                </div>
            </div>
        @endforeach
    @endif
</div>
<button type="button" class="btn btn-sm btn-outline-secondary" id="agregar-repuesto"><i class="bi bi-plus"></i> Agregar repuesto</button>

@push('scripts')
<script>
let repuestoIndex = {{ ($sub && $sub->relationLoaded('repuestos')) ? $sub->repuestos->count() : 0 }};
document.getElementById('agregar-repuesto')?.addEventListener('click', function() {
    const container = document.getElementById('repuestos-container');
    const options = `@foreach ($repuestos as $rep)<option value="{{ $rep->id }}">{{ $rep->nombre }} ({{ $rep->codigo }})</option>@endforeach`;
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 repuesto-fila';
    div.innerHTML = `<div class="col-md-8"><select name="repuestos[${repuestoIndex}][repuesto_id]" class="form-select form-select-sm"><option value="">Seleccionar repuesto</option>${options}</select></div><div class="col-md-3"><input type="number" step="0.01" min="0.01" name="repuestos[${repuestoIndex}][cantidad]" class="form-control form-control-sm" placeholder="Cant."></div><div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.repuesto-fila').remove()"><i class="bi bi-x"></i></button></div>`;
    container.appendChild(div);
    repuestoIndex++;
});
</script>
@endpush
