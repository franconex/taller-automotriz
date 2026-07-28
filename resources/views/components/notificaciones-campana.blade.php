<div class="dropdown" id="notifBell">
    <button class="btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones" style="background:none;border:none;color:inherit;padding:4px 8px;font-size:1.15rem;">
        <i class="bi bi-bell"></i>
        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;display:none;min-width:16px;">0</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow" style="width:340px;max-height:420px;overflow-y:auto;border:0;border-radius:10px;padding:0;">
        <div class="p-2 border-bottom d-flex justify-content-between align-items-center" style="background:#f8fafc;">
            <strong class="small">Notificaciones</strong>
            <button id="notifMarcarLeidas" class="btn btn-sm btn-link text-decoration-none p-0 small">Marcar todas leídas</button>
        </div>
        <div id="notifLista">
            <div class="text-center text-muted small py-4" id="notifVacio">
                <i class="bi bi-check2-circle d-block mb-1 fs-5"></i>
                Sin notificaciones
            </div>
        </div>
        <div class="p-2 text-center border-top">
            <a href="{{ route('notificaciones.index') }}" class="small text-decoration-none">Ver todas</a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var bell = document.getElementById('notifBell');
            var badge = document.getElementById('notifBadge');
            var lista = document.getElementById('notifLista');
            var vacio = document.getElementById('notifVacio');
            var marcarTodas = document.getElementById('notifMarcarLeidas');
            var ultimoTotal = 0;

            function cargarNoLeidas() {
                fetch('{{ route("notificaciones.no-leidas") }}')
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.total !== ultimoTotal && data.total > ultimoTotal) {
                            if (typeof window.mostrarToastNotificacion === 'function') {
                                window.mostrarToastNotificacion(data.items[0]);
                            }
                        }
                        ultimoTotal = data.total;
                        actualizarBadge(data.total);
                        actualizarLista(data.items);
                    })
                    .catch(function () {});
            }

            function actualizarBadge(total) {
                if (total > 0) {
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            }

            function actualizarLista(items) {
                if (!items || items.length === 0) {
                    lista.innerHTML = '<div class="text-center text-muted small py-4" id="notifVacio">' +
                        '<i class="bi bi-check2-circle d-block mb-1 fs-5"></i>Sin notificaciones</div>';
                    return;
                }

                var html = '';
                items.forEach(function (n) {
                    var icono = n.icono || 'bi-bell';
                    var notifUrl = n.url || '#';
                    html += '<div class="dropdown-item p-2 border-bottom notif-item" data-id="' + n.id + '" data-url="' + escHtml(notifUrl) + '" style="cursor:pointer;">' +
                        '<div class="d-flex gap-2">' +
                            '<div style="font-size:1.1rem;color:#E31E24;"><i class="bi ' + icono + '"></i></div>' +
                            '<div class="flex-fill" style="min-width:0;">' +
                                '<div class="fw-semibold small text-truncate">' + escHtml(n.titulo) + '</div>' +
                                '<div class="text-muted small text-truncate">' + escHtml(n.mensaje) + '</div>' +
                                '<div class="text-muted" style="font-size:.65rem;">' + escHtml(n.tiempo) + '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                });
                lista.innerHTML = html;

                var baseUrl = '{{ url("notificaciones") }}';
                lista.querySelectorAll('.notif-item').forEach(function (el) {
                    el.addEventListener('click', function () {
                        var id = this.getAttribute('data-id');
                        var url = this.getAttribute('data-url');
                        fetch(baseUrl + '/' + id + '/leer', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '' } })
                            .then(function (r) {
                                if (r.ok && url && url !== '#' && url !== '' && url.startsWith('/')) {
                                    window.location.href = url;
                                } else {
                                    cargarNoLeidas();
                                }
                            })
                            .catch(function () { cargarNoLeidas(); });
                    });
                });
            }

            if (marcarTodas) {
                marcarTodas.addEventListener('click', function () {
                    fetch('{{ route("notificaciones.marcar-todas") }}', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '' } })
                        .then(function () { cargarNoLeidas(); })
                        .catch(function () {});
                });
            }

            function escHtml(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            window.mostrarToastNotificacion = function (notif) {
                if (!notif) return;
                var container = document.getElementById('notifToastContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'notifToastContainer';
                    container.style.cssText = 'position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
                    document.body.appendChild(container);
                }
                var toast = document.createElement('div');
                toast.style.cssText = 'background:#fff;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,.12);padding:12px 16px;max-width:360px;border-left:4px solid #E31E24;animation:slideIn .3s ease;display:flex;align-items:start;gap:10px;';
                toast.innerHTML = '<div style="color:#E31E24;font-size:1.1rem;"><i class="bi ' + (notif.icono || 'bi-bell') + '"></i></div>' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-weight:600;font-size:.85rem;">' + escHtml(notif.titulo) + '</div>' +
                        '<div style="color:#64748b;font-size:.8rem;" class="text-truncate">' + escHtml(notif.mensaje) + '</div>' +
                    '</div>' +
                    '<button onclick="this.parentElement.remove()" style="background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:1rem;">×</button>';
                container.appendChild(toast);
                setTimeout(function () { if (toast.parentElement) toast.remove(); }, 6000);
            };

            var style = document.createElement('style');
            style.textContent = '@keyframes slideIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }';
            document.head.appendChild(style);

            cargarNoLeidas();
            setInterval(cargarNoLeidas, 30000);
        });
    </script>
@endpush
