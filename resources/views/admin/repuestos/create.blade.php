@extends('layouts.admin')

@section('title', 'Nuevo repuesto')
@section('navbar-title', 'Nuevo repuesto')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.repuestos.index') }}">Repuestos</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo repuesto"
        description="Registra un nuevo repuesto en el catálogo.">
        <x-slot:actions>
            <a href="{{ route('admin.repuestos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.repuestos.store') }}">
            @csrf
            @include('admin.repuestos._form', ['repuesto' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.repuestos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar repuesto
                </button>
            </div>
        </form>
    </div>
@endsection
