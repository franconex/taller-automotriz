@extends('layouts.admin')

@section('title', 'Crear Usuario')
@section('page-title', 'Crear Usuario')

@section('content')
    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-5">
            @csrf
            <div class="card p-5">
                <h3 class="text-sm font-semibold mb-4" style="color: var(--color-text);">Datos del usuario</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre completo" :required="true" placeholder="Ej. Juan Pérez" />
                    <x-admin.form-input name="username" label="Nombre de usuario" :required="true" placeholder="Ej. jperez" />
                </div>
                <div class="mt-4">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :required="true" placeholder="ejemplo@correo.com" />
                </div>
                <div class="mt-4">
                    <x-admin.form-input name="rol_id" type="select" label="Rol" :required="true">
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <x-admin.form-input name="password" type="password" label="Contraseña" :required="true" placeholder="Mín. 8 caracteres" />
                    <x-admin.form-input name="password_confirmation" type="password" label="Confirmar contraseña" :required="true" placeholder="Repite la contraseña" />
                </div>
                <div class="mt-4">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="estado" value="1" checked class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span style="color: var(--color-text);">Usuario activo</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <x-admin.button type="primary" tag="button">Guardar usuario</x-admin.button>
                <a href="{{ route('admin.usuarios.index') }}"><x-admin.button type="secondary" tag="button">Cancelar</x-admin.button></a>
            </div>
        </form>
    </div>
@endsection
