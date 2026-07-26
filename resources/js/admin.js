(function () {
    'use strict';

    /* =========================================================
       1. SIDEBAR (collapse / mobile offcanvas)
       ========================================================= */
    function initSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const toggle = document.getElementById('adminSidebarToggle');
        if (!sidebar || !toggle) return;

        const STORAGE_KEY = 'tallerpro:admin-sidebar-collapsed';
        const COLLAPSED_CLASS = 'admin-sidebar--collapsed';

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            sidebar.style.transition = 'none';
        }

        try {
            if (localStorage.getItem(STORAGE_KEY) === '1') {
                sidebar.classList.add(COLLAPSED_CLASS);
            }
        } catch (e) { /* localStorage may be unavailable */ }

        const apply = () => {
            const collapsed = sidebar.classList.contains(COLLAPSED_CLASS);
            toggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
            toggle.setAttribute(
                'aria-label',
                collapsed ? 'Desplegar menú' : 'Plegar menú'
            );
            toggleSidebarTooltips(collapsed);
        };

        toggle.addEventListener('click', function () {
            sidebar.classList.toggle(COLLAPSED_CLASS);
            const collapsed = sidebar.classList.contains(COLLAPSED_CLASS);
            try { localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0'); } catch (e) { /* ignore */ }
            apply();
        });

        apply();

        apply();
    }

    /* ---------------------------------------------------------
       Sidebar tooltips: enable when collapsed, disable when expanded
       --------------------------------------------------------- */
    let sidebarTooltips = [];

    function toggleSidebarTooltips(enable) {
        destroySidebarTooltips();
        if (!enable) return;

        const links = document.querySelectorAll('#adminSidebar .admin-sidebar__link');
        links.forEach(function (el) {
            const label = el.getAttribute('data-tp-label');
            if (!label) return;
            const instance = new bootstrap.Tooltip(el, {
                placement: 'right',
                customClass: 'admin-tooltip',
                title: label,
                trigger: 'hover focus',
            });
            sidebarTooltips.push(instance);
        });
    }

    function destroySidebarTooltips() {
        sidebarTooltips.forEach(function (instance) { instance.dispose(); });
        sidebarTooltips = [];
    }

    /* =========================================================
       1.1 SIDEBAR GROUPS (submenus)
       ========================================================= */
    function initSidebarGroups() {
        const groups = document.querySelectorAll('[data-tp-sidebar-group]');
        groups.forEach(function (btn) {
            const group = btn.closest('.admin-sidebar__group');
            if (!group) return;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const isOpen = group.classList.toggle('is-open');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    }

    /* =========================================================
       2. CONFIRM MODAL (programmatic)
       ------------------------------------------------------------
       Usage:
         window.TPConfirm.ask({
             title: '¿Anular pago?',
             message: 'Esta acción no se puede deshacer.',
             confirmText: 'Anular pago',
             confirmClass: 'btn-danger',
             icon: 'warning',
         }).then((ok) => { if (ok) ... });
       Or data-attribute:
         <button data-tp-confirm
                 data-tp-confirm-title="¿Eliminar cliente?"
                 data-tp-confirm-message="..."
                 data-tp-confirm-text="Eliminar"
                 data-tp-confirm-class="btn-danger"
                 data-tp-form-id="delete-form-1">Eliminar</button>
       ========================================================= */
    function ensureConfirmModal() {
        let modal = document.getElementById('tpConfirmModal');
        if (modal) return modal;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="modal fade" id="tpConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="tpConfirmModalTitle">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center px-4 pt-4 pb-3">
                            <div class="admin-confirm__icon" id="tpConfirmIcon"><i class="bi bi-exclamation-triangle"></i></div>
                            <h2 class="h5 fw-bold mb-2" id="tpConfirmModalTitle">¿Confirmar acción?</h2>
                            <p class="text-muted mb-0" id="tpConfirmMessage">Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                            <button type="button" class="btn btn-outline-secondary" data-tp-confirm-cancel>Cancelar</button>
                            <button type="button" class="btn btn-danger" id="tpConfirmOk">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        `.trim();
        document.body.appendChild(wrapper.firstChild);
        modal = document.getElementById('tpConfirmModal');
        return modal;
    }

    function setConfirmIcon(kind) {
        const icon = document.getElementById('tpConfirmIcon');
        if (!icon) return;
        icon.classList.remove('admin-confirm__icon--warning', 'admin-confirm__icon--info');
        const i = icon.querySelector('i');
        if (kind === 'warning') {
            icon.classList.add('admin-confirm__icon--warning');
            i.className = 'bi bi-exclamation-triangle';
        } else if (kind === 'info') {
            icon.classList.add('admin-confirm__icon--info');
            i.className = 'bi bi-info-circle';
        } else {
            i.className = 'bi bi-exclamation-triangle';
        }
    }

    function submitTargetForm(formId) {
        if (!formId) return false;
        const form = document.getElementById(formId);
        if (!form) return false;
        form.submit();
        return true;
    }

    function initConfirm() {
        ensureConfirmModal();

        window.TPConfirm = {
            ask: function (opts) {
                opts = opts || {};
                const modalEl = ensureConfirmModal();
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                const titleEl = document.getElementById('tpConfirmModalTitle');
                const msgEl = document.getElementById('tpConfirmMessage');
                const okBtn = document.getElementById('tpConfirmOk');
                const cancelBtn = modalEl.querySelector('[data-tp-confirm-cancel]');

                titleEl.textContent = opts.title || '¿Confirmar acción?';
                msgEl.textContent = opts.message || 'Esta acción no se puede deshacer.';
                okBtn.textContent = opts.confirmText || 'Confirmar';
                okBtn.className = 'btn ' + (opts.confirmClass || 'btn-danger');
                setConfirmIcon(opts.icon || 'danger');

                return new Promise(function (resolve) {
                    const cleanup = () => {
                        okBtn.removeEventListener('click', onOk);
                        cancelBtn.removeEventListener('click', onCancel);
                        modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    };
                    const onOk = () => {
                        cleanup();
                        modal.hide();
                        if (opts.formId) {
                            submitTargetForm(opts.formId);
                            resolve(true);
                        } else if (typeof opts.onConfirm === 'function') {
                            Promise.resolve(opts.onConfirm()).then(resolve);
                        } else {
                            resolve(true);
                        }
                    };
                    const onCancel = () => { cleanup(); modal.hide(); resolve(false); };
                    const onHidden = () => { cleanup(); resolve(false); };

                    okBtn.addEventListener('click', onOk);
                    cancelBtn.addEventListener('click', onCancel);
                    modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });

                    modal.show();
                });
            }
        };

        // Delegate data-attribute triggers
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-tp-confirm]');
            if (!trigger) return;
            e.preventDefault();
            window.TPConfirm.ask({
                title: trigger.getAttribute('data-tp-confirm-title') || '¿Confirmar acción?',
                message: trigger.getAttribute('data-tp-confirm-message') || 'Esta acción no se puede deshacer.',
                confirmText: trigger.getAttribute('data-tp-confirm-text') || 'Confirmar',
                confirmClass: trigger.getAttribute('data-tp-confirm-class') || 'btn-danger',
                icon: trigger.getAttribute('data-tp-confirm-icon') || 'danger',
                formId: trigger.getAttribute('data-tp-form-id') || null,
            });
        });
    }

    /* =========================================================
       3. OFFCANVAS (open/close + auto-close on success)
       ========================================================= */
    function initOffcanvas() {
        document.addEventListener('click', function (e) {
            const opener = e.target.closest('[data-tp-offcanvas-open]');
            if (opener) {
                e.preventDefault();
                const target = document.querySelector(opener.getAttribute('data-tp-offcanvas-open'));
                if (target && window.bootstrap) {
                    bootstrap.Offcanvas.getOrCreateInstance(target).show();
                }
                return;
            }
            const closer = e.target.closest('[data-tp-offcanvas-close]');
            if (closer) {
                e.preventDefault();
                const off = closer.closest('.offcanvas');
                if (off && window.bootstrap) {
                    bootstrap.Offcanvas.getOrCreateInstance(off).hide();
                }
            }
        });

        // Auto-cerrar offcanvas cuando se muestra un flash de éxito
        const successFlash = document.querySelector('.admin-flash--success');
        if (successFlash) {
            document.querySelectorAll('.offcanvas.show').forEach(function (off) {
                if (window.bootstrap) {
                    bootstrap.Offcanvas.getOrCreateInstance(off).hide();
                }
            });
        }
    }

    /* =========================================================
       4. AUTO-DISMISS FLASH
       ========================================================= */
    function initFlashAutoDismiss() {
        document.querySelectorAll('.admin-flash').forEach(function (el) {
            window.setTimeout(function () {
                el.style.transition = 'opacity .25s ease';
                el.style.opacity = '0';
                window.setTimeout(function () { el.remove(); }, 280);
            }, 5000);
        });
    }

    /* =========================================================
       5. BOOT
       ========================================================= */
    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
        initSidebarGroups();
        initConfirm();
        initOffcanvas();
        initFlashAutoDismiss();
    });
})();
