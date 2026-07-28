(function () {
    'use strict';

    const modal = document.getElementById('modalCobrar');
    if (!modal) return;

    const bsModal = new bootstrap.Modal(modal, { backdrop: 'static', keyboard: false });
    const modalBody = document.getElementById('modalCobrarBody');
    const btnConfirmar = document.getElementById('modalCobrarConfirmar');
    const btnCancelar = document.getElementById('modalCobrarCancelar');
    const metodoRadios = document.querySelectorAll('input[name="modal_metodo"]');
    const totalSpan = document.getElementById('modalCobrarTotal');

    let currentOrdenId = null;
    let currentTotal = 0;

    // Abrir modal desde botones con data-orden-id
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

        var url = '/admin/pagos/modal-data/' + ordenId;
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            currentTotal = data.total_general || 0;
            renderModal(data);
            btnConfirmar.disabled = false;
        })
        .catch(function (err) {
            modalBody.innerHTML = '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Error al cargar datos: ' + err.message + '</div>';
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

        // Datos del cliente
        html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;">';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Cliente</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(c.nombre_completo) + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Vehículo</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(v.placa) + (v.marca ? ' · ' + esc(v.marca) + ' ' + esc(v.modelo || '') : '') + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Orden</div><div style="font-weight:600;color:#0B1D3A;font-size:.9rem;margin-top:.1rem;">' + esc(o.numero_orden) + '</div></div>';
        html += '<div style="padding:.5rem .75rem;background:#f9fafb;border:1px solid #e5e7eb;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;">Total a cobrar</div><div style="font-weight:700;color:#D62828;font-size:1.1rem;margin-top:.1rem;">Bs ' + fmt(data.total_general) + '</div></div>';
        html += '</div>';

        // Servicios
        if (serv.length > 0) {
            html += '<div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;padding:.35rem 0 .15rem;">Servicios</div>';
            serv.forEach(function (s) {
                html += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.25rem 0;border-bottom:1px solid #f3f4f6;"><span>' + esc(s.nombre_servicio) + '</span><span style="font-weight:600;">Bs ' + fmt(s.precio_base) + '</span></div>';
            });
        }

        // Repuestos
        if (rep.length > 0) {
            html += '<div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;padding:.35rem 0 .15rem;margin-top:.35rem;">Repuestos</div>';
            rep.forEach(function (r) {
                var subtotal = (parseFloat(r.cantidad) || 0) * (parseFloat(r.precio_unitario_snapshot) || 0);
                html += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.25rem 0;border-bottom:1px solid #f3f4f6;"><span>' + esc(r.repuesto?.nombre || '#' + r.repuesto_id) + ' <span style="color:#9CA3AF;">x' + r.cantidad + '</span></span><span style="font-weight:600;">Bs ' + fmt(subtotal) + '</span></div>';
            });
        }

        // Total final
        html += '<div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;padding:.6rem 0 0;margin-top:.35rem;border-top:2px solid #0B1D3A;"><span style="color:#0B1D3A;">Total</span><span style="color:#D62828;">Bs ' + fmt(data.total_general) + '</span></div>';

        // Método de pago
        html += '<div style="margin-top:1rem;"><div style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#6B7280;letter-spacing:.04em;margin-bottom:.5rem;">Método de pago</div>';
        html += '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">';

        (data.metodos_pago || []).forEach(function (m) {
            var iconos = { 'Efectivo': 'bi-cash-stack', 'QR': 'bi-qr-code', 'Tarjeta': 'bi-credit-card-2-front', 'Transferencia': 'bi-bank' };
            var icono = iconos[m.nombre] || 'bi-wallet2';
            var checked = m.id === (data.metodo_default || data.metodos_pago?.[0]?.id) ? 'checked' : '';
            html += '<label style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:.6rem .25rem;border:1px solid #d1d5db;cursor:pointer;background:' + (checked ? '#0B1D3A' : '#fff') + ';color:' + (checked ? '#fff' : '#0B1D3A') + ';" class="metodo-label">';
            html += '<input type="radio" name="modal_metodo" value="' + m.id + '" ' + checked + ' style="display:none;" data-nombre="' + esc(m.nombre) + '">';
            html += '<i class="' + icono + '" style="font-size:1.3rem;"></i>';
            html += '<span style="font-size:.65rem;font-weight:600;margin-top:.2rem;">' + esc(m.nombre) + '</span>';
            html += '</label>';
        });

        html += '</div></div>';

        modalBody.innerHTML = html;

        // Eventos para cambio de método (cambio de color)
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
            });
        });
    }

    // Confirmar pago
    btnConfirmar.addEventListener('click', function () {
        if (!currentOrdenId) return;
        var selected = document.querySelector('input[name="modal_metodo"]:checked');
        if (!selected) {
            alert('Seleccioná un método de pago.');
            return;
        }

        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

        fetch('/admin/pagos/cobrar-modal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                orden_id: currentOrdenId,
                metodo_pago_id: selected.value,
            }),
            credentials: 'same-origin',
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                modalBody.innerHTML = '<div class="text-center py-4"><i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#2B9348;display:block;margin-bottom:.5rem;"></i><p style="font-weight:700;color:#0B1D3A;">Pago registrado</p><p class="small text-muted">' + esc(data.mensaje || '') + '</p></div>';
                btnConfirmar.style.display = 'none';
                btnCancelar.textContent = 'Cerrar';
                setTimeout(function () { bsModal.hide(); location.reload(); }, 1500);
            } else {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Confirmar pago';
                alert(data.mensaje || 'Error al registrar pago');
            }
        })
        .catch(function (err) {
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Confirmar pago';
            alert('Error de conexión: ' + err.message);
        });
    });

    // Reset al cerrar
    modal.addEventListener('hidden.bs.modal', function () {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = 'Confirmar pago';
        btnConfirmar.style.display = '';
        btnCancelar.textContent = 'Cancelar';
        currentOrdenId = null;
    });

    function esc(s) { return String(s || '').replace(/[&<>"']/g, function (c) { return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
    function fmt(n) { return parseFloat(n || 0).toFixed(2); }
})();
