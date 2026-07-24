<div class="admin-form-section">
    <h3 class="admin-form-section__title">Datos básicos</h3>
    <x-admin.form-field name="nombre" label="Nombre" :value="$usuario->nombre ?? null" required icon="bi-person" />
    <x-admin.form-field name="username" label="Nombre de usuario" :value="$usuario->username ?? null" required icon="bi-at" />
    <x-admin.form-field name="email" type="email" label="Correo electrónico" :value="$usuario->email ?? null" required icon="bi-envelope" />
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Asignación</h3>
    <x-admin.form-field name="rol_id" label="Rol" type="select" required>
        <option value="">— Selecciona un rol —</option>
        @foreach (($roles ?? collect()) as $r)
            <option value="{{ $r->id }}" @selected(old('rol_id', $usuario->rol_id ?? null) == $r->id)>{{ $r->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="sucursal_id" label="Sucursal" type="select" required>
        <option value="">— Selecciona una sucursal —</option>
        @foreach (($sucursales ?? collect()) as $s)
            <option value="{{ $s->id }}" @selected(old('sucursal_id', $usuario->sucursal_id ?? null) == $s->id)>{{ $s->nombre }}</option>
        @endforeach
    </x-admin.form-field>
    <x-admin.form-field name="empleado_id" label="Empleado asociado" type="select">
        <option value="">— Sin empleado asociado —</option>
        @foreach (($empleados ?? collect()) as $e)
            <option value="{{ $e->id }}" @selected(old('empleado_id', $usuario->empleado_id ?? null) == $e->id)>{{ $e->nombre_completo }}</option>
        @endforeach
    </x-admin.form-field>
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Credenciales</h3>
    @if (! isset($usuario) || ! $usuario->exists)
        <x-admin.form-field name="password" type="password" label="Contraseña" required icon="bi-lock" autocomplete="new-password" />
        <x-admin.form-field name="password_confirmation" type="password" label="Confirmar contraseña" required icon="bi-lock-fill" autocomplete="new-password" />
    @else
        <p class="cell-muted small mb-2">La contraseña solo se modifica desde "Restablecer contraseña" en la lista.</p>
    @endif
</div>

<div class="admin-form-section">
    <h3 class="admin-form-section__title">Estado</h3>
    <x-admin.form-field name="estado" label="Estado" type="select">
        <option value="activo" @selected(old('estado', $usuario->estado ?? 'activo') === 'activo')>Activo</option>
        <option value="inactivo" @selected(old('estado', $usuario->estado ?? 'activo') === 'inactivo')>Inactivo</option>
    </x-admin.form-field>
</div>
