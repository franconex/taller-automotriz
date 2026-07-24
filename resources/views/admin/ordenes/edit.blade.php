@extends('layouts.admin')

@section('title', 'Editar orden')
@section('navbar-title', 'Editar orden')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar orden"
        :description="'Modifica los datos de la orden ' . $orden->numero_orden . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.ordenes.update', $orden) }}">
            @csrf
            @method('PUT')
            @include('admin.ordenes._form', ['orden' => $orden])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
