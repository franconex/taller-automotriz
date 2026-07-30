@extends('layouts.admin')

@section('title', 'Editar mecánico')
@section('navbar-title', 'Editar mecánico')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.mecanicos.index') }}">Mecánicos</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar mecánico"
        :description="'Modifica los datos del mecánico ' . ($mecanico->empleado->nombre_completo ?? '') . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.mecanicos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.mecanicos.update', $mecanico) }}">
            @csrf
            @method('PUT')
            @include('admin.mecanicos._form', ['mecanico' => $mecanico])

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.mecanicos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection