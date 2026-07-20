@extends('layouts.admin')

@section('title', 'Crear Rol')

@section('page-title', 'Crear Rol')

@section('content')
    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
            @csrf
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Nuevo rol</h3>
                <x-admin.form-input name="nombre" label="Nombre del rol" :required="true" placeholder="Ej. Supervisor" />
                <div class="mt-5">
                    <x-admin.form-input name="descripcion" label="Descripción" placeholder="Ej. Supervisa las operaciones del taller" />
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" checked class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm" style="color: var(--color-text);">Rol activo</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Guardar rol</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
