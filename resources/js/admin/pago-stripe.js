(function () {
    const MODAL_ID = 'modal-pago-tarjeta';
    const modalEl = document.getElementById(MODAL_ID);
    if (!modalEl) return;

    const STRIPE_KEY = document.getElementById('stripe-key')?.getAttribute('content');
    if (!STRIPE_KEY) return;

    let stripe = null;
    let elements = null;
    let cardNumber = null;
    let cardExpiry = null;
    let cardCvc = null;
    let initialized = false;

    function initStripe() {
        if (initialized) return;
        initialized = true;

        stripe = Stripe(STRIPE_KEY);
        elements = stripe.elements({ locale: 'es' });

        cardNumber = elements.create('cardNumber', { style: { base: { fontSize: '16px', color: '#1F2937' } } });
        cardExpiry = elements.create('cardExpiry', { style: { base: { fontSize: '16px', color: '#1F2937' } } });
        cardCvc = elements.create('cardCvc', { style: { base: { fontSize: '16px', color: '#1F2937' } } });

        cardNumber.mount('#stripe-card-number');
        cardExpiry.mount('#stripe-card-expiry');
        cardCvc.mount('#stripe-card-cvc');
    }

    function destroyStripe() {
        if (!initialized) return;
        try { cardNumber?.destroy(); } catch(e) {}
        try { cardExpiry?.destroy(); } catch(e) {}
        try { cardCvc?.destroy(); } catch(e) {}
        initialized = false;
    }

    const btnPagar = document.getElementById('btn-pagar-tarjeta');
    const errorEl = document.getElementById('stripe-error');
    const successEl = document.getElementById('stripe-success');
    const formWrapper = document.getElementById('stripe-form-wrapper');
    const loadingEl = document.getElementById('stripe-loading');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (!btnPagar || !errorEl || !successEl || !formWrapper || !loadingEl) return;

    modalEl.addEventListener('shown.bs.modal', function () {
        setTimeout(initStripe, 100);
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        destroyStripe();
        btnPagar.disabled = false;
        formWrapper.classList.remove('d-none');
        loadingEl.classList.add('d-none');
        errorEl.classList.add('d-none');
        successEl.classList.add('d-none');
    });

    function mostrarError(msg) {
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
        successEl.classList.add('d-none');
    }

    function ocultarError() {
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
    }

    btnPagar.addEventListener('click', async function () {
        const ordenId = this.getAttribute('data-orden-id');
        if (!ordenId || ordenId === '0') { mostrarError('Orden no valida.'); return; }

        ocultarError();
        btnPagar.disabled = true;
        formWrapper.classList.add('d-none');
        loadingEl.classList.remove('d-none');

        try {
            const { error, paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
            });

            if (error) {
                mostrarError(error.message);
                btnPagar.disabled = false;
                formWrapper.classList.remove('d-none');
                loadingEl.classList.add('d-none');
                return;
            }

            const res = await fetch('/admin/pagos/stripe/cobrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ orden_id: ordenId }),
            });

            const data = await res.json();

            if (data.ok) {
                successEl.innerHTML = '<strong><i class="bi bi-check-circle-fill"></i> Pago exitoso</strong><br>Ref: ' + data.referencia;
                successEl.classList.remove('d-none');
                formWrapper.classList.add('d-none');
                loadingEl.classList.add('d-none');
                btnPagar.classList.add('d-none');
                setTimeout(function () { window.location.reload(); }, 2500);
            } else {
                mostrarError(data.message || 'Error al procesar el pago.');
                btnPagar.disabled = false;
                formWrapper.classList.remove('d-none');
                loadingEl.classList.add('d-none');
            }
        } catch (err) {
            mostrarError('Error de conexion. Intenta de nuevo.');
            btnPagar.disabled = false;
            formWrapper.classList.remove('d-none');
            loadingEl.classList.add('d-none');
        }
    });
})();