@extends('layouts.admin')

@section('title', 'Crear Sucursal')

@section('page-title', 'Crear Sucursal')

@section('content')
    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.sucursales.store') }}" class="space-y-6">
            @csrf
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Datos de la sucursal</h3>
                <x-admin.form-input name="nombre" label="Nombre" :required="true" placeholder="Ej. Sucursal Central" />
                <div class="mt-5">
                    <x-admin.form-input name="direccion" label="Dirección" :required="true" placeholder="Ej. Av. Principal #456" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="telefono" label="Teléfono" placeholder="Ej. 3333333" />
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" placeholder="sucursal@taller.com" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="horario_atencion" label="Horario de atención" placeholder="Ej. Lun–Sáb 08:00–18:00" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="latitud" type="text" step="any" label="Latitud" placeholder="Ej. -17.8470" />
                    <x-admin.form-input name="longitud" type="text" step="any" label="Longitud" placeholder="Ej. -63.1633" />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Guardar sucursal</button>
                <a href="{{ route('admin.sucursales.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
