@extends('layouts.admin')

@section('title', 'Editar Empleado')

@section('page-title', 'Editar Empleado')

@section('content')
    <div class="max-w-2xl space-y-8">
        <form method="POST" action="{{ route('admin.empleados.update', $empleado) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Datos personales</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre" :value="$empleado->nombre" :required="true" />
                    <x-admin.form-input name="apellido" label="Apellido" :value="$empleado->apellido" :required="true" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="ci" label="Cédula de Identidad" :value="$empleado->ci" :required="true" />
                    <x-admin.form-input name="telefono" label="Teléfono" :value="$empleado->telefono" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="direccion" label="Dirección" :value="$empleado->direccion" />
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información laboral</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="cargo" label="Cargo" :value="$empleado->cargo" />
                    <x-admin.form-input name="sucursal_id" type="select" label="Sucursal">
                        <option value="">Sin sucursal</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}" @selected(old('sucursal_id', $empleado->sucursal_id) == $s->id)>{{ $s->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-3">
                    <x-admin.form-input name="fecha_nacimiento" type="date" label="Nacimiento" :value="$empleado->fecha_nacimiento?->format('Y-m-d')" />
                    <x-admin.form-input name="fecha_ingreso" type="date" label="Ingreso" :value="$empleado->fecha_ingreso?->format('Y-m-d')" />
                    <x-admin.form-input name="salario" type="number" step="0.01" label="Salario (Bs)" :value="$empleado->salario" />
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" @checked($empleado->estado) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm" style="color: var(--color-text);">Empleado activo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                    Actualizar empleado
                </button>
                <a href="{{ route('admin.empleados.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                    Cancelar
                </a>
            </div>
        </form>

        @if ($empleado->user)
            <div class="rounded-2xl border p-6" style="border-color: rgba(var(--color-brand-red), 0.2); background-color: #FEF2F2;">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Cuenta de acceso</h3>
                <p class="text-sm mb-4" style="color: var(--color-muted);">
                    Usuario: <strong>{{ $empleado->user->username }}</strong> · 
                    Email: <strong>{{ $empleado->user->email }}</strong> ·
                    Rol actual: <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $empleado->user->rol->nombre ?? 'Sin rol' }}</span>
                </p>

                <form method="POST" action="{{ route('admin.empleados.cambiar-rol', $empleado) }}" class="flex flex-wrap items-end gap-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--color-text);">Cambiar rol</label>
                        <select name="rol_id" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/10">
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" @selected($empleado->user->rol_id === $rol->id)>{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="rounded-xl bg-brand-red px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark" onclick="return confirm('¿Cambiar el rol de {{ $empleado->nombre }} {{ $empleado->apellido }}? Los permisos se actualizarán automáticamente.')">
                        Cambiar rol
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
