@extends('layouts.admin')

@section('title', 'Editar cotización')
@section('navbar-title', 'Editar cotización')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.index') }}">Solicitudes</a></li>
    <li><a href="{{ route('admin.solicitudes-compra.show', $cotizacion->solicitud_compra_id) }}">{{ $cotizacion->solicitudCompra->numero ?? '' }}</a></li>
    <li><a href="{{ route('admin.cotizaciones.show', $cotizacion) }}">Cotización</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar cotización"
        :description="$cotizacion->proveedor->nombre_empresa ?? ''" />

    <form method="POST" action="{{ route('admin.cotizaciones.update', $cotizacion) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" name="solicitud_compra_id" value="{{ $cotizacion->solicitud_compra_id }}">

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <x-admin.form-field name="proveedor_id" label="Proveedor" type="select" :value="$cotizacion->proveedor_id" required>
                    @foreach ($proveedores as $p)
                        <option value="{{ $p->id }}" @selected($cotizacion->proveedor_id === $p->id)>{{ $p->nombre_empresa }}</option>
                    @endforeach
                </x-admin.form-field>
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="medio_contacto" label="Medio de contacto" type="select" :value="$cotizacion->medio_contacto" required>
                    <option value="whatsapp" @selected($cotizacion->medio_contacto === 'whatsapp')>WhatsApp manual</option>
                    <option value="llamada" @selected($cotizacion->medio_contacto === 'llamada')>Llamada telefónica</option>
                    <option value="correo" @selected($cotizacion->medio_contacto === 'correo')>Correo electrónico</option>
                    <option value="presencial" @selected($cotizacion->medio_contacto === 'presencial')>Presencial</option>
                    <option value="doc_fisico" @selected($cotizacion->medio_contacto === 'doc_fisico')>Documento físico</option>
                    <option value="otro" @selected($cotizacion->medio_contacto === 'otro')>Otro</option>
                </x-admin.form-field>
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="nombre_contacto" label="Nombre de quien respondió" :value="$cotizacion->nombre_contacto" />
            </div>
            <div class="col-12 col-md-6">
                <x-admin.form-field name="fecha_vencimiento" type="date" label="Vencimiento de la oferta" :value="$cotizacion->fecha_vencimiento?->format('Y-m-d')" />
            </div>
            <div class="col-12">
                <x-admin.form-field name="observaciones" label="Observaciones" type="textarea" :value="$cotizacion->observaciones" />
            </div>
            <div class="col-12">
                <x-admin.form-field name="archivo" type="file" label="Archivo adjunto (PDF, imagen)" help="Máx. 5 MB" />
                @if ($cotizacion->archivo)
                    <div class="small cell-muted">Archivo actual: <a href="{{ Storage::url($cotizacion->archivo) }}" target="_blank">Ver</a></div>
                @endif
            </div>
        </div>

        <div class="admin-table-wrap mb-4">
            <div class="px-4 py-3 border-bottom">
                <h2 class="h6 fw-bold mb-0">Productos cotizados</h2>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Solicitado</th>
                            <th class="text-end">Precio unitario (Bs)</th>
                            <th class="text-end d-none d-md-table-cell">Dto. (Bs)</th>
                            <th class="text-end d-none d-md-table-cell">Imp. (Bs)</th>
                            <th class="text-end d-none d-lg-table-cell">Envío (Bs)</th>
                            <th class="text-end d-none d-lg-table-cell">Entrega</th>
                            <th class="text-end d-none d-lg-table-cell">Garantía</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cotizacion->detalles as $i => $det)
                            <tr>
                                <td>
                                    <div class="cell-strong">{{ $det->repuesto->nombre ?? '—' }}</div>
                                    <input type="hidden" name="productos[{{ $i }}][repuesto_id]" value="{{ $det->repuesto_id }}">
                                    <input type="hidden" name="productos[{{ $i }}][cantidad_solicitada]" value="{{ $det->cantidad_solicitada }}">
                                </td>
                                <td class="text-end">{{ $det->cantidad_solicitada }}</td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][precio_unitario]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.precio_unitario', $det->precio_unitario) }}"
                                           required>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][descuento]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.descuento', $det->descuento) }}">
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][impuesto]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.impuesto', $det->impuesto) }}">
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" step="0.01" min="0"
                                           name="productos[{{ $i }}][costo_envio]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.costo_envio', $det->costo_envio) }}">
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" min="0"
                                           name="productos[{{ $i }}][tiempo_entrega_dias]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.tiempo_entrega_dias', $det->tiempo_entrega_dias) }}"
                                           style="max-width:80px;">
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <input type="number" min="0"
                                           name="productos[{{ $i }}][garantia_dias]"
                                           class="form-control form-control-sm text-end"
                                           value="{{ old('productos.' . $i . '.garantia_dias', $det->garantia_dias) }}"
                                           style="max-width:80px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save" aria-hidden="true"></i>
                Actualizar cotización
            </button>
            <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
