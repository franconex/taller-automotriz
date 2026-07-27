<div class="admin-form-section">
    <h3 class="admin-form-section__title">Información personal</h3>
    <x-admin.form-field name="nombre_completo" label="Nombre completo" :value="$cliente->nombre_completo ?? null" required icon="bi-person" autocomplete="name" />
    <x-admin.form-field name="ci" label="Cédula de identidad" :value="$cliente->ci ?? null" icon="bi-card-text" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Contacto</h3>
    <x-admin.form-field name="telefono" label="Teléfono" :value="$cliente->telefono ?? null" required icon="bi-telephone" autocomplete="tel" />
    <x-admin.form-field name="email" label="Correo electrónico" type="email" :value="$cliente->email ?? null" icon="bi-envelope" autocomplete="email" />
    <x-admin.form-field name="direccion" label="Dirección" :value="$cliente->direccion ?? null" icon="bi-geo-alt" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">
        Vehículos
        <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="btn-agregar-vehiculo" title="Agregar otro vehículo">
            <i class="bi bi-plus-lg"></i>
        </button>
    </h3>
    @php $esCreacion = ! isset($cliente) || ! $cliente->id; @endphp
    @if ($esCreacion)
    <p class="small text-muted mb-3">Registra al menos un vehículo para el cliente.</p>
    @else
    <p class="small text-muted mb-3">Agrega más vehículos usando el botón <i class="bi bi-plus-lg"></i>.</p>
    @endif
    <div id="vehiculos-container">
        <div class="vehiculo-row border rounded-3 p-3 mb-3 position-relative" data-index="0">
            <div class="row g-3">
                <div class="col-md-6">
                    <x-admin.form-field name="vehiculos[0][marca]" label="Marca" :required="$esCreacion" icon="bi-building" />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field name="vehiculos[0][modelo]" label="Modelo" :required="$esCreacion" icon="bi-car-front" />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field name="vehiculos[0][placa]" label="Placa" :required="$esCreacion" icon="bi-upc-scan" />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field name="vehiculos[0][anio]" label="Año" type="number" />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field name="vehiculos[0][color]" label="Color" />
                </div>
            </div>
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
            id="clienteEstado"
            name="estado"
            value="1"
            @checked(old('estado', $cliente->estado ?? true))>
        <label class="form-check-label" for="clienteEstado">Cliente activo</label>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const container = document.getElementById('vehiculos-container');
    const btnAgregar = document.getElementById('btn-agregar-vehiculo');
    if (!container || !btnAgregar) return;

    let idx = container.querySelectorAll('.vehiculo-row').length;

    btnAgregar.addEventListener('click', function () {
        const row = container.querySelector('.vehiculo-row');
        if (!row) return;
        const clone = row.cloneNode(true);
        clone.dataset.index = idx;
        clone.querySelectorAll('input, select, textarea').forEach(function (el) {
            const name = el.getAttribute('name');
            if (name) el.name = name.replace(/\d+/, idx);
            if (el.type !== 'hidden') el.value = '';
            el.classList.remove('is-invalid');
            const fb = el.parentElement?.querySelector('.invalid-feedback');
            if (fb) fb.textContent = '';
        });

        // Boton eliminar
        if (!clone.querySelector('.btn-eliminar-vehiculo')) {
            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.className = 'btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 btn-eliminar-vehiculo';
            btnRemove.innerHTML = '<i class="bi bi-x-lg"></i>';
            btnRemove.title = 'Eliminar vehículo';
            btnRemove.addEventListener('click', function () { clone.remove(); });
            clone.style.position = 'relative';
            clone.appendChild(btnRemove);
        }

        container.appendChild(clone);
        idx++;
    });
})();
</script>
@endpush

