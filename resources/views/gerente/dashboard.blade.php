@extends('layouts.admin')

@section('title', 'Panel de Gerente')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de Bienvenida -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Panel Gerencial, {{ auth()->user()->nombre ?? 'Gerente' }}</h1>
            <p class="text-muted small mb-0">
                {{ \Carbon\Carbon::now()->translatedFormat('l d \d\e F, Y') }} — Supervisión general, reportes y autorizaciones
            </p>
        </div>
        <div>
            <a href="{{ route('gerente.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </a>
        </div>
    </div>

    <!-- Alerta de Bienvenida -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-check fs-4 me-3 text-info"></i>
            <div>
                Bienvenido, <strong>{{ auth()->user()->nombre }}</strong>. Tu rol es <strong>Gerente</strong>. Desde aquí supervisas las operaciones, los reportes financieros y las autorizaciones del taller.
            </div>
        </div>
    </div>

    <!-- Tarjetas de Acceso Rápido -->
    <div class="row g-4">
        
        {{-- Tarjeta 1: Reportes y Métricas --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-graph-up fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">Reportes y Métricas</h4>
                                <p class="text-muted small mb-0">Análisis de rendimiento, ingresos y estadísticas operativas del taller.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr class="my-3 opacity-25">
                        <div class="text-end">
                            <a href="{{ route('admin.reportes.index') }}" class="btn btn-primary px-4 fw-bold shadow-sm">
                                Ver reportes <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Autorizaciones y Pagos --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                                <i class="bi bi-cash-coin fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">Autorizaciones</h4>
                                <p class="text-muted small mb-0">Control de pagos, validación de transacciones y autorizaciones financieras.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr class="my-3 opacity-25">
                        <div class="text-end">
                            <a href="{{ route('admin.pagos.index') }}" class="btn btn-success px-4 fw-bold shadow-sm text-white">
                                Revisar pagos <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 3: Auditoría y Supervisión --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                                <i class="bi bi-journal-text fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">Auditoría</h4>
                                <p class="text-muted small mb-0">Supervisión de registros, movimientos y trazabilidad de acciones del personal.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr class="my-3 opacity-25">
                        <div class="text-end">
                            <a href="{{ route('admin.auditoria.index') }}" class="btn btn-warning px-4 fw-bold shadow-sm text-dark">
                                Ver auditoría <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection