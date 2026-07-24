<div class="admin-form-section">
    <h3 class="admin-form-section__title">Empleado</h3>
    <x-admin.form-field name="empleado_id" label="Empleado" type="select" required>
        <option value="">— Selecciona un empleado —</option>
        @foreach (($empleados ?? collect()) as $e)
            <option value="{{ $e->id }}" @selected(old('empleado_id', $mecanico->empleado_id ?? null) == $e->id)>{{ $e->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Datos técnicos</h3>
    <x-admin.form-field name="especialidad_id" label="Especialidad" type="select" required>
        <option value="">— Selecciona una especialidad —</option>
        @foreach (($especialidades ?? collect()) as $esp)
            <option value="{{ $esp->id }}" @selected(old('especialidad_id', $mecanico->especialidad_id ?? null) == $esp->id)>{{ $esp->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="disponibilidad" label="Disponibilidad" type="select" required>
        <option value="disponible" @selected(old('disponibilidad', $mecanico->disponibilidad ?? 'disponible') === 'disponible')>Disponible</option>
        <option value="ocupado" @selected(old('disponibilidad', $mecanico->disponibilidad ?? null) === 'ocupado')>Ocupado</option>
        <option value="ausente" @selected(old('disponibilidad', $mecanico->disponibilidad ?? null) === 'ausente')>Ausente</option>
    </x-admin.form-field>
    <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="$mecanico->observaciones ?? null" />
</div>
