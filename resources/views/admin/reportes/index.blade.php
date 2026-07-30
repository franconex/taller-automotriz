@extends('layouts.admin')

@section('title', 'Reportes')
@section('navbar-title', 'Reportes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Reportes</li>
@endsection

@section('content')
    <x-admin.page-header title="Reportes" description="Reportes operativos y financieros del taller." />

    <div class="row g-3">
        @php
            $reportes = [
                ['slug' => 'ingresos',                 'icon' => 'bi-cash-coin',       'color' => '#16a34a', 'bg' => '#f0fdf4', 'titulo' => 'Ingresos por período',  'desc' => 'Suma de pagos confirmados en un rango de fechas.'],
                ['slug' => 'ordenes-estado',           'icon' => 'bi-clipboard-check', 'color' => '#2563eb', 'bg' => '#e8f4fd', 'titulo' => 'Órdenes por estado',    'desc' => 'Distribución de órdenes según su estado actual.'],
                ['slug' => 'mecanicos-productividad',  'icon' => 'bi-tools',           'color' => '#d97706', 'bg' => '#fffbeb', 'titulo' => 'Productividad de mecánicos', 'desc' => 'Asignaciones finalizadas por mecánico en el período.'],
                ['slug' => 'stock-critico',            'icon' => 'bi-boxes',           'color' => '#dc2626', 'bg' => '#fef2f2', 'titulo' => 'Stock crítico',         'desc' => 'Repuestos con stock por debajo del mínimo.'],
                ['slug' => 'clientes-frecuentes',      'icon' => 'bi-people',          'color' => '#8b5cf6', 'bg' => '#f3e8ff', 'titulo' => 'Clientes frecuentes',   'desc' => 'Clientes con mayor número de órdenes en el período.'],
                ['slug' => 'servicios-mas-vendidos',   'icon' => 'bi-graph-up',        'color' => '#0ea5e9', 'bg' => '#ecfeff', 'titulo' => 'Servicios más vendidos','desc' => 'Servicios con mayor cantidad registrada.'],
            ];
        @endphp

        @foreach ($reportes as $r)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('admin.reportes.mostrar', $r['slug']) }}" class="admin-card-module text-decoration-none d-block" style="height:100%;transition:all 0.2s;" onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='';this.style.transform=''">
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;border-radius:10px;background:{{ $r['bg'] }};color:{{ $r['color'] }};font-size:1.2rem;">
                            <i class="bi {{ $r['icon'] }}"></i>
                        </span>
                        <div>
                            <span class="d-block fw-bold" style="color:var(--tp-text);font-size:0.95rem;">{{ $r['titulo'] }}</span>
                            <span class="d-block" style="color:var(--tp-text-secondary);font-size:0.82rem;margin-top:2px;">{{ $r['desc'] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection