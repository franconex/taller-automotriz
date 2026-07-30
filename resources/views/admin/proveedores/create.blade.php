@extends('layouts.admin')

@section('title', 'Nuevo proveedor')
@section('navbar-title', 'Nuevo proveedor')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.proveedores.index') }}">Proveedores</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header title="Nuevo proveedor" description="Registra un nuevo proveedor de repuestos.">
        <x-slot:actions>
            <a href="{{ route('admin.proveedores.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>
    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.proveedores.store') }}">
            @csrf
            @include('admin.proveedores._form', ['proveedor' => null, 'codigo_pais' => $codigo_pais ?? '+591', 'telefono_numero' => $telefono_numero ?? ''])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.proveedores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Guardar proveedor</button>
            </div>
        </form>
    </div>
@endsection