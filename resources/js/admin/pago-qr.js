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

        if (container) {
            container.innerHTML = '<img src="/img/QR-Pago.jpeg" alt="Código QR de pago" class="img-fluid" style="max-width:250px;">';
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
