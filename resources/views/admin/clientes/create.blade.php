@extends('layouts.admin')

@section('title', 'Nuevo cliente')
@section('navbar-title', 'Nuevo cliente')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo cliente"
        description="Registra un nuevo cliente en el sistema.">
        <x-slot:actions>
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.clientes.store') }}">
            @csrf
            @include('admin.clientes._form', ['cliente' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cliente
                </button>
            </div>
        </form>
    </div>
@endsection
