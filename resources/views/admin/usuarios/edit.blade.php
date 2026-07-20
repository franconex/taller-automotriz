@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('page-title', 'Editar Usuario')

@section('content')
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos del usuario</h3>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre completo" :value="$usuario->nombre" :required="true" />
                    <x-admin.form-input name="username" label="Nombre de usuario" :value="$usuario->username" :required="true" />
                </div>

                <div class="mt-5">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :value="$usuario->email" :required="true" />
                </div>

                <div class="mt-5">
                    <x-admin.form-input name="rol_id" type="select" label="Rol" :required="true">
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('rol_id', $usuario->rol_id) == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </x-admin.form-input>
                </div>

                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" @checked($usuario->estado) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm text-gray-700">Usuario activo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                    Actualizar usuario
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>

        <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-6">
            <h3 class="text-lg font-bold text-red-800 mb-2">Restablecer contraseña</h3>
            <p class="text-sm text-red-600 mb-4">Se generará una nueva contraseña temporal. El usuario deberá cambiarla en su próximo inicio de sesión.</p>
            <form method="POST" action="{{ route('admin.usuarios.password', $usuario) }}" onsubmit="return confirm('¿Restablecer la contraseña de {{ $usuario->nombre }}?')">
                @csrf
                @method('PUT')
                <div class="grid gap-3 sm:grid-cols-2 max-w-md">
                    <x-admin.form-input name="password" type="password" label="Nueva contraseña" :required="true" placeholder="Mín. 8 caracteres" />
                    <x-admin.form-input name="password_confirmation" type="password" label="Confirmar" :required="true" placeholder="Repite la contraseña" />
                </div>
                <button type="submit" class="mt-3 rounded-xl border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                    Restablecer contraseña
                </button>
            </form>
        </div>
    </div>
@endsection
