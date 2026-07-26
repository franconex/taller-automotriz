@extends('layouts.admin')

@section('title', 'Cotización — ' . ($cotizacion->proveedor->nombre_empresa ?? ''))
@section('navbar-title', 'Cotización')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.index') }}">Solicitudes</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.show', $cotizacion->solicitud_compra_id) }}">{{ $cotizacion->solicitudCompra->numero ?? '' }}</a></li>
    <li class="active" aria-current="page">Cotización</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="$cotizacion->proveedor->nombre_empresa ?? 'Cotización'"
        :description="$cotizacion->solicitudCompra->numero ?? ''">
        <x-slot:actions>
            <a href="{{ route('admin.solicitudes-compra.show', $cotizacion->solicitud_compra_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver a solicitud
            </a>
            @if ($cotizacion->estado === 'respondio')
                <a href="{{ route('admin.cotizaciones.edit', $cotizacion) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    Editar
                </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la cotización</h2>
                <dl class="admin-meta mb-0">
                    <dt>Proveedor</dt>
                    <dd>{{ $cotizacion->proveedor->nombre_empresa ?? '—' }}</dd>
                    <dt>Contacto</dt>
                    <dd>{{ $cotizacion->nombre_contacto ?? '—' }}</dd>
                    <dt>Medio de contacto</dt>
                    <dd>{{ $cotizacion->medio_contacto ?? '—' }}</dd>
                    <dt>Fecha</dt>
                    <dd>{{ $cotizacion->fecha_cotizacion?->format('d/m/Y H:i') }}</dd>
                    <dt>Vencimiento</dt>
                    <dd>{{ $cotizacion->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($cotizacion->estado) {
                                'respondio' => 'info',
                                'seleccionado' => 'success',
                                'no_seleccionado' => 'neutral',
                                'sin_disponibilidad' => 'danger',
                                default => 'neutral',
                            }"
                            :label="match($cotizacion->estado) {
                                'respondio' => 'Respondió',
                                'seleccionado' => 'Seleccionado',
                                'no_seleccionado' => 'No seleccionado',
                                'sin_disponibilidad' => 'Sin disponibilidad',
                                default => $cotizacion->estado,
                            }" />
                    </dd>
                    @if ($cotizacion->archivo)
                        <dt>Archivo</dt>
                        <dd><a href="{{ Storage::url($cotizacion->archivo) }}" target="_blank">Ver archivo</a></dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Productos cotizados</h2>
                <table class="admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cant.</th>
                            <th class="text-end">P. Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $granTotal = 0; @endphp
                        @foreach ($cotizacion->detalles as $det)
                            @php
                                $sub = (float) $det->subtotal;
                                $granTotal += $sub;
                            @endphp
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                                    <div class="cell-muted small">{{ $det->marca_ofrecida ? 'Marca: ' . $det->marca_ofrecida : '' }}</div>
                                </td>
                                <td class="text-end">{{ $det->cantidad_solicitada }}</td>
                                <td class="text-end">Bs {{ number_format((float) $det->precio_unitario, 2) }}</td>
                                <td class="text-end cell-strong">Bs {{ number_format($sub, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Subtotal</th>
                            <th class="text-end">Bs {{ number_format($granTotal, 2) }}</th>
                        </tr>
                        @php
                            $totalEnvio = $cotizacion->detalles->sum('costo_envio');
                            $totalDescuento = $cotizacion->detalles->sum('descuento');
                            $totalImpuesto = $cotizacion->detalles->sum('impuesto');
                        @endphp
                        @if ((float) $totalEnvio > 0)
                            <tr>
                                <td colspan="3" class="text-end cell-muted">Envío</td>
                                <td class="text-end">Bs {{ number_format((float) $totalEnvio, 2) }}</td>
                            </tr>
                        @endif
                        @if ((float) $totalDescuento > 0)
                            <tr>
                                <td colspan="3" class="text-end cell-muted">Descuento</td>
                                <td class="text-end">-Bs {{ number_format((float) $totalDescuento, 2) }}</td>
                            </tr>
                        @endif
                        @if ((float) $totalImpuesto > 0)
                            <tr>
                                <td colspan="3" class="text-end cell-muted">Impuesto</td>
                                <td class="text-end">Bs {{ number_format((float) $totalImpuesto, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">Bs {{ number_format($granTotal + (float) $totalEnvio + (float) $totalImpuesto - (float) $totalDescuento, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if ($cotizacion->observaciones)
        <div class="admin-table-wrap p-4 mb-4">
            <h2 class="h6 fw-bold mb-2">Observaciones</h2>
            <p class="mb-0">{{ $cotizacion->observaciones }}</p>
        </div>
    @endif

    @if ($cotizacion->estado === 'respondio')
        <div class="d-flex gap-2">
            <form action="{{ route('admin.cotizaciones.seleccionar', $cotizacion) }}"
                  method="POST"
                  onsubmit="return confirm('¿Seleccionar esta cotización? Se generará una orden de compra.')">
                @csrf @method('PATCH')
                <div class="d-flex gap-2 align-items-end flex-wrap mb-3">
                    <div>
                        <label for="motivo_seleccion" class="form-label small mb-1">Motivo de selección</label>
                        <select name="motivo_seleccion" id="motivo_seleccion" class="form-select form-select-sm" required style="max-width:220px;">
                            <option value="">Seleccionar...</option>
                            <option value="mejor_precio">Mejor precio</option>
                            <option value="disponibilidad_inmediata">Disponibilidad inmediata</option>
                            <option value="entrega_rapida">Entrega más rápida</option>
                            <option value="mejor_garantia">Mejor garantía</option>
                            <option value="proveedor_confiable">Proveedor confiable</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div id="otroMotivoDiv" style="display:none;">
                        <label for="motivo_seleccion_otro" class="form-label small mb-1">Especificar</label>
                        <input type="text" name="motivo_seleccion_otro" id="motivo_seleccion_otro" class="form-control form-control-sm" maxlength="255" style="max-width:250px;">
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-square" aria-hidden="true"></i>
                        Seleccionar y generar orden
                    </button>
                </div>
            </form>
            <a href="{{ route('admin.cotizaciones.edit', $cotizacion) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar cotización
            </a>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.getElementById('motivo_seleccion')?.addEventListener('change', function() {
    document.getElementById('otroMotivoDiv').style.display = this.value === 'otro' ? '' : 'none';
});
</script>
@endpush
