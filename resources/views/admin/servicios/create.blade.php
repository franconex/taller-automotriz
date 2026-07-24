@extends('layouts.admin')

@section('title', 'Nuevo servicio')
@section('navbar-title', 'Nuevo servicio')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.servicios.index') }}">Servicios</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo servicio"
        description="Crea un nuevo servicio en el catálogo del taller.">
        <x-slot:actions>
            <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.servicios.store') }}">
            @csrf
            @include('admin.servicios._form', ['servicio' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar servicio
                </button>
            </div>
        </form>
    </div>
@endsection
