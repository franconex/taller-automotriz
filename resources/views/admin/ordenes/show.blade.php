@extends('layouts.admin')

@section('title', $orden->numero_orden)
@section('navbar-title', $orden->numero_orden)

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.ordenes.index') }}">Órdenes de trabajo</a></li>
    <li class="active" aria-current="page">{{ $orden->numero_orden }}</li>

@include('admin.pagos.partials.modal-tarjeta')
@endsection

@section('content')
    <x-admin.page-header
        :title="$orden->numero_orden"
        :description="optional($orden->cliente)->nombre_completo . ' — ' . ($orden->vehiculo->placa ?? '')">
        <x-slot:actions>
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            @if (!Auth::user()->tieneRol('Mecánico') && Auth::user()->tienePermiso('ordenes.editar'))
            <a href="{{ route('admin.ordenes.edit', $orden) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar
            </a>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Datos de la orden</h2>
                <dl class="admin-meta">
                    <dt>Número</dt><dd>{{ $orden->numero_orden }}</dd>
                    <dt>Cliente</dt>
                    <dd><a href="{{ route('admin.clientes.show', $orden->cliente) }}">{{ $orden->cliente->nombre_completo ?? '—' }}</a></dd>
                    <dt>Vehículo</dt>
                    <dd>
                        @if ($orden->vehiculo)
                            <a href="{{ route('admin.vehiculos.show', $orden->vehiculo) }}">{{ $orden->vehiculo->placa }}</a>
                        @else — @endif
                    </dd>
                    <dt>Sucursal</dt><dd>{{ $orden->sucursal->nombre ?? '—' }}</dd>
                    <dt>Recibido por</dt><dd>{{ $orden->usuarioRecepcion->nombre ?? '—' }}</dd>
                    <dt>Mecánico asignado</dt>
                    <dd>
                        @php $asignacion = $orden->asignaciones->first(); @endphp
                        @if ($asignacion)
                            <strong>{{ $asignacion->mecanico->empleado->nombre_completo ?? '—' }}</strong>
                            <br>
                            <x-admin.status-badge
                                :tone="match($asignacion->estado) {
                                    'pendiente' => 'info',
                                    'en_proceso' => 'warning',
                                    'esperando_repuestos' => 'neutral',
                                    'finalizado' => 'success',
                                    default => 'neutral',
                                }"
                                :label="ucfirst(str_replace('_', ' ', $asignacion->estado))" />
                        @else
                            <span class="text-muted">Sin asignar</span>
                        @endif
                    </dd>
                    <dt>Emisión</dt><dd>{{ $orden->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Inicio</dt><dd>{{ $orden->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Fin</dt><dd>{{ $orden->fecha_fin?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Entrega</dt><dd>{{ $orden->fecha_entrega?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Tiempo estimado</dt><dd>{{ $orden->tiempo_estimado_horas ? $orden->tiempo_estimado_horas . ' h' : '—' }}</dd>
                    <dt>Estado</dt>
                    <dd>
                        <x-admin.status-badge
                            :tone="match($orden->estado) {
                                'recibida' => 'info',
                                'diagnostico' => 'warning',
                                'en_proceso' => 'warning',
                                'finalizada' => 'success',
                                'entregada' => 'success',
                                'anulada' => 'danger',
                                default => 'neutral',
                            }"
                            :icon="match($orden->estado) {
                                'recibida' => 'bi-inbox-fill',
                                'diagnostico' => 'bi-search',
                                'en_proceso' => 'bi-gear-fill',
                                'finalizada' => 'bi-check-circle-fill',
                                'entregada' => 'bi-truck',
                                'anulada' => 'bi-x-circle-fill',
                                default => 'bi-circle',
                            }"
                            :label="ucfirst(str_replace('_', ' ', $orden->estado))" />
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            @php
                $asignacion = $orden->asignaciones->first();
                $esMecanico = Auth::user()->tieneRol('Mecánico');
                $mecanicoPuedeEditar = $esMecanico && $asignacion;
            @endphp

            @if ($mecanicoPuedeEditar)
            {{-- AVANCE DE ESTADO --}}
            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-arrow-up-circle" aria-hidden="true"></i> Avance del trabajo</h2>
                <form method="POST" action="{{ route('admin.ordenes.actualizar-mi-estado', $orden) }}">
                    @csrf @method('PATCH')
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <select name="estado_asignacion" class="form-select" required>
                                <option value="">— Cambiar estado —</option>
                                <option value="pendiente" @if($asignacion->estado==='pendiente') disabled @endif>Pendiente</option>
                                <option value="en_proceso" @if(in_array($asignacion->estado,['en_proceso','esperando_repuestos','finalizado'])) disabled @endif>En proceso</option>
                                <option value="esperando_repuestos" @if(in_array($asignacion->estado,['esperando_repuestos','finalizado'])) disabled @endif>Esperando repuestos</option>
                                <option value="finalizado" @if($asignacion->estado==='finalizado') disabled @endif>Finalizado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-arrow-up-circle" aria-hidden="true"></i> Avanzar</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- SERVICIO RAPIDO --}}
            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-tools" aria-hidden="true"></i> Agregar servicio realizado</h2>
                <form method="POST" action="{{ route('admin.ordenes.servicio-mecanico', $orden) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-7">
                            <input type="text" name="descripcion_servicio" class="form-control form-control-sm" placeholder="Ej: Cambio de frenos" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="costo_servicio" class="form-control form-control-sm" placeholder="Costo Bs." step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm w-100">+</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- FINALIZAR TRABAJO --}}
            @if ($asignacion->estado !== 'finalizado')
            <div class="admin-table-wrap p-4 mb-3" style="border-left:4px solid var(--bs-success,#10b981);">
                <h2 class="h6 fw-bold mb-3 text-success"><i class="bi bi-check2-circle" aria-hidden="true"></i> Finalizar trabajo</h2>
                <form method="POST" action="{{ route('admin.ordenes.finalizar', $orden) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Nota de finalización</label>
                        <textarea name="nota_final" class="form-control" rows="2" maxlength="2000" placeholder="Ej: Trabajo completado. Se reparó el sistema de frenos..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Foto del trabajo terminado (opcional)</label>
                        <input type="file" name="foto_final" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                        Marcar como finalizado y quedar disponible
                    </button>
                </form>
            </div>
            @endif
            @endif

            {{-- OBSERVACIONES --}}
            @if ($mecanicoPuedeEditar)
            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-chat-left-text" aria-hidden="true"></i> Observación técnica</h2>
                <form method="POST" action="{{ route('admin.ordenes.observacion', $orden) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="observaciones" class="form-control" rows="2" maxlength="2000" placeholder="Ej: Se encontró una falla adicional..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-send" aria-hidden="true"></i> Agregar</button>
                </form>
            </div>
            @endif

            {{-- GALERIA DE FOTOS --}}
            @if ($orden->fotos->isNotEmpty())
            <div class="admin-table-wrap p-4 mb-3">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-images" aria-hidden="true"></i> Fotos del trabajo</h2>
                <div class="row g-2">
                    @foreach ($orden->fotos as $foto)
                        <div class="col-4 col-md-3">
                            <a href="{{ asset('storage/' . $foto->ruta) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('storage/' . $foto->ruta) }}" alt="{{ $foto->descripcion ?? 'Foto' }}" class="img-fluid rounded" style="height:100px;width:100%;object-fit:cover;">
                            </a>
                            @if ($foto->descripcion)
                                <small class="text-muted d-block text-center">{{ $foto->descripcion }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="admin-table-wrap p-4">
                <h2 class="h6 fw-bold mb-3">Totales</h2>
                <dl class="admin-meta">
                    <dt>Subtotal servicios</dt>
                    <dd>{{ number_format((float) $orden->subtotal_servicios, 2, ',', '.') }}</dd>
                    <dt>Subtotal repuestos</dt>
                    <dd>{{ number_format((float) $orden->subtotal_repuestos, 2, ',', '.') }}</dd>
                    <dt>Descuento</dt>
                    <dd>{{ number_format((float) $orden->descuento, 2, ',', '.') }}</dd>
                    <dt>Total</dt>
                    <dd><strong>{{ number_format((float) $orden->total_general, 2, ',', '.') }}</strong></dd>
                </dl>
                <hr>
                <h3 class="h6 fw-bold mb-2">Descripción del problema</h3>
                <p class="cell-muted small">{{ $orden->descripcion_problema }}</p>
                @if ($orden->diagnostico_general)
                    <h3 class="h6 fw-bold mt-3 mb-2">Diagnóstico</h3>
                    <p class="cell-muted small">{{ $orden->diagnostico_general }}</p>
                @endif
                @if ($orden->observaciones)
                    <h3 class="h6 fw-bold mt-3 mb-2">Observaciones</h3>
                    <p class="cell-muted small">{{ $orden->observaciones }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-table-wrap mt-3">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 fw-bold mb-0">Repuestos asignados</h2>
            @if ($orden->estado !== 'anulada')
                <a href="{{ route('admin.ordenes.repuestos', $orden) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Agregar repuesto
                </a>
            @endif
        </div>
        <div class="table-responsive">
            <table class="admin-table" aria-label="Repuestos asignados a la orden">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio unit.</th>
                        <th class="text-end">Subtotal</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $detallesRepuestos = $orden->detalles->where('tipo', 'repuesto');
                    @endphp
                    @forelse ($detallesRepuestos as $detalle)
                        @php
                            $sinStock = str_contains((string) $detalle->observaciones, 'SIN STOCK');
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-strong">{{ $detalle->repuesto->nombre ?? $detalle->descripcion }}</div>
                                <div class="cell-muted small">{{ $detalle->repuesto->codigo ?? '' }}</div>
                            </td>
                            <td class="text-end">{{ (int) $detalle->cantidad }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                            <td>
                                @if ($sinStock)
                                    <x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" label="Sin stock" />
                                @else
                                    <x-admin.status-badge tone="success" icon="bi-check-circle-fill" label="Disponible" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center cell-muted py-3">
                                No se han asignado repuestos a esta orden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@include('admin.pagos.partials.modal-tarjeta')
@endsection


