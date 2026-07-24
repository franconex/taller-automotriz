@extends('layouts.admin')

@section('title', 'Nuevo método de pago')
@section('navbar-title', 'Nuevo método de pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.metodos-pago.index') }}">Métodos de pago</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nuevo método de pago"
        description="Registra una nueva forma de pago aceptada por el taller.">
        <x-slot:actions>
            <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.metodos-pago.store') }}">
            @csrf
            @include('admin.metodos-pago._form', ['metodo' => null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar método
                </button>
            </div>
        </form>
    </div>
@endsection
