@extends('layouts.admin')

@section('title', 'Nuevo producto')
@section('navbar-title', 'Nuevo producto')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li class="active" aria-current="page">Nuevo producto</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo producto"
        description="Registrá un repuesto o herramienta en el taller.">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.repuestos.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.repuestos._form', ['repuesto' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar repuesto
                </button>
            </div>
        </form>
    </div>
@endsection
