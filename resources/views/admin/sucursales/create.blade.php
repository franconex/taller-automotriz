@extends('layouts.admin')

@section('title', 'Nueva sucursal')
@section('navbar-title', 'Nueva sucursal')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.sucursales.index') }}">Sucursales</a></li>
    <li class="active" aria-current="page">Nueva</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nueva sucursal"
        description="Registra una nueva ubicación para el taller.">
        <x-slot:actions>
            <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.sucursales.store') }}">
            @csrf
            @include('admin.sucursales._form', [
                'sucursal' => null,
                'codigo_pais' => $codigo_pais ?? '+591',
                'telefono_numero' => $telefono_numero ?? '',
            ])

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.sucursales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar sucursal
                </button>
            </div>
        </form>
    </div>
@endsection
