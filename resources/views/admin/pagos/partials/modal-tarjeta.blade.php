<script src="https://js.stripe.com/v3/"></script>
<div class="modal fade" id="modal-pago-tarjeta" tabindex="-1" aria-labelledby="modal-tarjeta-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="h5 fw-bold mb-0" id="modal-tarjeta-titulo">
                    <i class="bi bi-credit-card me-1"></i> Pagar con tarjeta
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="stripe-error" class="alert alert-danger d-none" role="alert"></div>
                <div id="stripe-success" class="alert alert-success d-none" role="alert"></div>

                <div class="text-center mb-3">
                    <div class="fw-bold" style="font-size:1.5rem;">Bs {{ number_format((float) ($orden->total_general ?? 0), 2, ',', '.') }}</div>
                    <div class="small text-muted">{{ $orden->numero_orden ?? '' }}</div>
                </div>

                <div id="stripe-form-wrapper">
                    <div class="mb-3">
                        <label class="form-label">N�mero de tarjeta</label>
                        <div id="stripe-card-number" class="form-control" style="padding:.5rem;"></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Fecha de expiraci�n</label>
                            <div id="stripe-card-expiry" class="form-control" style="padding:.5rem;"></div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">C�digo de seguridad</label>
                            <div id="stripe-card-cvc" class="form-control" style="padding:.5rem;"></div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-shield-lock"></i>
                        Modo prueba � usa <code>4242 4242 4242 4242</code> con cualquier CVV y fecha futura.
                    </p>
                </div>

                <div id="stripe-loading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Procesando...</span>
                    </div>
                    <p class="mt-2 small text-muted">Procesando pago...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-pagar-tarjeta" data-orden-id="{{ $orden->id ?? 0 }}">
                    <i class="bi bi-credit-card"></i> Pagar Bs {{ number_format((float) ($orden->total_general ?? 0), 2, ',', '.') }}
                </button>
            </div>
        </div>
    </div>
</div>
