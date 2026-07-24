@extends('layouts.admin')

@section('title', 'Editar método de pago')
@section('navbar-title', 'Editar método de pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.metodos-pago.index') }}">Métodos de pago</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar método de pago"
        :description="'Modifica el método de pago ' . $metodo->nombre . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.metodos-pago.update', $metodo) }}">
            @csrf
            @method('PUT')
            @include('admin.metodos-pago._form', ['metodo' => $metodo])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
