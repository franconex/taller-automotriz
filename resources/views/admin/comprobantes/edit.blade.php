@extends('layouts.admin')

@section('title', 'Editar comprobante')
@section('navbar-title', 'Editar comprobante')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.comprobantes.index') }}">Comprobantes</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar comprobante"
        :description="'Modifica los datos fiscales del comprobante ' . $comprobante->numero . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.comprobantes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.comprobantes.update', $comprobante) }}">
            @csrf
            @method('PUT')
            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Datos fiscales</h3>
                <x-admin.form-field name="nit_ci" label="NIT/CI" :value="$comprobante->nit_ci" icon="bi-upc" />
                <x-admin.form-field name="razon_social" label="Razón social" :value="$comprobante->razon_social" icon="bi-building" />
            </div>
            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Observaciones</h3>
                <x-admin.form-field name="observaciones" label="Notas" type="textarea" :value="$comprobante->observaciones" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.comprobantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
