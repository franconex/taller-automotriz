@extends('layouts.admin')

@section('title', 'Editar servicio')
@section('navbar-title', 'Editar servicio')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.servicios.index') }}">Servicios</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header title="Editar servicio" :description="'Modifica los datos del servicio ' . $servicio->nombre . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>
    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.servicios.update', $servicio) }}">
            @csrf @method('PUT')
            @include('admin.servicios._form', ['servicio' => $servicio])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
@endsection