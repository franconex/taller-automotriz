@extends('layouts.admin')

@section('title', 'Editar sucursal')
@section('navbar-title', 'Editar sucursal')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.sucursales.index') }}">Sucursales</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar sucursal"
        :description="'Modifica los datos de la sucursal ' . $sucursal->nombre . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.sucursales.update', $sucursal) }}">
            @csrf
            @method('PUT')
            @include('admin.sucursales._form', ['sucursal' => $sucursal])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
