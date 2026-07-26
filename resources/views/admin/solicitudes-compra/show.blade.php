@extends('layouts.admin')

@section('title', $solicitud->numero)
@section('navbar-title', $solicitud->numero)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.index') }}">Solicitudes de compra</a></li>
    <li class="active" aria-current="page">{{ $solicitud->numero }}</li>
@endsection

@section('content')
    @php
        $toneEstado = match($solicitud->estado) {
            'pendiente' => 'warning',
            'aprobada' => 'success',
            'rechazada' => 'danger',
            default => 'neutral',
        };
        $iconEstado = match($solicitud->estado) {
            'pendiente' => 'bi-clock',
            'aprobada' => 'bi-check-circle-fill',
            'rechazada' => 'bi-x-circle-fill',
            default => 'bi-question-circle',
        };
        $labelEstado = match($solicitud->estado) {
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            default => $solicitud->estado,
        };
        $tonePrioridad = match($solicitud->prioridad) {
            'alta' => 'danger',
            'media' => 'warning',
            'baja' => 'info',
            default => 'neutral',
        };
    @endphp

    <x-admin.page-header
        :title="$solicitud->numero"
        :description="$solicitud->sucursal->nombre ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.solicitudes-compra.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if ($solicitud->estado === 'pendiente')
                <form method="POST" action="{{ route('admin.solicitudes-compra.aprobar', $solicitud) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Aprobar esta solicitud?')">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                        Aprobar
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rechazarModal">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                    Rechazar
                </button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <dl class="admin-meta mb-0">
                    <dt>Estado</dt>
                    <dd><x-admin.status-badge :tone="$toneEstado" :icon="$iconEstado" :label="$labelEstado" /></dd>
                    <dt>Prioridad</dt>
                    <dd><x-admin.status-badge :tone="$tonePrioridad" :label="ucfirst($solicitud->prioridad)" /></dd>
                    <dt>Fecha</dt>
                    <dd>{{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') }}</dd>
                    <dt>Solicitante</dt>
                    <dd>{{ $solicitud->usuarioSolicitante->nombre ?? '—' }}</dd>
                    @if ($solicitud->usuarioAutoriza)
                        <dt>Autorizó</dt>
                        <dd>{{ $solicitud->usuarioAutoriza->nombre }} ({{ $solicitud->fecha_aprobacion?->format('d/m/Y H:i') }})</dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Productos solicitados</h2>
                <table class="admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Mínimo</th>
                            <th class="text-end">Solicitado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($solicitud->detalles as $det)
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                                    <div class="cell-muted small">{{ $det->repuesto->codigo ?? '' }}</div>
                                </td>
                                <td class="text-end">{{ $det->stock_actual }}</td>
                                <td class="text-end">{{ $det->stock_minimo }}</td>
                                <td class="text-end cell-strong">{{ $det->cantidad_solicitada }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($solicitud->estado !== 'rechazada')
        <div class="admin-table-wrap mb-4">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h6 fw-bold mb-0">Proveedores</h2>
                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#proveedoresCollapse">
                    <i class="bi bi-truck" aria-hidden="true"></i>
                    Ver proveedores compatibles
                </button>
            </div>
            <div class="collapse" id="proveedoresCollapse">
                <div class="p-4">
                    @if ($proveedoresCompatibles['directos']->isEmpty() && $proveedoresCompatibles['sugeridos']->isEmpty())
                        <p class="cell-muted mb-0">No hay proveedores activos registrados.</p>
                    @else
                        @if ($proveedoresCompatibles['directos']->isNotEmpty())
                            <h3 class="h6 fw-bold mb-2">Proveedores con productos relacionados</h3>
                            <div class="table-responsive mb-4">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            <th>Contacto</th>
                                            <th>Teléfono</th>
                                            <th>Productos relacionados</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proveedoresCompatibles['directos'] as $prov)
                                            <tr>
                                                <td>
                                                    <div class="cell-strong">{{ $prov->nombre_empresa }}</div>
                                                    <div class="cell-muted small">{{ $prov->email ?? '—' }}</div>
                                                </td>
                                                <td>{{ $prov->contacto ?? '—' }}</td>
                                                <td class="cell-muted">{{ $prov->telefono }}</td>
                                                <td>{{ $prov->repuestos_count }}</td>
                                                <td>
                                                    <div class="row-actions">
                                                        <a href="https://wa.me/591{{ preg_replace('/[^0-9]/', '', $prov->telefono) }}?text={{ urlencode('Hola, somos de Taller Pro. Necesitamos cotizar los siguientes productos para su posterior compra.') }}"
                                                           target="_blank"
                                                           class="btn-icon"
                                                           title="Abrir WhatsApp"
                                                           aria-label="Abrir WhatsApp">
                                                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="tel:{{ $prov->telefono }}"
                                                           class="btn-icon"
                                                           title="Llamar"
                                                           aria-label="Llamar">
                                                            <i class="bi bi-telephone" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.create', ['solicitud_compra_id' => $solicitud->id, 'proveedor_id' => $prov->id]) }}"
                                                           class="btn-icon btn-icon--primary"
                                                           title="Registrar cotización"
                                                           aria-label="Registrar cotización">
                                                            <i class="bi bi-file-text" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if ($proveedoresCompatibles['sugeridos']->isNotEmpty())
                            <h3 class="h6 fw-bold mb-2">Otros proveedores activos</h3>
                            <div class="table-responsive">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            <th>Contacto</th>
                                            <th>Teléfono</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proveedoresCompatibles['sugeridos'] as $prov)
                                            <tr>
                                                <td>
                                                    <div class="cell-strong">{{ $prov->nombre_empresa }}</div>
                                                    <div class="cell-muted small">{{ $prov->email ?? '—' }}</div>
                                                </td>
                                                <td>{{ $prov->contacto ?? '—' }}</td>
                                                <td class="cell-muted">{{ $prov->telefono }}</td>
                                                <td>
                                                    <div class="row-actions">
                                                        <a href="https://wa.me/591{{ preg_replace('/[^0-9]/', '', $prov->telefono) }}?text={{ urlencode('Hola, somos de Taller Pro. Necesitamos cotizar productos.') }}"
                                                           target="_blank"
                                                           class="btn-icon"
                                                           title="Abrir WhatsApp"
                                                           aria-label="Abrir WhatsApp">
                                                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="tel:{{ $prov->telefono }}"
                                                           class="btn-icon"
                                                           title="Llamar"
                                                           aria-label="Llamar">
                                                            <i class="bi bi-telephone" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.create', ['solicitud_compra_id' => $solicitud->id, 'proveedor_id' => $prov->id]) }}"
                                                           class="btn-icon btn-icon--primary"
                                                           title="Registrar cotización"
                                                           aria-label="Registrar cotización">
                                                            <i class="bi bi-file-text" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('admin.cotizaciones.create', ['solicitud_compra_id' => $solicitud->id]) }}"
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                Registrar cotización (otro proveedor)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="admin-table-wrap mb-4">
            <div class="px-4 py-3 border-bottom">
                <h2 class="h6 fw-bold mb-0">Cotizaciones recibidas</h2>
            </div>
            @if ($solicitud->cotizaciones->isEmpty())
                <div class="p-4 text-center cell-muted">
                    Aún no se registraron cotizaciones.
                    <div class="mt-2">
                        <a href="{{ route('admin.cotizaciones.create', ['solicitud_compra_id' => $solicitud->id]) }}"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                            Registrar primera cotización
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Contacto</th>
                                <th>Medio</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th class="col-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $respondidas = $solicitud->cotizaciones->where('estado', 'respondio');
                            @endphp
                            @foreach ($solicitud->cotizaciones as $cot)
                                @php
                                    $totalCot = 0;
                                    foreach ($cot->detalles as $d) {
                                        $totalCot += (float) $d->subtotal + (float) $d->costo_envio;
                                    }
                                    $toneCot = match($cot->estado) {
                                        'respondio' => 'info',
                                        'seleccionado' => 'success',
                                        'no_seleccionado' => 'neutral',
                                        'sin_disponibilidad' => 'danger',
                                        'no_respondio' => 'warning',
                                        default => 'neutral',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="cell-strong">{{ $cot->proveedor->nombre_empresa ?? '—' }}</div>
                                    </td>
                                    <td class="cell-muted">{{ $cot->nombre_contacto ?? '—' }}</td>
                                    <td>
                                        <x-admin.status-badge
                                            :tone="match($cot->medio_contacto) {
                                                'whatsapp' => 'success',
                                                'llamada' => 'info',
                                                'correo' => 'primary',
                                                'presencial' => 'warning',
                                                default => 'neutral',
                                            }"
                                            :label="match($cot->medio_contacto) {
                                                'whatsapp' => 'WhatsApp',
                                                'llamada' => 'Llamada',
                                                'correo' => 'Correo',
                                                'presencial' => 'Presencial',
                                                'doc_fisico' => 'Doc. físico',
                                                'otro' => 'Otro',
                                                default => $cot->medio_contacto,
                                            }" />
                                    </td>
                                    <td class="text-end cell-strong">Bs {{ number_format($totalCot, 2) }}</td>
                                    <td>
                                        <x-admin.status-badge :tone="$toneCot" :label="match($cot->estado) {
                                            'respondio' => 'Respondió',
                                            'seleccionado' => 'Seleccionado',
                                            'no_seleccionado' => 'No seleccionado',
                                            'sin_disponibilidad' => 'Sin stock',
                                            'no_respondio' => 'No respondió',
                                            'pendiente' => 'Pendiente',
                                            default => $cot->estado,
                                        }" />
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <a href="{{ route('admin.cotizaciones.show', $cot) }}"
                                               class="btn-icon"
                                               title="Ver detalle"
                                               aria-label="Ver detalle">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </a>
                                            @if ($cot->estado === 'respondio')
                                                <form action="{{ route('admin.cotizaciones.seleccionar', $cot) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Seleccionar esta cotización? Se generará una orden de compra.')">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="motivo_seleccion" value="mejor_precio">
                                                    <button type="submit"
                                                            class="btn-icon btn-icon--success"
                                                            title="Seleccionar"
                                                            aria-label="Seleccionar">
                                                        <i class="bi bi-check2-square" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($respondidas->count() >= 2)
                    <div class="px-4 py-3 border-top">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#comparativaCollapse">
                            <i class="bi bi-bar-chart" aria-hidden="true"></i>
                            Comparar cotizaciones ({{ $respondidas->count() }})
                        </button>
                        <div class="collapse mt-3" id="comparativaCollapse">
                            <div class="table-responsive">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            @foreach ($solicitud->detalles as $det)
                                                <th class="text-end">{{ $det->repuesto->nombre ?? '—' }}</th>
                                            @endforeach
                                            <th class="text-end">Envío</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">Entrega</th>
                                            <th>Garantía</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($respondidas as $cot)
                                            @php
                                                $totalCot = 0;
                                                $totalEnvio = 0;
                                                $entregaMin = null;
                                                $garantiaMax = null;
                                                $preciosPorRepuesto = [];
                                                foreach ($cot->detalles as $d) {
                                                    $preciosPorRepuesto[$d->repuesto_id] = $d;
                                                    $totalCot += (float) $d->subtotal;
                                                    $totalEnvio += (float) $d->costo_envio;
                                                    if ($d->tiempo_entrega_dias && (!$entregaMin || $d->tiempo_entrega_dias < $entregaMin)) {
                                                        $entregaMin = $d->tiempo_entrega_dias;
                                                    }
                                                    if ($d->garantia_dias && (!$garantiaMax || $d->garantia_dias > $garantiaMax)) {
                                                        $garantiaMax = $d->garantia_dias;
                                                    }
                                                }
                                                $granTotal = $totalCot + $totalEnvio;
                                            @endphp
                                            <tr>
                                                <td class="cell-strong">{{ $cot->proveedor->nombre_empresa }}</td>
                                                @foreach ($solicitud->detalles as $det)
                                                    <td class="text-end">
                                                        @php
                                                            $precioDet = $preciosPorRepuesto[$det->repuesto_id]->precio_unitario ?? null;
                                                        @endphp
                                                        {{ $precioDet ? 'Bs ' . number_format($precioDet, 2) : '—' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-end">Bs {{ number_format($totalEnvio, 2) }}</td>
                                                <td class="text-end cell-strong">Bs {{ number_format($granTotal, 2) }}</td>
                                                <td class="text-end">{{ $entregaMin ? $entregaMin . ' día(s)' : '—' }}</td>
                                                <td>{{ $garantiaMax ? $garantiaMax . ' día(s)' : '—' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.cotizaciones.seleccionar', $cot) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('¿Seleccionar esta cotización?')">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="motivo_seleccion" value="mejor_precio">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            Seleccionar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Modal Rechazar --}}
    <div class="modal fade" id="rechazarModal" tabindex="-1" aria-labelledby="rechazarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.solicitudes-compra.rechazar', $solicitud) }}">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="rechazarModalLabel">Rechazar solicitud</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo del rechazo</label>
                            <textarea id="motivo" name="motivo" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Rechazar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
