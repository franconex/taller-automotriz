<div class="modal fade" id="modalPagoQR" tabindex="-1" aria-labelledby="modalPagoQRTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body p-0">
                <h5 class="fw-bold mb-1" id="modalPagoQRTitle">Código QR</h5>
                <p class="cell-muted small mb-3">Escanea con tu app bancaria</p>

                <div id="modal-qr-container" class="mb-3">
                    <div class="text-center py-4">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Generando QR...</span>
                        </div>
                    </div>
                </div>

                <dl class="admin-meta text-start small mb-3" id="modal-qr-datos">
                    <dt>Orden</dt>
                    <dd id="modal-qr-orden">—</dd>
                    <dt>Monto</dt>
                    <dd id="modal-qr-monto">—</dd>
                    <dt>Referencia</dt>
                    <dd id="modal-qr-ref">—</dd>
                </dl>

                <div class="cell-muted small mb-3">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    Transfiere el monto exacto desde tu banco usando estos datos.
                </div>

                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
