@extends('layouts.admin')

@section('title', $proveedor->nombre_empresa)
@section('navbar-title', $proveedor->nombre_empresa)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.proveedores.index') }}">Proveedores</a></li>
    <li class="active" aria-current="page">{{ $proveedor->nombre_empresa }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$proveedor->nombre_empresa"
        :description="$proveedor->contacto ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.proveedores.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.proveedores.edit', $proveedor) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos</h2>
                <dl class="admin-meta">
                    <dt>Empresa</dt><dd>{{ $proveedor->nombre_empresa }}</dd>
                    <dt>Contacto</dt><dd>{{ $proveedor->contacto ?? '—' }}</dd>
                    <dt>Teléfono</dt><dd>{{ $proveedor->telefono }}</dd>
                    <dt>Email</dt><dd>{{ $proveedor->email ?? '—' }}</dd>
                    <dt>NIT</dt><dd>{{ $proveedor->nit ?? '—' }}</dd>
                    <dt>Dirección</dt><dd>{{ $proveedor->direccion ?? '—' }}</dd>
                    <dt>Tiempo de entrega</dt><dd>{{ $proveedor->tiempo_entrega_dias ? $proveedor->tiempo_entrega_dias . ' días' : '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Repuestos</h2>
                @if ($proveedor->repuestos->isEmpty())
                    <p class="cell-muted small mb-0">Aún no hay repuestos asociados a este proveedor.</p>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach ($proveedor->repuestos as $repuesto)
                            <li class="py-1">
                                <a href="{{ route('admin.repuestos.edit', $repuesto) }}">{{ $repuesto->nombre }}</a>
                                <span class="cell-muted small">({{ $repuesto->codigo }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
