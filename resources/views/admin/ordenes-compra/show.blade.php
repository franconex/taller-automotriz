@extends('layouts.admin')

@section('title', $orden->numero)
@section('navbar-title', $orden->numero)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes-compra.index') }}">Órdenes de compra</a></li>
    <li class="active" aria-current="page">{{ $orden->numero }}</li>
@endsection

@section('content')
    @php
        $toneEstado = match($orden->estado) {
            'borrador' => 'neutral',
            'pendiente_aprobacion' => 'warning',
            'aprobada' => 'info',
            'enviada' => 'primary',
            'parcialmente_recibida' => 'warning',
            'recibida' => 'success',
            'cancelada' => 'danger',
            default => 'neutral',
        };
        $labelEstado = match($orden->estado) {
            'borrador' => 'Borrador',
            'pendiente_aprobacion' => 'Pendiente de aprobación',
            'aprobada' => 'Aprobada',
            'enviada' => 'Enviada al proveedor',
            'parcialmente_recibida' => 'Parcialmente recibida',
            'recibida' => 'Recibida',
            'cancelada' => 'Cancelada',
            default => $orden->estado,
        };
    @endphp

    <x-admin.page-header
        :title="$orden->numero"
        :description="$orden->proveedor->nombre_empresa ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes-compra.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (in_array($orden->estado, ['pendiente_aprobacion', 'aprobada']))
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enviarModal">
                    <i class="bi bi-send" aria-hidden="true"></i>
                    Marcar como enviada
                </button>
            @endif
            @if (in_array($orden->estado, ['enviada', 'parcialmente_recibida']))
                <a href="{{ route('admin.ordenes-compra.recibir', $orden) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                    Recibir productos
                </a>
            @endif
            @if (! in_array($orden->estado, ['recibida', 'cancelada']))
                <form method="POST" action="{{ route('admin.ordenes-compra.cancelar', $orden) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Cancelar esta orden?')">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        Cancelar
                    </button>
                </form>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la orden</h2>
                <dl class="admin-meta mb-0">
                    <dt>Estado</dt>
                    <dd><x-admin.status-badge :tone="$toneEstado" :label="$labelEstado" /></dd>
                    <dt>Proveedor</dt>
                    <dd>
                        <div class="cell-strong">{{ $orden->proveedor->nombre_empresa ?? '—' }}</div>
                        <div class="cell-muted small">{{ $orden->proveedor->telefono ?? '' }}</div>
                    </dd>
                    <dt>Solicitud origen</dt>
                    <dd>{{ $orden->solicitudCompra->numero ?? '—' }}</dd>
                    <dt>Sucursal</dt>
                    <dd>{{ $orden->sucursal->nombre ?? '—' }}</dd>
                    <dt>Emisión</dt>
                    <dd>{{ $orden->fecha_emision?->format('d/m/Y H:i') }}</dd>
                    <dt>Entrega estimada</dt>
                    <dd>{{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Forma de pago</dt>
                    <dd>{{ $orden->forma_pago ?? '—' }}</dd>
                    <dt>Solicitante</dt>
                    <dd>{{ $orden->usuarioSolicitante->nombre ?? '—' }}</dd>
                    @if ($orden->enviada_medio)
                        <dt>Enviada por</dt>
                        <dd>{{ $orden->enviada_medio }} — {{ $orden->enviada_fecha?->format('d/m/Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Totales</h2>
                <dl class="admin-meta mb-0">
                    <dt>Subtotal</dt>
                    <dd>Bs {{ number_format((float) $orden->subtotal, 2) }}</dd>
                    @if ((float) $orden->costo_envio > 0)
                        <dt>Costo de envío</dt>
                        <dd>Bs {{ number_format((float) $orden->costo_envio, 2) }}</dd>
                    @endif
                    @if ((float) $orden->descuento > 0)
                        <dt>Descuento</dt>
                        <dd>-Bs {{ number_format((float) $orden->descuento, 2) }}</dd>
                    @endif
                    @if ((float) $orden->impuesto > 0)
                        <dt>Impuesto</dt>
                        <dd>Bs {{ number_format((float) $orden->impuesto, 2) }}</dd>
                    @endif
                    <dt class="fw-bold">Total</dt>
                    <dd class="fw-bold text-success">Bs {{ number_format((float) $orden->total, 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="admin-table-wrap mb-4">
        <div class="px-4 py-3 border-bottom">
            <h2 class="h6 fw-bold mb-0">Productos</h2>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Solicitado</th>
                        <th class="text-end">P. Unit.</th>
                        <th class="text-end d-none d-md-table-cell">Dto.</th>
                        <th class="text-end d-none d-md-table-cell">Subtotal</th>
                        <th class="text-end">Recibido</th>
                        <th class="text-end">Aceptado</th>
                        <th class="text-end">Rechazado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orden->detalles as $det)
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                            </td>
                            <td class="text-end">{{ $det->cantidad_solicitada }}</td>
                            <td class="text-end">Bs {{ number_format((float) $det->precio_unitario, 2) }}</td>
                            <td class="text-end d-none d-md-table-cell">Bs {{ number_format((float) $det->descuento, 2) }}</td>
                            <td class="text-end d-none d-md-table-cell">Bs {{ number_format((float) $det->subtotal, 2) }}</td>
                            <td class="text-end cell-strong">{{ $det->cantidad_recibida }}</td>
                            <td class="text-end text-success">{{ $det->cantidad_aceptada }}</td>
                            <td class="text-end {{ $det->cantidad_rechazada > 0 ? 'text-danger' : '' }}">{{ $det->cantidad_rechazada }}</td>
                        </tr>
                        @if ($det->motivo_rechazo)
                            <tr>
                                <td colspan="8" class="small cell-muted ps-5">
                                    Motivo rechazo: {{ $det->motivo_rechazo }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($orden->observaciones)
        <div class="admin-table-wrap p-4 mb-4">
            <h2 class="h6 fw-bold mb-2">Observaciones</h2>
            <p class="mb-0">{{ $orden->observaciones }}</p>
        </div>
    @endif

    {{-- Modal enviar --}}
    <div class="modal fade" id="enviarModal" tabindex="-1" aria-labelledby="enviarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.ordenes-compra.enviar', $orden) }}">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="enviarModalLabel">Marcar orden como enviada</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="enviada_medio" class="form-label">Medio de envío</label>
                            <select name="enviada_medio" id="enviada_medio" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="llamada">Llamada telefónica</option>
                                <option value="correo">Correo electrónico</option>
                                <option value="presencial">Presencial</option>
                                <option value="doc_fisico">Documento físico</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="enviada_fecha" class="form-label">Fecha de envío</label>
                            <input type="date" name="enviada_fecha" id="enviada_fecha" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Marcar como enviada</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
