(function () {
    'use strict';

    const modal = document.getElementById('modalPagoQR');
    if (!modal) return;

    const container = document.getElementById('modal-qr-container');
    const ordenEl = document.getElementById('modal-qr-orden');
    const montoEl = document.getElementById('modal-qr-monto');
    const refEl = document.getElementById('modal-qr-ref');

    const metodoSelect = document.getElementById('field-metodo_pago_id');
    const montoInput = document.getElementById('field-monto');
    const refInput = document.getElementById('field-numero_comprobante');
    const ordenSelect = document.getElementById('field-orden_trabajo_id');

    if (!metodoSelect) return;

    let qrCache = null;

    function esMetodoQR() {
        return metodoSelect.selectedOptions[0]?.text?.trim() === 'QR';
    }

    function actualizarQR() {
        if (!esMetodoQR()) {
            modalQr && modalQr.hide();
            return;
        }

        const ordenText = ordenSelect?.selectedOptions[0]?.text?.trim() || '—';
        const monto = montoInput?.value || '0';
        const ref = refInput?.value || '—';

        if (ordenEl) ordenEl.textContent = ordenText;
        if (montoEl) montoEl.textContent = 'Bs ' + parseFloat(monto || 0).toFixed(2).replace('.', ',');
        if (refEl) refEl.textContent = ref;

        const contenido = [
            'PAGO TALLER',
            'Orden: ' + ordenText,
            'Monto: Bs ' + parseFloat(monto || 0).toFixed(2),
            'Ref: ' + ref,
        ].join('\n');

        if (contenido === qrCache) return;
        qrCache = contenido;

        if (container) {
            container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Generando QR...</span></div></div>';

            fetch('/admin/pagos/qr-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ contenido: contenido }),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.ok && data.svg) {
                    container.innerHTML = data.svg;
                } else {
                    container.innerHTML = '<div class="text-danger small">Error al generar QR</div>';
                }
            })
            .catch(function () {
                container.innerHTML = '<div class="text-danger small">Error de conexión</div>';
            });
        }
    }

    if (metodoSelect) {
        metodoSelect.addEventListener('change', actualizarQR);
    }
    if (montoInput) {
        montoInput.addEventListener('input', actualizarQR);
    }
    if (refInput) {
        refInput.addEventListener('input', actualizarQR);
    }
    if (ordenSelect) {
        ordenSelect.addEventListener('change', actualizarQR);
    }

    let modalQr = null;
    if (modal) {
        modalQr = bootstrap.Modal.getOrCreateInstance(modal);
    }
})();
