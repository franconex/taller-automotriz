@extends('layouts.admin')

@section('title', 'Crear Usuario')

@section('page-title', 'Crear Usuario')

@section('content')
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos del usuario</h3>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre completo" :required="true" placeholder="Ej. Juan Pérez" />
                    <x-admin.form-input name="username" label="Nombre de usuario" :required="true" placeholder="Ej. jperez" />
                </div>

                <div class="mt-5">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :required="true" placeholder="ejemplo@correo.com" />
                </div>

                <div class="mt-5">
                    <x-admin.form-input name="rol_id" type="select" label="Rol" :required="true">
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="password" type="password" label="Contraseña" :required="true" placeholder="Mín. 8 caracteres" />
                    <x-admin.form-input name="password_confirmation" type="password" label="Confirmar contraseña" :required="true" placeholder="Repite la contraseña" />
                </div>

                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" checked class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm text-gray-700">Usuario activo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                    Guardar usuario
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
