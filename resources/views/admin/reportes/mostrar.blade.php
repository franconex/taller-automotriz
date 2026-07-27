@extends('layouts.admin')

@section('title', $datos['titulo'])
@section('navbar-title', $datos['titulo'])

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
    <li class="active" aria-current="page">{{ $datos['titulo'] }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$datos['titulo']"
        :description="$datos['descripcion']">
        <x-slot:actions>
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('admin.reportes.pdf', $tipo) }}?desde={{ request('desde') }}&hasta={{ request('hasta') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-filetype-pdf" aria-hidden="true"></i>
                PDF
            </a>
            <a href="{{ route('admin.reportes.csv', $tipo) }}?desde={{ request('desde') }}&hasta={{ request('hasta') }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i>
                CSV
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($tipo === 'ingresos')
        <section class="admin-stats mb-4">
            <div class="admin-stats__item">
                <span class="admin-stats__label">Total recaudado</span>
                <span class="admin-stats__value">{{ number_format($datos['total'], 2, ',', '.') }}</span>
                <span class="admin-stats__hint">pagos confirmados</span>
            </div>
            <div class="admin-stats__item">
                <span class="admin-stats__label">Cantidad de pagos</span>
                <span class="admin-stats__value">{{ $datos['pagos']->count() }}</span>
                <span class="admin-stats__hint">en el período</span>
            </div>
        </section>

        <div class="row g-3">
            <div class="col-12 col-lg-5">
                <div class="admin-table-wrap">
                    <div class="px-4 py-3 border-bottom">
                        <h2 class="h6 fw-bold mb-0">Por método de pago</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos['por_metodo'] as $metodo => $info)
                                <tr>
                                    <td class="cell-strong">{{ $metodo }}</td>
                                    <td class="text-end">{{ $info['cantidad'] }}</td>
                                    <td class="text-end cell-strong">{{ number_format($info['total'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="admin-table-wrap">
                    <div class="px-4 py-3 border-bottom">
                        <h2 class="h6 fw-bold mb-0">Pagos</h2>
                    </div>
                    @if ($datos['pagos']->isEmpty())
                        <x-admin.empty-state icon="bi-cash-coin" title="Sin pagos" message="No hay pagos en el período." />
                    @else
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Orden</th>
                                    <th>Método</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datos['pagos'] as $pago)
                                    <tr>
                                        <td class="cell-muted small">{{ $pago->fecha_pago?->format('d/m/Y H:i') }}</td>
                                        <td class="cell-strong">{{ $pago->ordenTrabajo->numero_orden ?? '—' }}</td>
                                        <td>{{ $pago->metodoPago->nombre ?? '—' }}</td>
                                        <td class="text-end cell-strong">{{ number_format((float) $pago->monto, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    @elseif ($tipo === 'ordenes-estado')
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Monto total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($datos['por_estado'] as $row)
                        <tr>
                            <td class="cell-strong">{{ ucfirst(str_replace('_', ' ', $row->estado)) }}</td>
                            <td class="text-end">{{ $row->cantidad }}</td>
                            <td class="text-end cell-strong">{{ number_format((float) $row->monto, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0">
                            <x-admin.empty-state icon="bi-clipboard-check" title="Sin datos" message="No hay órdenes para mostrar." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'mecanicos-productividad')
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mecánico</th>
                        <th class="text-end">Asignaciones</th>
                        <th class="text-end">Finalizadas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($datos['mecanicos'] as $m)
                        <tr>
                            <td class="cell-strong">{{ $m['mecanico'] }}</td>
                            <td class="text-end">{{ $m['asignaciones'] }}</td>
                            <td class="text-end cell-strong">{{ $m['finalizadas'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0">
                            <x-admin.empty-state icon="bi-tools" title="Sin datos" message="No hay asignaciones en el período." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'stock-critico')
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="d-none d-md-table-cell">Sucursal</th>
                        <th class="text-end">Stock</th>
                        <th class="text-end d-none d-md-table-cell">Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($datos['items'] as $it)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $it->repuesto->nombre ?? '—' }}</div>
                                <div class="cell-muted small">{{ $it->repuesto->codigo ?? '' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $it->sucursal->nombre ?? '—' }}</td>
                            <td class="text-end cell-strong">{{ $it->cantidad_actual }}</td>
                            <td class="text-end cell-muted d-none d-md-table-cell">{{ $it->repuesto->stock_minimo ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-0">
                            <x-admin.empty-state icon="bi-boxes" title="Sin alertas" message="No hay repuestos con stock crítico." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'clientes-frecuentes')
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-end">Órdenes</th>
                        <th class="text-end">Monto total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($datos['clientes'] as $c)
                        <tr>
                            <td class="cell-strong">{{ $c['cliente'] }}</td>
                            <td class="text-end">{{ $c['ordenes'] }}</td>
                            <td class="text-end cell-strong">{{ number_format($c['monto_total'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0">
                            <x-admin.empty-state icon="bi-people" title="Sin datos" message="No hay clientes con órdenes en el período." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif ($tipo === 'servicios-mas-vendidos')
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th class="d-none d-md-table-cell">Tipo</th>
                        <th class="text-end">Veces</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($datos['servicios'] as $s)
                        <tr>
                            <td class="cell-strong">{{ $s['servicio'] }}</td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $s['tipo'] }}</td>
                            <td class="text-end cell-strong">{{ $s['veces'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-0">
                            <x-admin.empty-state icon="bi-graph-up" title="Sin datos" message="No hay servicios vendidos en el período." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
