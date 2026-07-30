@extends('layouts.admin')

@section('title', 'Editar cliente')
@section('navbar-title', 'Editar cliente')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar cliente"
        :description="'Modifica los datos de ' . $cliente->nombre_completo . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}">
            @csrf
            @method('PUT')
            @include('admin.clientes._form', [
                'cliente' => $cliente,
                'codigo_pais' => $codigo_pais ?? '+591',
                'telefono_numero' => $telefono_numero ?? '',
            ])

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection