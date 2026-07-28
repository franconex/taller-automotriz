@extends('layouts.admin')

@section('title', 'Inventario')
@section('navbar-title', 'Inventario')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Inventario</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Inventario"
        description="Repuestos del taller. Stock global sin sucursales.">
        <x-slot:actions>
            <button type="button" class="btn btn-primary" onclick="abrirAgregarRepuesto()">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Agregar repuesto
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="abrirBuscarCodigo()">
                <i class="bi bi-search"></i>
                Buscar por código
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Campo permanente: Barcode to PC / lector USB / manual --}}
    <div class="admin-table-wrap mb-3">
        <div class="p-3 d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <i class="bi bi-upc-scan fs-4 text-primary"></i>
                <span class="fw-semibold small">Escanear o escribir código</span>
            </div>
            <div class="flex-grow-1" style="min-width:200px;">
                <input type="text"
                       id="pi-input"
                       class="form-control"
                       placeholder="Código de barras"
                       autocomplete="off"
                       autofocus>
            </div>
            <div class="d-flex align-items-center gap-1 small text-success flex-shrink-0">
                <span class="spinner-grow spinner-grow-sm" role="status" style="width:8px;height:8px;" aria-hidden="true"></span>
                Lector preparado
            </div>
        </div>
        <div id="pi-resultado" class="px-3 pb-3" style="display:none;"></div>
    </div>

    {{-- Tabs --}}
    <div class="d-flex gap-1 mb-3 flex-wrap">
        <a href="{{ route('admin.inventario.index', request()->except(['tipo', 'page'])) }}"
           class="btn btn-sm px-3 {{ !request('tipo') ? 'btn-dark' : 'btn-outline-secondary' }}">
            Todos
        </a>
        <a href="{{ route('admin.inventario.index', array_merge(request()->except(['tipo', 'page']), ['tipo' => 'repuesto'])) }}"
           class="btn btn-sm px-3 {{ request('tipo') === 'repuesto' ? 'btn-dark' : 'btn-outline-secondary' }}">
            <i class="bi bi-box-seam" aria-hidden="true"></i>
            Repuestos
        </a>
    </div>

    {{-- Filtros --}}
    <x-admin.filters
        :action="route('admin.inventario.index')"
        search-name="q"
        search-placeholder="Buscar por nombre, código, marca o categoría..."
        search-id="inventario-search">
        <x-slot:filters>
            <select name="categoria_id" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $c)
                    <option value="{{ $c->id }}" @selected((string) request('categoria_id') === (string) $c->id)>{{ $c->nombre }}</option>
                @endforeach
            </select>
            <select name="stock" class="form-select" style="max-width:150px;" onchange="this.form.submit()">
                <option value="">Todo stock</option>
                <option value="bajo" @selected(request('stock') === 'bajo')>Stock bajo (< 5)</option>
                <option value="normal" @selected(request('stock') === 'normal')>Stock normal</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($productos->isEmpty() && !request()->has('q') && !request()->has('stock') && !request()->has('tipo'))
         <x-admin.empty-state
             icon="bi-upc-scan"
             title="Aún no hay productos"
             message="Escaneá el código de barras del repuesto para registrarlo." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Inventario">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="d-none d-md-table-cell">Código</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th class="text-end">Stock</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $p)
                        @php
                            $stockTotal = (int) $p->inventarios->sum('cantidad_actual');
                            $stockReservado = (int) $p->inventarios->sum('cantidad_reservada');
                            $disponible = $stockTotal - $stockReservado;
                            $sinStock = $stockTotal === 0;
                            $alerta = !$sinStock && $disponible < 5;
                        @endphp
                        <tr class="{{ $sinStock ? 'table-secondary' : ($alerta ? 'table-danger' : '') }}">
                            <td>
                                <div class="cell-strong">{{ $p->nombre }}</div>
                                <div class="cell-muted small">{{ $p->marca ? $p->marca . ' · ' : '' }}{{ $p->categoria ?? '' }}</div>
                            </td>
                            <td class="d-none d-md-table-cell cell-muted">{{ $p->codigo }}</td>
                            <td class="cell-muted">{{ $p->categoria?->nombre ?? '—' }}</td>
                            <td>
                                <x-admin.status-badge
                                    :tone="$p->tipo === 'herramienta' ? 'warning' : 'primary'"
                                    :icon="$p->tipo === 'herramienta' ? 'bi-gear' : 'bi-box-seam'"
                                    :label="$p->tipo === 'herramienta' ? 'Herramienta' : 'Repuesto'" />
                            </td>
                            <td class="text-end">
                                @if ($sinStock)
                                    <x-admin.status-badge tone="neutral" icon="bi-x-circle-fill" label="0" />
                                @elseif ($alerta)
                                    <x-admin.status-badge tone="danger" icon="bi-exclamation-triangle-fill" :label="$disponible" />
                                @else
                                    <x-admin.status-badge tone="success" icon="bi-check-circle-fill" :label="$disponible" />
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="btn-icon btn-icon--success"
                                            title="Entrada rápida"
                                            onclick="abrirEntradaRapida('{{ $p->codigo_barras ?? $p->codigo }}', '{{ $p->nombre }}')">
                                        <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                    </button>
                                    @if ($alerta || $sinStock)
                                        <a href="{{ route('admin.solicitudes-compra.create', ['repuesto_id' => $p->id]) }}"
                                           class="btn-icon btn-icon--warning" title="Solicitar compra">
                                            <i class="bi bi-cart-plus" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    @php $invId = $p->inventarios->first()?->id; @endphp
                                    <a href="{{ $invId ? route('admin.inventario.show', $invId) : '#' }}"
                                       class="btn-icon {{ !$invId ? 'disabled' : '' }}"
                                       title="Ver movimientos">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.repuestos.edit', $p) }}"
                                       class="btn-icon btn-icon--primary" title="Editar">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron productos." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <x-admin.table-pagination :paginator="$productos" />
        </div>
    @endif
@endsection

{{-- MODAL REGISTRO RÁPIDO DESDE ESCÁNER --}}
<div class="modal fade" id="modalFormEscaner" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.inventario.crear-desde-escaner') }}" id="formEscaner">
                @csrf
                <input type="hidden" name="codigo_barras" id="ef_codigo_barras">
                <input type="hidden" name="tipo" value="repuesto">
                <input type="hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id ?? session('admin_sucursal_id') ?? '' }}">
                <div class="modal-header">
                    <h2 class="modal-title h5">
                        <i class="bi bi-upc-scan text-primary"></i>
                        Registrar producto
                        <small class="d-block text-muted fw-normal small" style="font-size:0.8rem;">
                            Código: <strong id="ef_codigo_label"></strong>
                        </small>
                    </h2>
                    <button type="button" class="btn-close" onclick="tpCerrar('modalFormEscaner')" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nombre <span class="required">*</span></label>
                        <input type="text" name="nombre" id="ef_nombre" class="form-control" required autofocus>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" list="marcasList" placeholder="Castrol, Bosch">
                            <datalist id="marcasList">
                                @foreach ($marcas ?? collect() as $marca)
                                    <option value="{{ $marca }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" class="form-control form-select">
                                <option value="">— Sin categoría —</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-4">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Precio compra Bs</label>
                            <input type="number" step="0.01" name="costo_compra" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Precio venta Bs</label>
                            <input type="number" step="0.01" name="precio_venta" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Especificaciones, compatibilidad..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="tpCerrar('modalFormEscaner')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL BUSCAR POR CÓDIGO --}}
<div class="modal fade" id="modalBuscarCodigo" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5"><i class="bi bi-search"></i> Buscar por código</h2>
                <button type="button" class="btn-close" onclick="tpCerrar('modalBuscarCodigo')"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Código de barras</label>
                    <input type="text" id="bc-input" class="form-control" placeholder="Ej: 7791234567890" autocomplete="off">
                </div>
                <button type="button" class="btn btn-primary w-100" onclick="buscarCodigoModal()">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <div id="bc-resultado" class="mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="tpCerrar('modalBuscarCodigo')">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    (function () {
        'use strict';

        /* ---------------------------------------------------------
           ABRIR MODALES
           --------------------------------------------------------- */
        window.abrirAgregarRepuesto = function () {
            document.getElementById('ef_codigo_barras').value = '';
            document.getElementById('ef_codigo_label').textContent = '—';
            document.getElementById('ef_nombre').value = '';
            tpAbrirModal('modalFormEscaner');
            setTimeout(function () { document.getElementById('ef_nombre')?.focus(); }, 200);
        };

        window.abrirBuscarCodigo = function () {
            document.getElementById('bc-input').value = '';
            document.getElementById('bc-resultado').style.display = 'none';
            tpAbrirModal('modalBuscarCodigo');
            setTimeout(function () { document.getElementById('bc-input').focus(); }, 300);
        };

        /* ---------------------------------------------------------
           BUSCAR DESDE INPUT PERMANENTE (Barcode to PC / manual)
           --------------------------------------------------------- */
        window.buscarCodigoPermanente = function () {
            var codigo = (document.getElementById('pi-input').value || '').trim();
            if (!codigo) return;
            var resEl = document.getElementById('pi-resultado');
            resEl.style.display = '';
            resEl.innerHTML = '<div class="text-center text-muted small py-2"><i class="bi bi-arrow-repeat"></i> Buscando…</div>';

            fetch('{{ route("admin.repuestos.escaner-buscar") }}?codigo=' + encodeURIComponent(codigo))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.encontrado) {
                        mostrarEntrada(resEl, data.repuesto, codigo);
                    } else {
                        resEl.innerHTML =
                            '<div class="alert alert-warning py-3 mb-0 text-center">' +
                                '<p class="mb-2"><strong>' + escHtml(codigo) + '</strong> no está registrado.</p>' +
                                '<button class="btn btn-primary btn-sm" onclick="tpAbrirModalRegistro(\'' + escJs(codigo) + '\')">' +
                                    '<i class="bi bi-plus-lg"></i> Registrar repuesto' +
                                '</button>' +
                                '<button class="btn btn-outline-secondary btn-sm ms-2" onclick="limpiarPanel()">Cerrar</button>' +
                            '</div>';
                    }
                })
                .catch(function () {
                    resEl.innerHTML = '<div class="alert alert-danger py-2 small mb-0">Error al buscar.</div>';
                });
        };

        /* ---------------------------------------------------------
           BUSCAR EN MODAL BUSCAR POR CÓDIGO (solo info)
           --------------------------------------------------------- */
        window.buscarCodigoModal = function () {
            var codigo = (document.getElementById('bc-input').value || '').trim();
            if (!codigo) return;
            var resEl = document.getElementById('bc-resultado');
            resEl.style.display = '';
            resEl.innerHTML = '<div class="text-muted small"><i class="bi bi-arrow-repeat"></i> Buscando…</div>';

            fetch('{{ route("admin.repuestos.escaner-buscar") }}?codigo=' + encodeURIComponent(codigo))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.encontrado) {
                        var r = data.repuesto;
                        resEl.innerHTML =
                            '<div class="card border-success mt-2">' +
                                '<div class="card-header bg-success text-white small fw-bold py-1"><i class="bi bi-check-circle"></i> Encontrado</div>' +
                                '<div class="card-body py-2 small">' +
                                    '<div class="fw-semibold">' + escHtml(r.nombre) + '</div>' +
                                    '<div class="text-muted">' + (r.codigo_barras || r.codigo_interno || '') + '</div>' +
                                    '<div class="mt-1">Stock: <strong>' + r.stock_disponible + '</strong></div>' +
                                    '<a href="{{ url("admin/repuestos") }}/' + r.id + '/edit" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-eye"></i> Ver repuesto</a>' +
                                '</div>' +
                            '</div>';
                    } else {
                        resEl.innerHTML =
                            '<div class="alert alert-warning py-2 small mt-2 mb-0">' +
                                '<strong>' + escHtml(codigo) + '</strong> no registrado. ' +
                                '<button class="btn btn-link btn-sm p-0" onclick="tpCerrar(\'modalBuscarCodigo\'); tpAbrirModalRegistro(\'' + escJs(codigo) + '\')">Registrar</button>' +
                            '</div>';
                    }
                })
                .catch(function () {
                    resEl.innerHTML = '<div class="alert alert-danger py-2 small mt-2 mb-0">Error al buscar.</div>';
                });
        };

        /* ---------------------------------------------------------
           FORMULARIO DE ENTRADA (inline, simplificado)
           --------------------------------------------------------- */
        function mostrarEntrada(el, rep, codigo) {
            var tieneStock = rep.stock_disponible > 0;

            el.innerHTML =
                '<div class="border rounded p-3 bg-light small mt-2">' +
                    '<div class="d-flex justify-content-between align-items-start">' +
                        '<div>' +
                            '<div class="fw-bold">' + escHtml(rep.nombre) + '</div>' +
                            '<div class="text-muted">' + escHtml(codigo) + (rep.marca ? ' · ' + escHtml(rep.marca) : '') + '</div>' +
                            '<div class="mt-1">Stock: <strong class="' + (tieneStock ? 'text-success' : 'text-danger') + '">' + rep.stock_disponible + '</strong></div>' +
                        '</div>' +
                    '</div>' +
                    '<form id="form-entrada-pi" class="row g-2 mt-2">' +
                        '<input type="hidden" name="repuesto_id" value="' + rep.id + '">' +
                        '<input type="hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id ?? session('admin_sucursal_id') ?? '' }}">' +
                        '<div class="col-4">' +
                            '<label class="form-label small">Cantidad</label>' +
                            '<input type="number" name="cantidad" class="form-control form-control-sm" min="1" required>' +
                        '</div>' +
                        '<div class="col-4">' +
                            '<label class="form-label small">Precio Bs</label>' +
                            '<input type="number" step="0.01" name="precio_unitario" class="form-control form-control-sm" min="0">' +
                        '</div>' +
                        '<div class="col-4">' +
                            '<label class="form-label small">Factura</label>' +
                            '<input type="text" name="factura" class="form-control form-control-sm">' +
                        '</div>' +
                        '<div class="col-12"><div id="pi-error" class="text-danger small d-none"></div></div>' +
                        '<div class="col-12 d-flex gap-2 mt-2">' +
                            '<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check2"></i> Confirmar entrada</button>' +
                            '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarPanel()">Cancelar</button>' +
                        '</div>' +
                    '</form>' +
                '</div>';

            document.getElementById('form-entrada-pi')?.addEventListener('submit', function (e) {
                e.preventDefault();
                confirmarEntrada(this);
            });
        }

        function confirmarEntrada(form) {
            var btn = form.querySelector('button[type="submit"]');
            var errorEl = document.getElementById('pi-error');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Guardando…';
            if (errorEl) { errorEl.classList.add('d-none'); errorEl.textContent = ''; }

            var datos = {};
            var fd = new FormData(form);
            for (var pair of fd.entries()) { datos[pair[0]] = pair[1]; }

            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch('{{ route("admin.inventario.entrada") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(function (r) {
                return r.json().then(function (data) { return { status: r.status, data: data }; });
            })
            .then(function (result) {
                if (result.data && result.data.exito) {
                    var resEl = document.getElementById('pi-resultado');
                    resEl.innerHTML = '<div class="text-success fw-semibold small py-2"><i class="bi bi-check-circle"></i> ' + escHtml(result.data.mensaje || 'Entrada registrada') + '</div>';
                    setTimeout(function () { limpiarPanel(); }, 1500);
                } else {
                    var msg = 'Error del servidor.';
                    if (result.data && result.data.message) msg = result.data.message;
                    if (result.data && result.data.errors) {
                        var lista = Object.values(result.data.errors).flat();
                        if (lista.length) msg = lista.join('. ');
                    }
                    if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('d-none'); }
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check2"></i> Confirmar entrada';
                }
            })
            .catch(function () {
                if (errorEl) { errorEl.textContent = 'Error de conexión. Intenta de nuevo.'; errorEl.classList.remove('d-none'); }
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2"></i> Confirmar entrada';
            });
        }

        function limpiarPanel() {
            var el = document.getElementById('pi-resultado');
            if (el) { el.style.display = 'none'; el.innerHTML = ''; }
            var inp = document.getElementById('pi-input');
            if (inp) { inp.value = ''; inp.focus(); }
        }
        window.limpiarPanel = limpiarPanel;

        /* ---------------------------------------------------------
           ENTRADA RÁPIDA DESDE TABLA
           --------------------------------------------------------- */
        window.abrirEntradaRapida = function (codigo, nombre) {
            if (!codigo) return;
            document.getElementById('pi-input').value = codigo;
            document.getElementById('pi-resultado').style.display = '';
            document.getElementById('pi-resultado').innerHTML = '<div class="text-center text-muted small py-2"><i class="bi bi-arrow-repeat"></i> Buscando ' + escHtml(nombre || codigo) + '…</div>';
            window.buscarCodigoPermanente();
        };

        /* ---------------------------------------------------------
           MODALES
           --------------------------------------------------------- */
        function tpAbrirModal(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.add('show');
            el.style.display = 'block';
            el.setAttribute('aria-modal', 'true');
            el.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            if (!document.querySelector('.modal-backdrop')) {
                var bd = document.createElement('div');
                bd.className = 'modal-backdrop fade show';
                document.body.appendChild(bd);
            }
        }

        function tpCerrar(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('show');
            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
            el.removeAttribute('aria-modal');
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        }

        window.tpAbrirModal = tpAbrirModal;
        window.tpCerrar = tpCerrar;

        window.tpToggleTipoEscaner = function () {};

        window.tpAbrirModalRegistro = function (codigo) {
            document.getElementById('ef_codigo_barras').value = codigo;
            document.getElementById('ef_codigo_label').textContent = codigo;
            tpAbrirModal('modalFormEscaner');
            setTimeout(function () { document.getElementById('ef_nombre')?.focus(); }, 200);
        };

        /* ---------------------------------------------------------
           HELPERS
           --------------------------------------------------------- */
        function escHtml(str) { if (!str) return ''; return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
        function escJs(str) { if (!str) return ''; return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'"); }

        /* ---------------------------------------------------------
           ENTER EN INPUTS
           --------------------------------------------------------- */
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('pi-input')?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); buscarCodigoPermanente(); }
            });
            document.getElementById('bc-input')?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); buscarCodigoModal(); }
            });

            /* ---------------------------------------------------------
               AUTOCOMPLETE EN BUSCADOR PRINCIPAL
               --------------------------------------------------------- */
            var searchInput = document.getElementById('inventario-search');
            var sugerenciasDiv = document.createElement('div');
            sugerenciasDiv.style.position = 'absolute';
            sugerenciasDiv.style.zIndex = '1000';
            sugerenciasDiv.style.background = '#fff';
            sugerenciasDiv.style.border = '1px solid #e2e8f0';
            sugerenciasDiv.style.borderRadius = '8px';
            sugerenciasDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
            sugerenciasDiv.style.maxHeight = '300px';
            sugerenciasDiv.style.overflowY = 'auto';
            sugerenciasDiv.style.display = 'none';
            sugerenciasDiv.style.width = '100%';
            sugerenciasDiv.style.marginTop = '2px';

            if (searchInput) {
                searchInput.parentNode.style.position = 'relative';
                searchInput.parentNode.appendChild(sugerenciasDiv);

                var timeoutId = null;

                searchInput.addEventListener('input', function () {
                    var q = this.value.trim();
                    if (q.length < 2) {
                        sugerenciasDiv.style.display = 'none';
                        return;
                    }
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(function () {
                        fetch('{{ route("admin.inventario.buscar.sugerencias") }}?q=' + encodeURIComponent(q))
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (!data || data.length === 0) {
                                    sugerenciasDiv.style.display = 'none';
                                    return;
                                }
                                sugerenciasDiv.innerHTML = '';
                                data.forEach(function (item) {
                                    var a = document.createElement('a');
                                    a.href = '#';
                                    a.style.display = 'block';
                                    a.style.padding = '8px 12px';
                                    a.style.fontSize = '0.85rem';
                                    a.style.textDecoration = 'none';
                                    a.style.color = '#1e293b';
                                    a.style.borderBottom = '1px solid #f1f5f9';
                                    if (item.tipo === 'categoria') {
                                        a.innerHTML = '<span class="badge bg-primary me-1">Cat</span> ' + escHtml(item.text);
                                        a.addEventListener('click', function (e) {
                                            e.preventDefault();
                                            window.location.href = '{{ route("admin.inventario.index") }}?categoria_id=' + item.id;
                                        });
                                    } else {
                                        a.innerHTML = '<strong>' + escHtml(item.nombre) + '</strong> <span class="text-muted">' + escHtml(item.codigo) + '</span> <span class="badge bg-secondary">Stock: ' + item.stock + '</span>';
                                        a.addEventListener('click', function (e) {
                                            e.preventDefault();
                                            searchInput.value = item.nombre;
                                            sugerenciasDiv.style.display = 'none';
                                            window.location.href = '{{ route("admin.inventario.index") }}?q=' + encodeURIComponent(item.nombre);
                                        });
                                    }
                                    a.addEventListener('mouseenter', function () { this.style.background = '#f8fafc'; });
                                    a.addEventListener('mouseleave', function () { this.style.background = ''; });
                                    sugerenciasDiv.appendChild(a);
                                });
                                sugerenciasDiv.style.display = '';
                            })
                            .catch(function () {
                                sugerenciasDiv.style.display = 'none';
                            });
                    }, 300);
                });

                searchInput.addEventListener('blur', function () {
                    setTimeout(function () { sugerenciasDiv.style.display = 'none'; }, 200);
                });

                searchInput.addEventListener('focus', function () {
                    if (sugerenciasDiv.children.length > 0) {
                        sugerenciasDiv.style.display = '';
                    }
                });
            }

            /* Envío AJAX del formulario de registro rápido */
            document.getElementById('formEscaner')?.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Guardando…';

                fetch(this.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(this)
                })
                .then(function (r) {
                    if (!r.ok) {
                        if (r.status === 422) {
                            return r.json().then(function (err) { throw { status: 422, errors: err.errors || err }; });
                        }
                        throw { status: r.status };
                    }
                    return r.json();
                })
                .then(function () {
                    tpCerrar('modalFormEscaner');
                    location.reload();
                })
                .catch(function (err) {
                    btn.disabled = false;
                    btn.innerHTML = 'Guardar producto';
                    if (err.status === 422 && err.errors) {
                        mostrarErroresFormulario('formEscaner', err.errors);
                    }
                });
            });
        });

        window.mostrarErroresFormulario = function (formId, errors) {
            document.querySelectorAll('#' + formId + ' .is-invalid').forEach(function (el) {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('#' + formId + ' .invalid-feedback').forEach(function (el) {
                el.remove();
            });
            Object.keys(errors).forEach(function (field) {
                var input = document.querySelector('#' + formId + ' [name="' + field + '"]');
                if (input) {
                    input.classList.add('is-invalid');
                    var feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = errors[field][0];
                    input.parentNode.appendChild(feedback);
                }
            });
        };
    })();
    </script>
@endpush
