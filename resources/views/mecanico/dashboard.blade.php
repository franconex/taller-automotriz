@extends('layouts.admin')

@section('title', 'Panel de Mecánico')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de Bienvenida -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Buenos días, {{ auth()->user()->nombre ?? 'Mecánico' }}</h1>
            <p class="text-muted small mb-0">
                {{ \Carbon\Carbon::now()->translatedFormat('l d \d\e F, Y') }} — Sucursal Principal · Mecánico
            </p>
        </div>
        <div>
            <a href="{{ route('mecanico.mis_ordenes') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3 me-3">
                            <i class="bi bi-tools fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Mis Órdenes Asignadas</h4>
                            <p class="text-muted small mb-0">Revisa los vehículos a tu cargo, registra diagnósticos técnicos y asigna repuestos con validación de stock.</p>
                        </div>
                    </div>
                    <hr class="my-3 opacity-25">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('mecanico.mis_ordenes') }}" class="btn btn-danger px-4 fw-bold shadow-sm">
                            Ir a mis órdenes <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection