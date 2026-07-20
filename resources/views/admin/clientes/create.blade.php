@extends('layouts.admin')

@section('title', 'Crear Cliente')

@section('page-title', 'Crear Cliente')

@section('content')
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
            @csrf
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Datos del cliente</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre" :required="true" placeholder="Ej. Juan" />
                    <x-admin.form-input name="apellido" label="Apellido" :required="true" placeholder="Ej. Pérez" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="ci" label="Cédula de Identidad" :required="true" placeholder="Ej. 1234567" />
                    <x-admin.form-input name="telefono" label="Teléfono" placeholder="Ej. 70000000" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="email" type="email" label="Correo electrónico" placeholder="cliente@correo.com" />
                    <x-admin.form-input name="direccion" label="Dirección" placeholder="Ej. Av. Principal #456" />
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nit" label="NIT" placeholder="Ej. 1234567890" />
                    <x-admin.form-input name="razon_social" label="Razón social" placeholder="Ej. Juan Pérez SRL" />
                </div>
                <div class="mt-5">
                    <x-admin.form-input name="observaciones" type="textarea" label="Observaciones" placeholder="Notas adicionales..." />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Guardar cliente</button>
                <a href="{{ route('admin.clientes.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
