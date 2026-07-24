@extends('layouts.admin')

@section('title', 'Editar pago')
@section('navbar-title', 'Editar pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar pago"
        :description="'Modifica el pago ' . ($pago->numero_comprobante ?? '#' . $pago->id) . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.pagos.update', $pago) }}">
            @csrf
            @method('PUT')
            @include('admin.pagos._form', ['pago' => $pago])

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
