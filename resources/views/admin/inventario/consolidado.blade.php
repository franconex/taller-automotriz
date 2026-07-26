@extends('layouts.admin')

@section('title', 'Inventario consolidado')
@section('navbar-title', 'Inventario consolidado')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li class="active" aria-current="page">Consolidado</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Inventario consolidado"
        description="Ve el inventario de todas las sucursales en un solo lugar y mueve stock entre ellas.">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul" aria-hidden="true"></i>
                Lista detallada
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4 mb-4">
        <form method="GET" action="{{ route('admin.inventario.consolidado') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label for="q" class="form-label">Buscar producto por nombre, código o código de barras</label>
                <input type="text" name="q" id="q" class="form-control"
                       value="{{ $busqueda }}" placeholder="Ej: Filtro de aceite, F-001, 7791234...">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    Buscar
                </button>
            </div>
            @if ($busqueda || $repuestoSeleccionado)
                <div class="col-12 col-md-2">
                    <a href="{{ route('admin.inventario.consolidado') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        Limpiar
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if ($repuestoSeleccionado)
        {{-- Producto seleccionado — detalle por sucursal --}}
        <div class="admin-table-wrap p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">{{ $repuestoSeleccionado->nombre }}</h2>
                    <p class="cell-muted small mb-0">
                        Cód: <strong>{{ $repuestoSeleccionado->codigo }}</strong>
                        @if ($repuestoSeleccionado->codigo_barras)
                            · Barras: <strong>{{ $repuestoSeleccionado->codigo_barras }}</strong>
                        @endif
                        @if ($repuestoSeleccionado->categoria)
                            · <span class="badge bg-secondary">{{ $repuestoSeleccionado->categoria }}</span>
                        @endif
                        @if ($repuestoSeleccionado->marca)
                            · <span class="badge bg-info">{{ $repuestoSeleccionado->marca }}</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.inventario.mover', ['repuesto_id' => $repuestoSeleccionado->id]) }}"
                   class="btn btn-warning">
                    <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                    Mover stock entre sucursales
                </a>
            </div>

            @php
                $totalFisico = 0;
                $totalDisponible = 0;
                foreach ($repuestoSeleccionado->inventarios as $inv) {
                    $totalFisico += (int) $inv->cantidad_actual;
                    $totalDisponible += (int) $inv->cantidad_actual - (int) $inv->cantidad_reservada;
                }
            @endphp

            <div class="row g-2 mb-3 text-center">
                <div class="col-4">
                    <div class="cell-muted small">Total físico</div>
                    <div class="fs-4 fw-bold">{{ $totalFisico }}</div>
                </div>
                <div class="col-4">
                    <div class="cell-muted small">Total disponible</div>
                    <div class="fs-4 fw-bold">{{ $totalDisponible }}</div>
                </div>
                <div class="col-4">
                    <div class="cell-muted small">Sucursales con stock</div>
                    <div class="fs-4 fw-bold">{{ $repuestoSeleccionado->inventarios->where('cantidad_actual', '>', 0)->count() }}</div>
                </div>
            </div>

            @if ($repuestoSeleccionado->inventarios->isEmpty())
                <div class="text-center cell-muted py-3">No hay existencias registradas de este producto.</div>
            @else
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sucursal</th>
                                <th class="text-end">Físico</th>
                                <th class="text-end">Reservado</th>
                                <th class="text-end">Disponible</th>
                                <th class="text-end d-none d-md-table-cell">Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repuestoSeleccionado->inventarios as $inv)
                                @php
                                    $disponible = (int) $inv->cantidad_actual - (int) $inv->cantidad_reservada;
                                    $alerta = $disponible > 0 && $disponible < 5;
                                @endphp
                                <tr class="{{ $inv->cantidad_actual == 0 ? 'table-secondary' : ($alerta ? 'table-danger' : '') }}">
                                    <td>{{ $inv->sucursal->nombre ?? '—' }}</td>
                                    <td class="text-end cell-strong">{{ $inv->cantidad_actual }}</td>
                                    <td class="text-end cell-muted">{{ $inv->cantidad_reservada }}</td>
                                    <td class="text-end">
                                        <x-admin.status-badge
                                            :tone="$inv->cantidad_actual == 0 ? 'neutral' : ($alerta ? 'danger' : 'success')"
                                            :label="$disponible" />
                                    </td>
                                    <td class="text-end d-none d-md-table-cell">{{ $minimo ?: '—' }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="$inv->cantidad_actual == 0 ? 'neutral' : ($alerta ? 'danger' : 'success')"
                                            :label="$inv->cantidad_actual == 0 ? 'Sin stock' : ($alerta ? 'Bajo' : 'OK')" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @elseif ($busqueda !== '' && $repuestosParaBusqueda->isEmpty())
        <x-admin.empty-state
            icon="bi-search"
            title="Sin resultados"
            message="No se encontraron productos que coincidan con «{{ $busqueda }}»." />
    @elseif ($busqueda !== '' && $repuestosParaBusqueda->isNotEmpty())
        <div class="admin-table-wrap p-4 mb-4">
            <h2 class="h6 fw-bold mb-3">Resultados de búsqueda ({{ $repuestosParaBusqueda->count() }})</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="d-none d-md-table-cell">Código</th>
                            <th class="d-none d-lg-table-cell">Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($repuestosParaBusqueda as $r)
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $r->nombre }}</div>
                                    <div class="cell-muted small">{{ $r->marca ?? '' }}</div>
                                </td>
                                <td class="d-none d-md-table-cell cell-muted">{{ $r->codigo }}</td>
                                <td class="d-none d-lg-table-cell">
                                    @if ($r->categoria)
                                        <span class="badge bg-secondary">{{ $r->categoria }}</span>
                                    @else — @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.inventario.consolidado', ['repuesto_id' => $r->id]) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        Ver por sucursal
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <h2 class="h5 fw-bold mb-3">Sucursales</h2>
    <div class="row g-3">
        @forelse ($sucursales as $s)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="admin-table-wrap p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="h6 fw-bold mb-1">{{ $s->nombre }}</h3>
                            @if ($s->direccion)
                                <p class="cell-muted small mb-0">{{ $s->direccion }}</p>
                            @endif
                        </div>
                        <x-admin.status-badge
                            :tone="($s->stock_bajo > 0 || $s->sin_stock > 0) ? 'danger' : 'success'"
                            :icon="($s->stock_bajo > 0 || $s->sin_stock > 0) ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'"
                            :label="($s->stock_bajo > 0 || $s->sin_stock > 0) ? 'Atención' : 'OK'" />
                    </div>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-4">
                            <div class="cell-muted small">Unidades</div>
                            <div class="fs-5 fw-bold">{{ $s->total_unidades }}</div>
                        </div>
                        <div class="col-4">
                            <div class="cell-muted small">Stock bajo</div>
                            <div class="fs-5 fw-bold {{ $s->stock_bajo > 0 ? 'text-warning' : '' }}">{{ $s->stock_bajo }}</div>
                        </div>
                        <div class="col-4">
                            <div class="cell-muted small">Valor</div>
                            <div class="fs-5 fw-bold">Bs {{ number_format($s->valor_total, 0) }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @if ($s->sin_stock > 0)
                            <span class="badge bg-secondary">{{ $s->sin_stock }} sin stock</span>
                        @endif
                        @if ($s->total_reservado > 0)
                            <span class="badge bg-primary">{{ $s->total_reservado }} reservados</span>
                        @endif
                    </div>

                    <a href="{{ route('admin.inventario.index', ['sucursal_id' => $s->id]) }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-boxes" aria-hidden="true"></i>
                        Ver inventario de esta sucursal
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-admin.empty-state icon="bi-building" title="Sin sucursales" message="No hay sucursales registradas." />
            </div>
        @endforelse
    </div>
@endsection
