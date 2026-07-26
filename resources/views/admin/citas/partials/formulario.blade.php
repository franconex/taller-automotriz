{{-- Modal: formulario de Cita (crear / editar / reprogramar) --}}
<div class="modal fade" id="modal-formulario-cita" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="h5 fw-bold mb-0" id="modal-formulario-titulo">Nueva cita</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formulario-cita" novalidate>
                @csrf
                <input type="hidden" name="cita_id" id="form-cita_id" value="">
                <input type="hidden" name="__accion" id="form-__accion" value="crear">

                <div class="modal-body">
                    <div id="formulario-errores" class="alert alert-danger d-none" role="alert"></div>

                    <div id="reprogramar-fields" class="d-none">
                        <div class="alert alert-warning small mb-3" role="alert" id="reprogramar-info"></div>
                        <div class="citas-form-section">
                            <h3 class="citas-form-section__title">Motivo de reprogramación</h3>
                            <div class="mb-0">
                                <textarea name="motivo_reprogramacion" id="form-motivo_reprogramacion" class="form-control" rows="2" placeholder="Motivo de reprogramación (obligatorio)"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label" for="form-cliente_id">Cliente <span class="required">*</span></label>
                            <div class="input-group">
                                <select name="cliente_id" id="form-cliente_id" class="form-select" required>
                                    <option value="">— Selecciona un cliente —</option>
                                    @foreach ($clientes as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="btn-quick-cliente" title="Agregar cliente rápido">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="form-vehiculo_id">Vehículo <span class="required">*</span></label>
                            <div class="input-group">
                                <select name="vehiculo_id" id="form-vehiculo_id" class="form-select" required>
                                    <option value="">— Selecciona primero un cliente —</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="btn-quick-vehiculo" title="Agregar vehículo rápido">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="form-sucursal_id">Sucursal <span class="required">*</span></label>
                            <select name="sucursal_id" id="form-sucursal_id" class="form-select" required>
                                <option value="">— Selecciona —</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="form-fecha">Fecha <span class="required">*</span></label>
                            <input type="date" name="fecha" id="form-fecha" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="form-hora">Hora <span class="required">*</span></label>
                            <input type="time" name="hora" id="form-hora" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="form-tipo">Tipo <span class="required">*</span></label>
                            <select name="tipo" id="form-tipo" class="form-select" required>
                                <option value="diagnostico">Diagnóstico</option>
                                <option value="mantenimiento">Mantenimiento</option>
                                <option value="reparacion">Reparación</option>
                                <option value="otro">Otro</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="form-servicio_id">Servicio</label>
                            <select name="servicio_id" id="form-servicio_id" class="form-select">
                                <option value="">— Sin servicio específico —</option>
                                @foreach ($servicios as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="form-mecanico_id">Mecánico</label>
                            <select name="mecanico_id" id="form-mecanico_id" class="form-select">
                                <option value="">— Sin mecánico asignado —</option>
                                @foreach ($mecanicos as $m)
                                    <option value="{{ $m->id }}">{{ $m->empleado->nombre_completo }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-4">
                            <label class="form-label" for="form-costo_consulta">Costo consulta</label>
                            <input type="number" step="0.01" min="0" name="costo_consulta" id="form-costo_consulta" class="form-control" value="0">
                            <small class="form-text">0 si no aplica.</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="form-estado">Estado</label>
                            <select name="estado" id="form-estado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="atendida">Atendida</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-4 d-flex align-items-end pb-1">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="deja_vehiculo" value="0">
                                <input class="form-check-input" type="checkbox" name="deja_vehiculo" id="form-deja_vehiculo" value="1">
                                <label class="form-check-label" for="form-deja_vehiculo">¿Dejará el vehículo?</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="form-descripcion_problema">Descripción del problema <span class="required">*</span></label>
                            <textarea name="descripcion_problema" id="form-descripcion_problema" class="form-control" rows="2" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2" aria-hidden="true"></i> Guardar cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/json" id="vehiculos-data">
[
    @foreach ($vehiculos as $v)
        {"id": {{ $v->id }}, "cliente_id": {{ $v->cliente_id }}, "label": "{{ addslashes($v->placa . ' — ' . ($v->cliente->nombre_completo ?? '')) }}"}
        @if (! $loop->last),@endif
    @endforeach
]
</script>

<script type="application/json" id="modelos-data">
[
    @foreach ($modelos as $m)
        {"id": {{ $m->id }}, "marca": "{{ addslashes($m->marcaVehiculo->nombre ?? '') }}", "nombre": "{{ addslashes($m->nombre) }}"}
        @if (! $loop->last),@endif
    @endforeach
]
</script>

{{-- Quick-create Cliente modal --}}
<div class="modal fade" id="modal-quick-cliente" tabindex="-1" aria-labelledby="modal-quick-cliente-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="h5 fw-bold mb-0" id="modal-quick-cliente-titulo">Agregar cliente</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="form-quick-cliente" novalidate>
                @csrf
                <div class="modal-body">
                    <div id="quick-cliente-errores" class="alert alert-danger d-none" role="alert"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="quick-cliente-nombre">Nombre completo <span class="required">*</span></label>
                            <input type="text" name="nombre_completo" id="quick-cliente-nombre" class="form-control" required maxlength="150">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="quick-cliente-ci">Cédula de Identidad</label>
                            <input type="text" name="ci" id="quick-cliente-ci" class="form-control" maxlength="20">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="quick-cliente-telefono">Teléfono <span class="required">*</span></label>
                            <input type="text" name="telefono" id="quick-cliente-telefono" class="form-control" required maxlength="20">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="quick-cliente-email">Correo electrónico</label>
                            <input type="email" name="email" id="quick-cliente-email" class="form-control" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-quick-cliente-submit">
                        <i class="bi bi-check2" aria-hidden="true"></i> Guardar cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick-create Vehículo modal --}}
<div class="modal fade" id="modal-quick-vehiculo" tabindex="-1" aria-labelledby="modal-quick-vehiculo-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="h5 fw-bold mb-0" id="modal-quick-vehiculo-titulo">Agregar vehículo</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="form-quick-vehiculo" novalidate>
                @csrf
                <input type="hidden" name="cliente_id" id="quick-vehiculo-cliente_id" value="">
                <div class="modal-body">
                    <div id="quick-vehiculo-errores" class="alert alert-danger d-none" role="alert"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="quick-vehiculo-marca">Marca <span class="required">*</span></label>
                            <input type="text" name="marca" id="quick-vehiculo-marca" class="form-control" required maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="quick-vehiculo-modelo">Modelo <span class="required">*</span></label>
                            <input type="text" name="modelo" id="quick-vehiculo-modelo" class="form-control" required maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="quick-vehiculo-placa">Placa <span class="required">*</span></label>
                            <input type="text" name="placa" id="quick-vehiculo-placa" class="form-control" required maxlength="20">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="quick-vehiculo-color">Color</label>
                            <input type="text" name="color" id="quick-vehiculo-color" class="form-control" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="quick-vehiculo-anio">Año</label>
                            <input type="number" name="anio" id="quick-vehiculo-anio" class="form-control" min="1900" max="{{ date('Y') + 1 }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-quick-vehiculo-submit">
                        <i class="bi bi-check2" aria-hidden="true"></i> Guardar vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
