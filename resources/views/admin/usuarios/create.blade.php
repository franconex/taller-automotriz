@extends('layouts.admin')

@section('title', 'Nuevo usuario')
@section('navbar-title', 'Nuevo usuario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo usuario"
        description="Crea una cuenta de acceso al sistema para un empleado.">
        <x-slot:actions>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.usuarios.store') }}">
            @csrf
            @include('admin.usuarios._form', ['usuario' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar usuario
                </button>
            </div>
        </form>
    </div>
@endsection
