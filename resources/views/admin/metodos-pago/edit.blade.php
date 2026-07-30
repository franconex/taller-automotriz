@extends('layouts.admin')

@section('title', 'Editar método de pago')
@section('navbar-title', 'Editar método de pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.metodos-pago.index') }}">Métodos de pago</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header title="Editar método de pago" :description="'Modifica el método de pago ' . $metodo->nombre . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.metodos-pago.update', $metodo) }}">
            @csrf @method('PUT')
            @include('admin.metodos-pago._form', ['metodo' => $metodo])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
@endsection