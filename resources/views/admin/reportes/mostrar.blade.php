@extends('layouts.admin')

@section('title', $datos['titulo'])
@section('navbar-title', $datos['titulo'])

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
    <li class="active" aria-current="page">{{ $datos['titulo'] }}</li>
@endsection

@section('content')
    <x-admin.page-header :title="$datos['titulo']" :description="$datos['descripcion']">
        <x-slot:actions>
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
            <a href="{{ route('admin.reportes.pdf', $tipo) }}?desde={{ request('desde') }}&hasta={{ request('hasta') }}" class="btn btn-danger btn-sm"><i class="bi bi-filetype-pdf"></i> PDF</a>
            <a href="{{ route('admin.reportes.csv', $tipo) }}?desde={{ request('desde') }}&hasta={{ request('hasta') }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-card-modern p-4 mb-4">
        <form method="GET" action="{{ route('admin.reportes.mostrar', $tipo) }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-medium mb-1" for="filter-desde">Desde</label>
                <input type="date" id="filter-desde" name="desde" class="form-control form-control-sm" style="min-width:160px;" value="{{ request('desde', $desde?->format('Y-m-d')) }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-medium mb-1" for="filter-hasta">Hasta</label>
                <input type="date" id="filter-hasta" name="hasta" class="form-control form-control-sm" style="min-width:160px;" value="{{ request('hasta', $hasta?->format('Y-m-d')) }}">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('admin.reportes.mostrar', $tipo) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    @if ($tipo === 'ingresos')
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6">
                <div class="admin-card-module text-center" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                    <div class="cell-secondary small fw-semibold">Total recaudado</div>
                    <div class="fw-bold" style="font-size:2rem;color:#16a34a;">Bs. {{ number_format($datos['total'], 2, ',', '.') }}</div>
                    <div class="cell-secondary small">pagos confirmados</div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="admin-card-module text-center" style="background:linear-gradient(135deg,#e8f4fd,#dbeafe);">
                    <div class="cell-secondary small fw-semibold">Cantidad de pagos</div>
                    <div class="fw-bold" style="font-size:2rem;color:#2563eb;">{{ $datos['pagos']->count() }}</div>
                    <div class="cell-secondary small">en el período</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-5">
                <div class="admin-card-modern p-0 overflow-hidden">
                    <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Por método de pago</div>
                    <table class="admin-table">
                        <thead><tr><th>Método</th><th class="text-end">Cantidad</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @foreach ($datos['por_metodo'] as $metodo => $info)
                                <tr>
                                    <td><span class="cell-label"><i class="bi {{ str_contains(strtolower($metodo),'efectivo') ? 'bi-cash-stack' : (str_contains(strtolower($metodo),'tarjeta') ? 'bi-credit-card-2-front' : (str_contains(strtolower($metodo),'qr') ? 'bi-qr-code-scan' : 'bi-coin')) }}"></i> {{ $metodo }}</span></td>
                                    <td class="text-end">{{ $info['cantidad'] }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($info['total'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="admin-card-modern p-0 overflow-hidden">
                    <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Pagos</div>
                    @if ($datos['pagos']->isEmpty())
                        <x-admin.empty-state icon="bi-cash-coin" title="Sin pagos" message="No hay pagos en el período." />
                    @else
                        <table class="admin-table">
                            <thead><tr><th>Fecha</th><th>Orden</th><th>Método</th><th class="text-end">Monto</th></tr></thead>
                            <tbody>
                                @foreach ($datos['pagos'] as $pago)
                                    <tr style="border-left:3px solid #16a34a;">
                                        <td class="cell-secondary small"><i class="bi bi-calendar2" style="font-size:0.7rem;"></i> {{ $pago->fecha_pago?->format('d/m/Y H:i') }}</td>
                                        <td class="fw-semibold">{{ $pago->ordenTrabajo->numero_orden ?? '—' }}</td>
                                        <td>{{ $pago->metodoPago->nombre ?? '—' }}</td>
                                        <td class="text-end fw-bold">{{ number_format((float) $pago->monto, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    @elseif ($tipo === 'ordenes-estado')
        <div class="admin-card-modern p-0 overflow-hidden">
            <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Distribución de órdenes</div>
            <table class="admin-table">
                <thead><tr><th>Estado</th><th class="text-end">Cantidad</th><th class="text-end">Monto total</th></tr></thead>
                <tbody>
                    @forelse ($datos['por_estado'] as $row)
                        @php
                            $estadoColors = ['recibida' => '#2563eb', 'diagnostico' => '#d97706', 'en_proceso' => '#d97706', 'finalizada' => '#16a34a', 'entregada' => '#16a34a', 'anulada' => '#dc2626'];
                            $estadoIcons = ['recibida' => 'bi-inbox-fill', 'diagnostico' => 'bi-search', 'en_proceso' => 'bi-gear-fill', 'finalizada' => 'bi-check-circle-fill', 'entregada' => 'bi-truck', 'anulada' => 'bi-x-circle-fill'];
                            $ec = $estadoColors[$row->estado] ?? '#94a3b8';
                            $ei = $estadoIcons[$row->estado] ?? 'bi-circle';
                        @endphp
                        <tr style="border-left:3px solid {{ $ec }};">
                            <td class="fw-semibold"><i class="bi {{ $ei }} me-1"></i> {{ ucfirst(str_replace('_', ' ', $row->estado)) }}</td>
                            <td class="text-end"><span class="badge" style="background:#e2e8f0;color:#475569;">{{ $row->cantidad }}</span></td>
                            <td class="text-end fw-bold">{{ number_format((float) $row->monto, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0"><x-admin.empty-state icon="bi-clipboard-check" title="Sin datos" message="No hay órdenes para mostrar." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'mecanicos-productividad')
        <div class="admin-card-modern p-0 overflow-hidden">
            <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Productividad</div>
            <table class="admin-table">
                <thead><tr><th>Mecánico</th><th class="text-end">Asignaciones</th><th class="text-end">Finalizadas</th></tr></thead>
                <tbody>
                    @forelse ($datos['mecanicos'] as $m)
                        <tr style="border-left:3px solid #d97706;">
                            <td><span class="cell-label"><i class="bi bi-person-gear"></i> <strong>{{ $m['mecanico'] }}</strong></span></td>
                            <td class="text-end">{{ $m['asignaciones'] }}</td>
                            <td class="text-end"><span class="fw-bold" style="color:#16a34a;">{{ $m['finalizadas'] }}</span> / {{ $m['asignaciones'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0"><x-admin.empty-state icon="bi-tools" title="Sin datos" message="No hay asignaciones en el período." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'stock-critico')
        <div class="admin-card-modern p-0 overflow-hidden">
            <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Repuestos críticos</div>
            <table class="admin-table">
                <thead><tr><th>Repuesto</th><th class="d-none d-md-table-cell">Sucursal</th><th class="text-end">Stock</th><th class="text-end d-none d-md-table-cell">Mínimo</th></tr></thead>
                <tbody>
                    @forelse ($datos['items'] as $it)
                        <tr style="border-left:3px solid #dc2626;">
                            <td>
                                <div class="cell-label"><i class="bi bi-box-seam" style="color:#dc2626;"></i>
                                    <div><strong>{{ $it->repuesto->nombre ?? '—' }}</strong><div class="cell-secondary small">{{ $it->repuesto->codigo ?? '' }}</div></div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell cell-secondary">{{ $it->sucursal->nombre ?? '—' }}</td>
                            <td class="text-end"><span class="fw-bold text-danger">{{ $it->cantidad_actual }}</span></td>
                            <td class="text-end cell-secondary d-none d-md-table-cell">{{ $it->repuesto->stock_minimo ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-0"><x-admin.empty-state icon="bi-boxes" title="Sin alertas" message="No hay repuestos con stock crítico." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'clientes-frecuentes')
        <div class="admin-card-modern p-0 overflow-hidden">
            <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Clientes frecuentes</div>
            <table class="admin-table">
                <thead><tr><th>Cliente</th><th class="text-end">Órdenes</th><th class="text-end">Monto total</th></tr></thead>
                <tbody>
                    @forelse ($datos['clientes'] as $c)
                        <tr style="border-left:3px solid #8b5cf6;">
                            <td><span class="cell-label"><i class="bi bi-person-vcard"></i> <strong>{{ $c['cliente'] }}</strong></span></td>
                            <td class="text-end"><span class="badge" style="background:#e2e8f0;color:#475569;">{{ $c['ordenes'] }}</span></td>
                            <td class="text-end fw-bold">{{ number_format($c['monto_total'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0"><x-admin.empty-state icon="bi-people" title="Sin datos" message="No hay clientes con órdenes en el período." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'servicios-mas-vendidos')
        <div class="admin-card-modern p-0 overflow-hidden">
            <div class="px-4 py-3 fw-bold" style="font-size:0.9rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;">Servicios más vendidos</div>
            <table class="admin-table">
                <thead><tr><th>Servicio</th><th class="d-none d-md-table-cell">Tipo</th><th class="text-end">Veces</th></tr></thead>
                <tbody>
                    @forelse ($datos['servicios'] as $s)
                        <tr style="border-left:3px solid #0ea5e9;">
                            <td><span class="cell-label"><i class="bi bi-gear"></i> <strong>{{ $s['servicio'] }}</strong></span></td>
                            <td class="d-none d-md-table-cell"><span class="badge rounded-pill" style="background:#e8f4fd;color:#2563eb;">{{ $s['tipo'] }}</span></td>
                            <td class="text-end"><span class="fw-bold" style="font-size:1.1rem;">{{ $s['veces'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0"><x-admin.empty-state icon="bi-graph-up" title="Sin datos" message="No hay servicios vendidos en el período." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection