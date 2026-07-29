(function () {
    'use strict';

    const modal = document.getElementById('modalCobrar');
    if (!modal) return;

    const STRIPE_KEY = document.getElementById('stripe-key')?.getAttribute('content');

    const bsModal = new bootstrap.Modal(modal, { backdrop: 'static', keyboard: false });
    const modalBody = document.getElementById('modalCobrarBody');
    const btnConfirmar = document.getElementById('modalCobrarConfirmar');
    const btnCancelar = document.getElementById('modalCobrarCancelar');

    let currentOrdenId = null;
    let currentTotal = 0;
    let currentPagado = 0;

    // Stripe
    let stripe = null;
    let elements = null;
    let cardNumber = null;
    let cardExpiry = null;
    let cardCvc = null;
    let stripeInitialized = false;
    const stripeFields = document.getElementById('stripe-fields');

    // Abrir modal desde botones con data-modal-cobrar
    document.querySelectorAll('[data-modal-cobrar]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            currentOrdenId = this.dataset.ordenId;
            cargarDatosOrden(currentOrdenId);
        });
    });

    function cargarDatosOrden(ordenId) {
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 small text-muted">Cargando datos de la orden...</p></div>';
        if (stripeFields) stripeFields.style.display = 'none';
        btnConfirmar.disabled = true;
        destroyStripe();
        bsModal.show();

        var url = '/admin/pagos/modal-data/' + ordenId;
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            currentTotal = data.total_general || 0;
            currentPagado = 0; // se podría calcular desde data si se agrega
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

        var html = '';
        html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;">';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Cliente</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(c.nombre_completo) + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Vehículo</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(v.placa) + (v.marca ? ' · ' + esc(v.marca) + ' ' + esc(v.modelo || '') : '') + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Orden</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(o.numero_orden) + '</div></div>';
        var pendiente = Math.max(0, currentTotal - currentPagado);
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
        html += '<div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;padding:.6rem 0 0;margin-top:.35rem;border-top:2px solid #0B1D3A;"><span style="color:#0B1D3A;">Total</span><span style="color:#D62828;">Bs ' + fmt(pendiente) + '</span></div>';

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

        // Eventos para cambio de método
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

                // Mostrar u ocultar Stripe
                var nombre = this.dataset.nombre;
                if (nombre === 'Tarjeta' && STRIPE_KEY) {
                    if (stripeFields) stripeFields.style.display = '';
                    initStripe();
                } else {
                    if (stripeFields) stripeFields.style.display = 'none';
                    destroyStripe();
                }
            });
        });

        // Si el método preseleccionado es Tarjeta, iniciar Stripe
        var preseleccionado = modalBody.querySelector('input[name="modal_metodo"]:checked');
        if (preseleccionado && preseleccionado.dataset.nombre === 'Tarjeta' && STRIPE_KEY) {
            if (stripeFields) stripeFields.style.display = '';
            setTimeout(initStripe, 200);
        }
    }

    function initStripe() {
        if (stripeInitialized || !STRIPE_KEY) return;
        stripeInitialized = true;
        stripe = Stripe(STRIPE_KEY);
        elements = stripe.elements({ locale: 'es' });
        var style = { base: { fontSize: '15px', color: '#1F2937' } };
        cardNumber = elements.create('cardNumber', { style: style });
        cardExpiry = elements.create('cardExpiry', { style: style });
        cardCvc = elements.create('cardCvc', { style: style });
        cardNumber.mount('#stripe-card-number');
        cardExpiry.mount('#stripe-card-expiry');
        cardCvc.mount('#stripe-card-cvc');
    }

    function destroyStripe() {
        if (!stripeInitialized) return;
        try { cardNumber?.destroy(); } catch(e) {}
        try { cardExpiry?.destroy(); } catch(e) {}
        try { cardCvc?.destroy(); } catch(e) {}
        stripeInitialized = false;
        stripe = null;
        elements = null;
    }

    // Confirmar pago
    btnConfirmar.addEventListener('click', function () {
        if (!currentOrdenId) return;
        var selected = document.querySelector('input[name="modal_metodo"]:checked');
        if (!selected) { alert('Seleccioná un método de pago.'); return; }

        var nombreMetodo = selected.dataset.nombre;
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

        if (nombreMetodo === 'Tarjeta' && STRIPE_KEY) {
            procesarPagoTarjeta(selected.value);
        } else {
            procesarPagoNormal(selected.value);
        }
    });

    async function procesarPagoTarjeta(metodoPagoId) {
        try {
            if (!stripe || !cardNumber) { alert('Stripe no inicializado.'); btnConfirmar.disabled = false; btnConfirmar.innerHTML = 'Confirmar pago'; return; }

            var pendiente = Math.max(0, currentTotal - currentPagado);
            if (pendiente <= 0) { alert('La orden ya está pagada.'); btnConfirmar.disabled = false; btnConfirmar.innerHTML = 'Confirmar pago'; return; }

            var { error: pmError, paymentMethod } = await stripe.createPaymentMethod({ type: 'card', card: cardNumber });
            if (pmError) { alert(pmError.message); btnConfirmar.disabled = false; btnConfirmar.innerHTML = 'Confirmar pago'; return; }

            var res = await fetch('/admin/pagos/stripe/cobrar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ orden_id: currentOrdenId }),
                credentials: 'same-origin',
            });
            var data = await res.json();

            if (data.ok) {
                mostrarExito(data.message || 'Pago exitoso');
            } else {
                alert(data.message || 'Error al procesar el pago con tarjeta.');
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Confirmar pago';
            }
        } catch (err) {
            alert('Error: ' + err.message);
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Confirmar pago';
        }
    }

    function procesarPagoNormal(metodoPagoId) {
        fetch('/admin/pagos/cobrar-modal', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify({ orden_id: currentOrdenId, metodo_pago_id: metodoPagoId }),
            credentials: 'same-origin',
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                mostrarExito(data.mensaje || 'Pago registrado');
            } else {
                alert(data.mensaje || 'Error al registrar pago');
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Confirmar pago';
            }
        })
        .catch(function (err) {
            alert('Error de conexión: ' + err.message);
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Confirmar pago';
        });
    }

    function mostrarExito(mensaje) {
        modalBody.innerHTML = '<div class="text-center py-4"><i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#2B9348;display:block;margin-bottom:.5rem;"></i><p style="font-weight:700;color:#0B1D3A;margin-bottom:.25rem;">Pago completado</p><p class="small text-muted">' + esc(mensaje) + '</p></div>';
        btnConfirmar.style.display = 'none';
        btnCancelar.textContent = 'Cerrar';
        if (stripeFields) stripeFields.style.display = 'none';
        destroyStripe();
        setTimeout(function () { bsModal.hide(); location.reload(); }, 2000);
    }

    // Reset al cerrar
    modal.addEventListener('hidden.bs.modal', function () {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = 'Confirmar pago';
        btnConfirmar.style.display = '';
        btnCancelar.textContent = 'Cancelar';
        currentOrdenId = null;
        destroyStripe();
        if (stripeFields) stripeFields.style.display = 'none';
    });

    function esc(s) { return String(s || '').replace(/[&<>"']/g, function (c) { return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
    function fmt(n) { return parseFloat(n || 0).toFixed(2); }
})();
