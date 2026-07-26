@extends('layouts.admin')

@section('title', 'Editar vehículo')
@section('navbar-title', 'Editar vehículo')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.vehiculos.index') }}">Vehículos</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar vehículo"
        :description="'Modifica los datos del vehículo con placa ' . $vehiculo->placa . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.vehiculos.update', $vehiculo) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.vehiculos._form', ['vehiculo' => $vehiculo])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
