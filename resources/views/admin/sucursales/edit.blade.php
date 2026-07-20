@extends('layouts.admin')

@section('title', 'Editar Sucursal')

@section('page-title', 'Editar Sucursal')

@section('content')
    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.sucursales.update', $sucursale) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Datos de la sucursal</h3>
                <x-admin.form-input name="nombre" label="Nombre" :value="$sucursale->nombre" :required="true" />
                <div class="mt-5">
                    <x-admin.form-input name="direccion" label="Dirección" :value="$sucursale->direccion" :required="true" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="telefono" label="Teléfono" :value="$sucursale->telefono" />
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" :value="$sucursale->email" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="horario_atencion" label="Horario de atención" :value="$sucursale->horario_atencion" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="latitud" type="text" step="any" label="Latitud" :value="$sucursale->latitud" />
                    <x-admin.form-input name="longitud" type="text" step="any" label="Longitud" :value="$sucursale->longitud" />
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" @checked($sucursale->estado) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm" style="color: var(--color-text);">Sucursal activa</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Actualizar sucursal</button>
                <a href="{{ route('admin.sucursales.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
