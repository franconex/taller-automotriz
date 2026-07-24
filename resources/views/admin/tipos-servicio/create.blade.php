@extends('layouts.admin')

@section('title', 'Nuevo tipo de servicio')
@section('navbar-title', 'Nuevo tipo de servicio')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.tipos-servicio.index') }}">Tipos de servicio</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo tipo de servicio"
        description="Crea una nueva categoría para los servicios del taller.">
        <x-slot:actions>
            <a href="{{ route('admin.tipos-servicio.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.tipos-servicio.store') }}">
            @csrf
            @include('admin.tipos-servicio._form', ['tipo' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.tipos-servicio.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar tipo
                </button>
            </div>
        </form>
    </div>
@endsection
