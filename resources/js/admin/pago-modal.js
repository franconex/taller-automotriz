(function () {
    'use strict';

    const modal = document.getElementById('modalCobrar');
    if (!modal) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const bsModal = new bootstrap.Modal(modal, { backdrop: 'static', keyboard: false });
    const modalBody = document.getElementById('modalCobrarBody');
    const btnConfirmar = document.getElementById('modalCobrarConfirmar');
    const btnCancelar = document.getElementById('modalCobrarCancelar');

    let currentOrdenId = null;
    let currentTotal = 0;
    let currentData = null;

    document.querySelectorAll('[data-modal-cobrar]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            currentOrdenId = this.dataset.ordenId;
            cargarDatosOrden(currentOrdenId);
        });
    });

    function cargarDatosOrden(ordenId) {
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 small text-muted">Cargando datos de la orden...</p></div>';
        btnConfirmar.disabled = true;
        bsModal.show();

        fetch('/admin/pagos/modal-data/' + ordenId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            currentTotal = data.total_general || 0;
            currentData = data;
            renderModal(data);
            btnConfirmar.disabled = false;
        })
        .catch(function (err) {
            modalBody.innerHTML = '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Error: ' + err.message + '</div>';
            btnConfirmar.disabled = true;
        });
    }

    function renderModal(data) {
        var o = data.orden;
        var c = data.cliente || {};
        var v = data.vehiculo || {};
        var serv = data.servicios || [];
        var rep = data.repuestos || [];

        var pendiente = Math.max(0, currentTotal);

        var html = '';
        html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;">';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Cliente</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(c.nombre_completo) + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Vehículo</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(v.placa) + (v.marca ? ' · ' + esc(v.marca) + ' ' + esc(v.modelo || '') : '') + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Orden</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(o.numero_orden) + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Total a cobrar</div><div style="font-weight:700;color:#D62828;font-size:1.1rem;margin-top:.1rem;">Bs ' + fmt(pendiente) + '</div></div>';
        html += '</div>';

        if (serv.length > 0) {
            html += '<div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;padding:.35rem 0 .15rem;">Servicios</div>';
            serv.forEach(function (s) {
                html += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.25rem 0;border-bottom:1px solid #f3f4f6;"><span>' + esc(s.nombre_servicio) + '</span><span style="font-weight:600;">Bs ' + fmt(s.precio_base) + '</span></div>';
            });
        }
        if (rep.length > 0) {
            html += '<div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;padding:.35rem 0 .15rem;margin-top:.35rem;">Repuestos</div>';
            rep.forEach(function (r) {
                var sub = (parseFloat(r.cantidad) || 0) * (parseFloat(r.precio_unitario_snapshot) || 0);
                html += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.25rem 0;border-bottom:1px solid #f3f4f6;"><span>' + esc(r.repuesto?.nombre || '#' + r.repuesto_id) + ' <span style="color:#9CA3AF;">x' + r.cantidad + '</span></span><span style="font-weight:600;">Bs ' + fmt(sub) + '</span></div>';
            });
        }
        if (data.mano_obra > 0) {
            html += '<div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;padding:.35rem 0 .15rem;margin-top:.35rem;">Mano de Obra</div>';
            html += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.25rem 0;border-bottom:1px solid #f3f4f6;"><span>Mano de obra</span><span style="font-weight:600;">Bs ' + fmt(data.mano_obra) + '</span></div>';
        }
        html += '<div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;padding:.6rem 0 0;margin-top:.35rem;border-top:2px solid #0B1D3A;"><span style="color:#0B1D3A;">Total</span><span style="color:#D62828;">Bs ' + fmt(pendiente) + '</span></div>';

        // Datos de factura
        var checkedNit = (c.nit && c.nit.length > 0) ? 'checked' : '';
        html += '<div style="margin-top:1rem;padding:.75rem;border:1px solid #d1d5db;background:#f9fafb;">';
        html += '<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">';
        html += '<input type="checkbox" id="factura-con-nit" ' + checkedNit + ' style="width:16px;height:16px;cursor:pointer;">';
        html += '<label for="factura-con-nit" style="font-size:.8rem;font-weight:600;cursor:pointer;margin:0;">Factura con NIT</label>';
        html += '</div>';
        html += '<div id="factura-nit-fields" style="display:' + (checkedNit ? 'grid' : 'none') + ';grid-template-columns:1fr 1fr;gap:.5rem;">';
        html += '<div><label style="font-size:.7rem;color:#6B7280;display:block;margin-bottom:.15rem;">NIT</label><input type="text" id="factura-nit" class="form-control form-control-sm" value="' + esc(c.nit || '') + '" placeholder="NIT" style="font-size:.8rem;border:1px solid #d1d5db;padding:.3rem .5rem;width:100%;"><div id="nit-verificacion" style="font-size:.7rem;margin-top:.15rem;"></div></div>';
        html += '<div><label style="font-size:.7rem;color:#6B7280;display:block;margin-bottom:.15rem;">Razón Social</label><input type="text" id="factura-razon" class="form-control form-control-sm" value="' + esc(c.nit ? (c.razon_social || c.nombre_completo || '') : '') + '" placeholder="Razón Social" style="font-size:.8rem;border:1px solid #d1d5db;padding:.3rem .5rem;width:100%;"></div>';
        html += '</div></div>';

        // Método de pago
        html += '<div style="margin-top:1rem;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;margin-bottom:.5rem;">Método de pago</div>';
        html += '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">';

        (data.metodos_pago || []).forEach(function (m) {
            var iconos = { 'Efectivo': 'bi-cash-stack', 'QR': 'bi-qr-code', 'Tarjeta': 'bi-credit-card-2-front', 'Transferencia': 'bi-bank' };
            var icono = iconos[m.nombre] || 'bi-wallet2';
            var checked = m.id === (data.metodo_default || data.metodos_pago?.[0]?.id) ? 'checked' : '';
            html += '<label style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:.6rem .25rem;border:1px solid #d1d5db;cursor:pointer;background:' + (checked ? '#0B1D3A' : '#fff') + ';color:' + (checked ? '#fff' : '#0B1D3A') + ';" class="metodo-label" data-nombre-metodo="' + esc(m.nombre) + '">';
            html += '<input type="radio" name="modal_metodo" value="' + m.id + '" ' + checked + ' style="display:none;" data-nombre="' + esc(m.nombre) + '">';
            html += '<i class="' + icono + '" style="font-size:1.3rem;"></i>';
            html += '<span style="font-size:.65rem;font-weight:600;margin-top:.2rem;">' + esc(m.nombre) + '</span>';
            html += '</label>';
        });

        html += '</div></div>';

        modalBody.innerHTML = html;

        // Toggle factura con/sin NIT
        var toggleNit = document.getElementById('factura-con-nit');
        var nitFields = document.getElementById('factura-nit-fields');
        if (toggleNit && nitFields) {
            toggleNit.addEventListener('change', function () {
                nitFields.style.display = this.checked ? 'grid' : 'none';
                if (!this.checked) {
                    var r = document.getElementById('nit-verificacion');
                    if (r) r.innerHTML = '';
                }
            });
        }

        // Verificación NIT contra SIN
        var nitInput = document.getElementById('factura-nit');
        var nitResult = document.getElementById('nit-verificacion');
        if (nitInput && nitResult) {
            var debounceTimer = null;
            nitInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                var val = this.value.replace(/\D/g, '');
                this.value = val;
                if (val.length < 9) { nitResult.innerHTML = ''; return; }
                debounceTimer = setTimeout(function () {
                    nitResult.innerHTML = '<span style="color:#6B7280;"><i class="bi bi-arrow-clockwise"></i> Verificando...</span>';
                    fetch('/admin/verificar-nit?nit=' + val, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    })
                    .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                    .then(function (data) {
                        if (data.valido) {
                            nitResult.innerHTML = '<span style="color:#16A34A;"><i class="bi bi-check-circle-fill"></i> ' + esc(data.razon_social) + ' — NIT habilitado</span>';
                            var razon = document.getElementById('factura-razon');
                            if (razon && !razon.value.trim()) razon.value = data.razon_social || '';
                        } else {
                            nitResult.innerHTML = '<span style="color:#DC2626;"><i class="bi bi-exclamation-circle-fill"></i> ' + esc(data.error || 'NIT inválido') + '</span>';
                        }
                    })
                    .catch(function () {
                        nitResult.innerHTML = '<span style="color:#d97706;"><i class="bi bi-exclamation-triangle-fill"></i> SIN no disponible (verificación manual)</span>';
                    });
                }, 600);
            });
            if (nitInput.value.length >= 9) {
                nitInput.dispatchEvent(new Event('input'));
            }
        }

        modalBody.querySelectorAll('input[name="modal_metodo"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                modalBody.querySelectorAll('.metodo-label').forEach(function (label) {
                    var inp = label.querySelector('input');
                    if (inp && inp.checked) {
                        label.style.background = '#0B1D3A';
                        label.style.color = '#fff';
                    } else {
                        label.style.background = '#fff';
                        label.style.color = '#0B1D3A';
                    }
                });
                var info = document.getElementById('stripe-info');
                if (info) {
                    info.style.display = this.dataset.nombre === 'Tarjeta' ? '' : 'none';
                }
            });
        });
        // Info stripe
        var infoHtml = '<div id="stripe-info" style="display:none;margin-top:.5rem;padding:.5rem;background:#f0f7ff;border:1px solid #b8d4f0;font-size:.75rem;color:#0B1D3A;border-radius:3px;"><i class="bi bi-shield-check me-1"></i>Serás redirigido a Stripe para pagar de forma segura con tu tarjeta.</div>';
        var contenedorMetodos = modalBody.querySelector('div:has(> .metodo-label)') || modalBody.querySelector('[style*="grid-template-columns:1fr 1fr 1fr"]');
        if (contenedorMetodos && contenedorMetodos.parentNode) {
            contenedorMetodos.parentNode.insertAdjacentHTML('beforeend', infoHtml);
        }
        // Mostrar info si tarjeta preseleccionada
        var preseleccionado = modalBody.querySelector('input[name="modal_metodo"]:checked');
        if (preseleccionado && preseleccionado.dataset.nombre === 'Tarjeta') {
            var info = document.getElementById('stripe-info');
            if (info) info.style.display = '';
        }
    }

    btnConfirmar.addEventListener('click', function () {
        if (!currentOrdenId) return;
        var selected = document.querySelector('input[name="modal_metodo"]:checked');
        if (!selected) { alert('Seleccioná un método de pago.'); return; }

        var nombreMetodo = selected.dataset.nombre;
        btnConfirmar.disabled = true;
        if (nombreMetodo === 'Tarjeta') {
            btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Redirigiendo a Stripe...';
        } else {
            btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
        }

        if (nombreMetodo === 'Tarjeta') {
            procesarPagoTarjeta();
        } else {
            procesarPagoNormal(selected.value);
        }
    });

    function obtenerDatosFactura() {
        var conNit = document.getElementById('factura-con-nit')?.checked || false;
        if (!conNit) return { nit: '', razon_social: 'Consumidor Final', con_nit: false };
        var nit = document.getElementById('factura-nit')?.value || '';
        var razon = document.getElementById('factura-razon')?.value || '';
        return { nit: nit, razon_social: razon, con_nit: true };
    }

    async function procesarPagoTarjeta() {
        var factura = obtenerDatosFactura();

        try {
            var res = await fetch('/admin/pagos/stripe/cobrar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ orden_id: currentOrdenId, nit: factura.nit, razon_social: factura.razon_social }),
                credentials: 'same-origin',
            });
            var data = await res.json();

            if (data.ok && data.url) {
                window.location.href = data.url;
            } else {
                alert(data.message || 'Error al procesar el pago con tarjeta.');
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Pagar';
            }
        } catch (err) {
            alert('Error: ' + err.message);
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Pagar';
        }
    }

    function procesarPagoNormal(metodoPagoId) {
        var factura = obtenerDatosFactura();
        var body = { orden_id: currentOrdenId, metodo_pago_id: metodoPagoId, nit: factura.nit, razon_social: factura.razon_social };

        fetch('/admin/pagos/cobrar-modal', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(body),
            credentials: 'same-origin',
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                mostrarExito(data.mensaje || 'Pago registrado', data.factura_url, data.comprobante_numero);
            } else {
                alert(data.mensaje || 'Error al registrar pago');
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Pagar';
            }
        })
        .catch(function (err) {
            alert('Error de conexión: ' + err.message);
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Pagar';
        });
    }

    function mostrarExito(mensaje, facturaUrl, comprobanteNumero) {
        var html = '<div class="text-center py-3"><i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#2B9348;display:block;margin-bottom:.5rem;"></i><p style="font-weight:700;color:#0B1D3A;margin-bottom:.25rem;">Pago completado</p><p class="small text-muted">' + esc(mensaje) + '</p>';
        if (facturaUrl) {
            html += '<a href="' + esc(facturaUrl) + '" target="_blank" class="btn btn-sm mt-2" style="border:1px solid #0B1D3A;border-radius:3px;color:#0B1D3A;font-size:.8rem;"><i class="bi bi-receipt me-1"></i> Ver Factura ' + esc(comprobanteNumero || '') + '</a>';
        }
        html += '</div>';
        modalBody.innerHTML = html;
        btnConfirmar.style.display = 'none';
        btnCancelar.textContent = 'Cerrar';
    }

    modal.addEventListener('hidden.bs.modal', function () {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = 'Pagar';
        btnConfirmar.style.display = '';
        btnCancelar.textContent = 'Cancelar';
        currentOrdenId = null;
        location.reload();
    });

    function esc(s) { return String(s || '').replace(/[&<>"']/g, function (c) { return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
    function fmt(n) { return parseFloat(n || 0).toFixed(2); }
})();
