@extends('layouts.admin')

@section('title', 'Editar cita')
@section('navbar-title', 'Editar cita')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.citas.index') }}">Citas</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar cita"
        description="Modifica los datos de la cita seleccionada.">
        <x-slot:actions>
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.citas.update', $cita) }}">
            @csrf
            @method('PUT')
            @include('admin.citas._form', ['cita' => $cita])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
