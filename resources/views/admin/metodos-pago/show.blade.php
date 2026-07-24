@extends('layouts.admin')

@section('title', $metodo->nombre)
@section('navbar-title', $metodo->nombre)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.metodos-pago.index') }}">Métodos de pago</a></li>
    <li class="active" aria-current="page">{{ $metodo->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$metodo->nombre"
        :description="$metodo->descripcion ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.metodos-pago.edit', $metodo) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <dl class="admin-meta">
            <dt>Nombre</dt><dd>{{ $metodo->nombre }}</dd>
            <dt>Descripción</dt><dd>{{ $metodo->descripcion ?? '—' }}</dd>
            <dt>Pagos registrados</dt><dd>{{ $metodo->pagos_count }}</dd>
            <dt>Estado</dt>
            <dd>
                <x-admin.status-badge
                    :tone="$metodo->estado ? 'success' : 'neutral'"
                    :icon="$metodo->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'"
                    :label="$metodo->estado ? 'Activo' : 'Inactivo'" />
            </dd>
        </dl>
    </div>
@endsection
