<div class="admin-toast" id="adminToastContainer" aria-live="polite" aria-atomic="true"></div>

@push('scripts')
<script>
(function () {
    'use strict';

    const container = document.getElementById('adminToastContainer');
    if (!container) return;

    const ICONS = {
        success: 'bi-check-circle-fill',
        error:   'bi-exclamation-octagon-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill',
    };

    function createToast(message, type, duration) {
        type = type || 'info';
        duration = duration || 5000;
        const icon = ICONS[type] || ICONS.info;

        const el = document.createElement('div');
        el.className = 'admin-toast__item admin-toast__item--' + type;
        el.setAttribute('role', 'alert');
        el.innerHTML =
            '<i class="bi ' + icon + '" aria-hidden="true"></i>' +
            '<span>' + escapeHtml(message) + '</span>' +
            '<button type="button" class="admin-toast__close" aria-label="Cerrar notificaci\u00f3n">' +
                '<i class="bi bi-x-lg" aria-hidden="true"></i>' +
            '</button>';

        container.appendChild(el);

        const closeBtn = el.querySelector('.admin-toast__close');
        const remove = function () {
            if (!el.parentNode) return;
            el.classList.add('admin-toast__item--leaving');
            setTimeout(function () { el.remove(); }, 300);
        };

        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            remove();
        });

        if (duration > 0) {
            setTimeout(remove, duration);
        }

        requestAnimationFrame(function () {
            el.classList.add('admin-toast__item--visible');
        });
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Convertir flashes de Laravel en toasts
    function convertFlashes() {
        var flashes = document.querySelectorAll('.admin-flash');
        flashes.forEach(function (flash) {
            var textEl = flash.querySelector('span');
            if (!textEl) return;
            var text = textEl.textContent || '';
            var type = 'info';
            if (flash.classList.contains('admin-flash--success')) type = 'success';
            else if (flash.classList.contains('admin-flash--danger')) type = 'error';
            else if (flash.classList.contains('admin-flash--warning')) type = 'warning';
            createToast(text, type, 6000);
            flash.remove();
        });
    }

    window.showToast = createToast;

    document.addEventListener('DOMContentLoaded', convertFlashes);
})();
</script>
@endpush
