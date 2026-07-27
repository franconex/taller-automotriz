@extends('layouts.admin')

@section('title', 'Nueva orden de trabajo')
@section('navbar-title', 'Nueva orden de trabajo')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li class="active" aria-current="page">Nueva</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nueva orden de trabajo"
        description="Emite una nueva orden para la recepción del vehículo.">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.ordenes.store') }}">
            @csrf
            @include('admin.ordenes._form')

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar orden
                </button>
            </div>
        </form>
    </div>
@endsection
