@extends('layouts.admin')

@section('title', 'Nueva cita')
@section('navbar-title', 'Nueva cita')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.citas.index') }}">Citas</a></li>
    <li class="active" aria-current="page">Nueva</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nueva cita"
        description="Agenda una nueva cita en la sucursal seleccionada.">
        <x-slot:actions>
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.citas.store') }}">
            @csrf
            @include('admin.citas._form', ['cita' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cita
                </button>
            </div>
        </form>
    </div>
@endsection
