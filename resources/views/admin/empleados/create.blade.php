@extends('layouts.admin')

@section('title', 'Crear Empleado')

@section('page-title', 'Crear Empleado')

@section('content')
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.empleados.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos personales</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre" :required="true" placeholder="Ej. Juan" />
                    <x-admin.form-input name="apellido" label="Apellido" :required="true" placeholder="Ej. Pérez" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="ci" label="Cédula de Identidad" :required="true" placeholder="Ej. 1234567" />
                    <x-admin.form-input name="telefono" label="Teléfono" placeholder="Ej. 70000000" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="direccion" label="Dirección" placeholder="Ej. Av. Principal #123" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Información laboral</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="cargo" label="Cargo" placeholder="Ej. Recepcionista" />
                    <x-admin.form-input name="sucursal_id" type="select" label="Sucursal">
                        <option value="">Selecciona una sucursal</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}" @selected(old('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-3">
                    <x-admin.form-input name="fecha_nacimiento" type="date" label="Fecha de nacimiento" />
                    <x-admin.form-input name="fecha_ingreso" type="date" label="Fecha de ingreso" />
                    <x-admin.form-input name="salario" type="number" step="0.01" label="Salario (Bs)" placeholder="0.00" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Cuenta de acceso</h3>
                <p class="text-sm text-gray-500 mb-4">Opcional: crea una cuenta de usuario para que el empleado acceda al sistema.</p>
                <label class="inline-flex items-center gap-2 mb-5">
                    <input type="checkbox" name="crear_usuario" value="1" id="crear_usuario" @checked(old('crear_usuario')) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                    <span class="text-sm font-medium text-gray-700">Crear cuenta de acceso</span>
                </label>
                <div id="user-fields" class="space-y-5 {{ old('crear_usuario') ? '' : 'hidden' }}">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.form-input name="username" label="Nombre de usuario" placeholder="Ej. jperez" />
                        <x-admin.form-input name="email" type="email" label="Correo electrónico" placeholder="ejemplo@correo.com" />
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.form-input name="password" type="password" label="Contraseña" placeholder="Mín. 8 caracteres" />
                        <x-admin.form-input name="password_confirmation" type="password" label="Confirmar contraseña" placeholder="Repite la contraseña" />
                    </div>
                    <x-admin.form-input name="rol_id" type="select" label="Rol">
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                    Guardar empleado
                </button>
                <a href="{{ route('admin.empleados.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('crear_usuario')?.addEventListener('change', function() {
            document.getElementById('user-fields').classList.toggle('hidden', !this.checked);
        });
    </script>
@endsection
