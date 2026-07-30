@extends('layouts.admin')

@section('title', 'Editar pago')
@section('navbar-title', 'Editar pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header title="Editar pago" :description="'Modifica el pago ' . ($pago->numero_comprobante ?? '#' . $pago->id) . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4">
        <form method="POST" action="{{ route('admin.pagos.update', $pago) }}">
            @csrf @method('PUT')
            @include('admin.pagos._form', ['pago' => $pago])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
    @include('admin.pagos.partials.modal-qr')
@endsection