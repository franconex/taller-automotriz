@extends('layouts.admin')

@section('title', 'Registrar pago')
@section('navbar-title', 'Registrar pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li class="active" aria-current="page">Nuevo</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Registrar pago"
        description="Registra un nuevo pago para una orden de trabajo.">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.pagos.store') }}">
            @csrf
            @include('admin.pagos._form', ['pago' => null, 'ordenId' => $ordenId ?? null])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar pago
                </button>
            </div>
        </form>
    </div>
@endsection
