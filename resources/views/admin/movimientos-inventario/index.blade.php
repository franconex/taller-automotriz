@extends('layouts.admin')

@section('title', 'Movimientos de inventario')
@section('navbar-title', 'Movimientos de inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Movimientos de inventario</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Movimientos de inventario"
        description="Entradas, salidas y ajustes de stock por sucursal.">
        <x-slot:actions>
            <a href="{{ route('admin.movimientos-inventario.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Nuevo movimiento
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters
        :action="route('admin.movimientos-inventario.index')"
        search-name="q"
        search-placeholder="Buscar por repuesto o motivo">
        <x-slot:filters>
            <select name="tipo" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <optgroup label="Entradas">
                    <option value="entrada_inicial" @selected(request('tipo') === 'entrada_inicial')>Entrada inicial</option>
                    <option value="entrada_compra" @selected(request('tipo') === 'entrada_compra')>Entrada por compra</option>
                    <option value="devolucion" @selected(request('tipo') === 'devolucion')>Devolución</option>
                    <option value="liberacion_reserva" @selected(request('tipo') === 'liberacion_reserva')>Liberación reserva</option>
                </optgroup>
                <optgroup label="Salidas">
                    <option value="salida_orden" @selected(request('tipo') === 'salida_orden')>Salida por orden</option>
                    <option value="consumo" @selected(request('tipo') === 'consumo')>Consumo</option>
                    <option value="dañado" @selected(request('tipo') === 'dañado')>Dañado</option>
                    <option value="vencido" @selected(request('tipo') === 'vencido')>Vencido</option>
                    <option value="perdida" @selected(request('tipo') === 'perdida')>Pérdida</option>
                    <option value="devolucion_proveedor" @selected(request('tipo') === 'devolucion_proveedor')>Devolución proveedor</option>
                    <option value="reserva" @selected(request('tipo') === 'reserva')>Reserva</option>
                </optgroup>
                <optgroup label="Ajustes">
                    <option value="ajuste_positivo" @selected(request('tipo') === 'ajuste_positivo')>Ajuste positivo</option>
                    <option value="ajuste_negativo" @selected(request('tipo') === 'ajuste_negativo')>Ajuste negativo</option>
                </optgroup>
                <optgroup label="Transferencia">
                    <option value="transferencia" @selected(request('tipo') === 'transferencia')>Transferencia</option>
                </optgroup>
            </select>
            <select name="sucursal_id" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">Todas las sucursales</option>
                @foreach (($sucursales ?? collect()) as $s)
                    <option value="{{ $s->id }}" @selected((string) request('sucursal_id') === (string) $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <select name="repuesto_id" class="form-select" style="max-width:240px;" onchange="this.form.submit()">
                <option value="">Todos los repuestos</option>
                @foreach (($repuestos ?? collect()) as $r)
                    <option value="{{ $r->id }}" @selected((string) request('repuesto_id') === (string) $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($movimientos->isEmpty() && ! request()->has('q') && ! request()->has('tipo') && ! request()->has('sucursal_id') && ! request()->has('repuesto_id'))
        <x-admin.empty-state
            icon="bi-arrow-left-right"
            title="Aún no hay movimientos de inventario"
            message="Registra el primer movimiento para iniciar el control de stock."
            :action-label="'Nuevo movimiento'"
            :action-href="route('admin.movimientos-inventario.create')" />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de movimientos de inventario">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Repuesto</th>
                        <th class="d-none d-md-table-cell">Sucursal</th>
                        <th>Tipo</th>
                        <th class="d-none d-lg-table-cell text-end">Cantidad</th>
                        <th class="d-none d-lg-table-cell text-end">Existencias</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tiposLabels = [
                            'entrada_inicial' => ['label' => 'Entrada inicial', 'tone' => 'success', 'icon' => 'bi-plus-circle-fill'],
                            'entrada_compra' => ['label' => 'Entrada compra', 'tone' => 'success', 'icon' => 'bi-plus-circle-fill'],
                            'devolucion' => ['label' => 'Devolución', 'tone' => 'success', 'icon' => 'bi-arrow-return-left'],
                            'liberacion_reserva' => ['label' => 'Lib. reserva', 'tone' => 'info', 'icon' => 'bi-bookmark-x'],
                            'salida_orden' => ['label' => 'Salida orden', 'tone' => 'warning', 'icon' => 'bi-dash-circle-fill'],
                            'consumo' => ['label' => 'Consumo', 'tone' => 'warning', 'icon' => 'bi-wrench'],
                            'dañado' => ['label' => 'Dañado', 'tone' => 'danger', 'icon' => 'bi-x-circle-fill'],
                            'vencido' => ['label' => 'Vencido', 'tone' => 'danger', 'icon' => 'bi-calendar-x'],
                            'perdida' => ['label' => 'Pérdida', 'tone' => 'danger', 'icon' => 'bi-question-circle'],
                            'devolucion_proveedor' => ['label' => 'Dev. proveedor', 'tone' => 'warning', 'icon' => 'bi-box-arrow-up'],
                            'reserva' => ['label' => 'Reserva', 'tone' => 'info', 'icon' => 'bi-bookmark'],
                            'ajuste_positivo' => ['label' => 'Ajuste +', 'tone' => 'success', 'icon' => 'bi-plus'],
                            'ajuste_negativo' => ['label' => 'Ajuste -', 'tone' => 'danger', 'icon' => 'bi-dash'],
                            'transferencia' => ['label' => 'Transferencia', 'tone' => 'primary', 'icon' => 'bi-arrow-left-right'],
                            'entrada' => ['label' => 'Entrada', 'tone' => 'success', 'icon' => 'bi-plus-circle-fill'],
                            'salida' => ['label' => 'Salida', 'tone' => 'warning', 'icon' => 'bi-dash-circle-fill'],
                            'ajuste' => ['label' => 'Ajuste', 'tone' => 'info', 'icon' => 'bi-arrow-left-right'],
                        ];
                    @endphp
                    @forelse ($movimientos as $m)
                        @php
                            $tipoInfo = $tiposLabels[$m->tipo] ?? ['label' => $m->tipo, 'tone' => 'neutral', 'icon' => 'bi-circle'];
                        @endphp
                        <tr style="border-left:3px solid {{ match($m->tipo) { 'entrada','entrada_inicial','entrada_compra','devolucion','liberacion_reserva','ajuste_positivo' => '#16a34a', 'salida','salida_orden','consumo','devolucion_proveedor','ajuste_negativo' => '#d97706', 'dañado','vencido','perdida' => '#dc2626', default => '#2563eb' } }};">
                            <td><span class="cell-label"><i class="bi bi-calendar2" style="font-size:0.75rem;"></i> {{ $m->fecha_movimiento?->format('d/m/Y H:i') ?? '—' }}</span></td>
                            <td>
                                <div class="cell-label">
                                    <i class="bi bi-box-seam"></i>
                                    <div>
                                        <div class="cell-strong">{{ optional($m->inventario->repuesto)->nombre ?? '—' }}</div>
                                        <div class="cell-secondary small">{{ optional($m->inventario->repuesto)->codigo ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell cell-secondary">{{ optional($m->inventario->sucursal)->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge :tone="$tipoInfo['tone']" :icon="$tipoInfo['icon']" :label="$tipoInfo['label']" />
                            </td>
                            <td class="d-none d-lg-table-cell text-end cell-strong">{{ $m->cantidad }}</td>
                            <td class="d-none d-lg-table-cell text-end cell-secondary">{{ $m->existencia_anterior }} → {{ $m->existencia_nueva }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.movimientos-inventario.show', $m) }}" class="btn-icon" title="Ver detalle"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.movimientos-inventario.route', $m) }}" class="btn-icon" title="Ver ruta"><i class="bi bi-map"></i></a>
                                    <form method="POST" action="{{ route('admin.movimientos-inventario.destroy', $m) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn-icon btn-icon--danger" title="Eliminar"><i class="bi bi-trash3"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron movimientos con los filtros aplicados." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-admin.table-pagination :paginator="$movimientos" />
        </div>
    @endif
@endsection
