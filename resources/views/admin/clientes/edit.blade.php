@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('page-title', 'Editar Cliente')

@section('content')
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos del cliente</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre" :value="$cliente->nombre" :required="true" />
                    <x-admin.form-input name="apellido" label="Apellido" :value="$cliente->apellido" :required="true" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="ci" label="Cédula de Identidad" :value="$cliente->ci" :required="true" />
                    <x-admin.form-input name="telefono" label="Teléfono" :value="$cliente->telefono" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :value="$cliente->email" />
                    <x-admin.form-input name="direccion" label="Dirección" :value="$cliente->direccion" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nit" label="NIT" :value="$cliente->nit" />
                    <x-admin.form-input name="razon_social" label="Razón social" :value="$cliente->razon_social" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="observaciones" type="textarea" label="Observaciones" :value="$cliente->observaciones" />
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" @checked($cliente->estado) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm text-gray-700">Cliente activo</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Actualizar cliente</button>
                <a href="{{ route('admin.clientes.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
