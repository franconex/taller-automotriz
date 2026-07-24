@extends('layouts.admin')

@section('title', 'Reportes')
@section('navbar-title', 'Reportes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Reportes</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Reportes"
        description="Reportes operativos y financieros del taller.">
    </x-admin.page-header>

    <div class="row g-3">
        @php
            $reportes = [
                ['slug' => 'ingresos',                 'icon' => 'bi-cash-coin',       'titulo' => 'Ingresos por período',  'desc' => 'Suma de pagos confirmados en un rango de fechas.'],
                ['slug' => 'ordenes-estado',           'icon' => 'bi-clipboard-check', 'titulo' => 'Órdenes por estado',    'desc' => 'Distribución de órdenes según su estado actual.'],
                ['slug' => 'mecanicos-productividad',  'icon' => 'bi-tools',           'titulo' => 'Productividad de mecánicos', 'desc' => 'Asignaciones finalizadas por mecánico en el período.'],
                ['slug' => 'stock-critico',            'icon' => 'bi-boxes',           'titulo' => 'Stock crítico',         'desc' => 'Repuestos con stock por debajo del mínimo.'],
                ['slug' => 'clientes-frecuentes',      'icon' => 'bi-people',          'titulo' => 'Clientes frecuentes',   'desc' => 'Clientes con mayor número de órdenes en el período.'],
                ['slug' => 'servicios-mas-vendidos',   'icon' => 'bi-graph-up',        'titulo' => 'Servicios más vendidos','desc' => 'Servicios con mayor cantidad registrada.'],
            ];
        @endphp

        @foreach ($reportes as $r)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('admin.reportes.mostrar', $r['slug']) }}" class="d-flex align-items-start gap-3 p-3 admin-table-wrap hover-lift text-decoration-none" style="height:100%;">
                    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;border-radius:8px;background:var(--tp-red-soft);color:var(--tp-red);">
                        <i class="bi {{ $r['icon'] }}" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="d-block fw-semibold" style="color:var(--tp-text);">{{ $r['titulo'] }}</span>
                        <span class="d-block" style="color:var(--tp-text-secondary); font-size:.85rem;">{{ $r['desc'] }}</span>
                    </span>
                </a>
            </div>
        @endforeach
    </div>
@endsection
