/* =====================================================================
   CITAS · Lógica del calendario interactivo
   ===================================================================== */

import '../../css/admin/citas.css';
import * as bootstrap from 'bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

(function () {
    'use strict';

    const root = document.getElementById('citas-app');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fechaInicial = root.dataset.fecha || new Date().toISOString().slice(0, 10);
    const permitirReprog = root.dataset.reprogramar === '1';
    const permitirCrear = root.dataset.crear === '1';
    const urlEventos  = root.dataset.urlEventos;
    const urlMostrar  = root.dataset.urlMostrar;
    const urlAcciones = root.dataset.urlAcciones;
    const urlTablaDia = root.dataset.urlTablaDia;
    const urlProximas = root.dataset.urlProximas;

    const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DIAS_SEMANA = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const COLORES = {
        confirmada: '#16A34A',
        pendiente:  '#F59E0B',
        atendida:   '#0891B2',
        cancelada:  '#9CA3AF',
        no_asistio: '#B91C1C',
    };

    function escape(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));
    }

    function formatHora(hora) {
        if (!hora) return '';
        return hora.slice(0, 5);
    }

    function getInitialView() {
        return window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek';
    }

    function setLoading(active) {
        const el = document.getElementById('citas-loading');
        if (el) el.classList.toggle('is-active', !!active);
    }

    function mostrarExito(mensaje) {
        const flash = document.querySelector('.admin-flash-wrapper');
        if (!flash) return;
        const div = document.createElement('div');
        div.className = 'admin-flash admin-flash--success';
        div.setAttribute('role', 'alert');
        div.innerHTML = `<i class="bi bi-check-circle-fill"></i><span>${escape(mensaje)}</span><button type="button" class="admin-flash__close" data-bs-dismiss="alert"><i class="bi bi-x-lg"></i></button>`;
        flash.prepend(div);
    }

    function mostrarError(mensaje) {
        const flash = document.querySelector('.admin-flash-wrapper');
        if (!flash) { alert(mensaje); return; }
        const div = document.createElement('div');
        div.className = 'admin-flash admin-flash--danger';
        div.setAttribute('role', 'alert');
        div.innerHTML = `<i class="bi bi-exclamation-octagon-fill"></i><span>${escape(mensaje)}</span><button type="button" class="admin-flash__close" data-bs-dismiss="alert"><i class="bi bi-x-lg"></i></button>`;
        flash.prepend(div);
    }

    /* ===========================================================
       Calendario principal
       =========================================================== */

    const mainEl = document.getElementById('calendario-citas');
    let mainCalendar = null;

    function initMainCalendar() {
        if (!mainEl) return;

        mainCalendar = new Calendar(mainEl, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
            initialView: getInitialView(),
            initialDate: fechaInicial,
            locale: 'es',
            firstDay: 1,
            nowIndicator: true,
            allDaySlot: false,
            slotMinTime: '08:00:00',
            slotMaxTime: '19:00:00',
            slotDuration: '00:30:00',
            slotLabelInterval: '01:00:00',
            height: 'auto',
            expandRows: true,
            editable: permitirReprog,
            selectable: permitirCrear,
            dayMaxEvents: true,
            headerToolbar: false,
            events(info, success, failure) {
                const leer = (id) => document.getElementById(id)?.value || '';
                const url = new URL(urlEventos, window.location.origin);
                url.searchParams.set('start', info.startStr.slice(0, 10));
                url.searchParams.set('end', info.endStr.slice(0, 10));
                ['sucursal_id','servicio_id','mecanico_id','estado'].forEach((k) => {
                    const v = leer('filtro-' + k);
                    if (v) url.searchParams.set(k, v);
                });
                setLoading(true);
                fetch(url.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                })
                    .then((r) => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
                    .then((data) => {
                        console.log('FullCalendar events loaded:', data.length, 'events for view', info.view.type, info.startStr, '-', info.endStr);
                        setLoading(false);
                        success(data);
                    })
                    .catch((err) => {
                        console.error('Error loading events:', err);
                        setLoading(false);
                        failure(err);
                    });
            },
            eventDidMount(info) {
                try {
                    const ev = info.event;
                    const p = ev.extendedProps || {};
                    const viewType = info.view.type;
                    if (viewType === 'timeGridWeek' || viewType === 'timeGridDay') {
                        const el = info.el;
                        if (el) {
                            el.style.background = p.estado ? (COLORES[p.estado] || '#6B7280') : '#6B7280';
                            el.style.borderLeft = '3px solid rgba(255,255,255,0.6)';
                            el.style.color = '#fff';
                        }
                    }
                } catch (e) {
                    console.error('eventDidMount error:', e);
                }
            },
            eventClick(info) {
                info.jsEvent.preventDefault();
                abrirDetalle(info.event.id);
            },
            eventContent(arg) {
                try {
                    const ev = arg.event;
                    const p = ev.extendedProps || {};
                    const esTimeGrid = arg.view.type === 'timeGridWeek' || arg.view.type === 'timeGridDay';
                    const esDayGrid = arg.view.type === 'dayGridMonth';
                    const h = ev.start ? String(ev.start.getHours()).padStart(2, '0') + ':' + String(ev.start.getMinutes()).padStart(2, '0') : '';
                    const hf = ev.end ? String(ev.end.getHours()).padStart(2, '0') + ':' + String(ev.end.getMinutes()).padStart(2, '0') : '';
                    const hTexto = (h && hf) ? h + ' - ' + hf : h;
                    const cliente = p.cliente || '';
                    const vehiculo = p.vehiculo || '';
                    const servicio = p.servicio || '';
                    const estado = p.estado || '';
                    const color = COLORES[estado] || '#6B7280';
                    var labelEstado = p.estado_label || estado;
                    labelEstado = labelEstado.charAt(0).toUpperCase() + labelEstado.slice(1);

                    if (esTimeGrid) {
                        return { html: '<div class="cita-card cita-card--timegrid" style="--cita-color:' + color + '">' +
                            '<div class="cita-card__hora">' + escape(hTexto) + '</div>' +
                            '<div class="cita-card__cliente">' + escape(cliente) + '</div>' +
                            (vehiculo ? '<div class="cita-card__vehiculo">' + escape(vehiculo) + '</div>' : '') +
                            (servicio ? '<div class="cita-card__servicio">' + escape(servicio) + '</div>' : '') +
                        '</div>' };
                    }
                    if (esDayGrid) {
                        return { html: '<div class="cita-card cita-card--daygrid">' +
                            '<span class="cita-card__dot" style="background:' + color + '"></span>' +
                            '<span class="cita-card__cliente">' + escape(cliente) + '</span>' +
                        '</div>' };
                    }
                    return { html: '<span>' + escape(hTexto) + ' ' + escape(cliente) + (vehiculo ? ' \u00b7 ' + escape(vehiculo) : '') + '</span>' };
                } catch (e) {
                    return { html: '<span>' + escape(arg.event.title || 'Cita') + '</span>' };
                }
            },
            select(info) {
                if (!permitirCrear) return;
                mainCalendar.unselect();
                abrirFormulario({
                    fecha: info.startStr.slice(0, 10),
                    hora: info.startStr.slice(11, 16) || '09:00',
                });
            },
            eventDrop(info) {
                if (!permitirReprog) { info.revert(); return; }
                reprogramarEvento(info.event, info);
            },
            eventResize(info) {
                if (!permitirReprog) { info.revert(); return; }
                reprogramarEvento(info.event, info);
            },
            datesSet(info) {
                actualizarRango(info.start, info.end);
                actualizarBotonesVista();
                const view = info.view.type;
                if (view === 'timeGridDay' || view === 'timeGridWeek') {
                    cargarCitasDelDia(info.start.toISOString().slice(0, 10));
                }
            },
        });
        mainCalendar.render();
    }

    function actualizarRango(start, end) {
        const el = document.getElementById('citas-rango');
        if (!el || !mainCalendar) return;

        const fmt = (d) => `${d.getDate()} ${MESES[d.getMonth()]}`;
        const a = start;
        const b = new Date(end.getTime() - 24 * 60 * 60 * 1000);

        if (a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear()) {
            el.textContent = `${a.getDate()} – ${b.getDate()} ${MESES[a.getMonth()]} ${a.getFullYear()}`;
        } else {
            el.textContent = `${fmt(a)} – ${fmt(b)} ${b.getFullYear()}`;
        }
    }

    function actualizarBotonesVista() {
        const view = mainCalendar?.view?.type;
        document.querySelectorAll('[data-cal-view]').forEach((b) => {
            b.classList.toggle('is-active', b.getAttribute('data-cal-view') === view);
        });
    }

    function bindToolbar() {
        document.querySelectorAll('[data-cal-action]').forEach((btn) => {
            btn.addEventListener('click', () => {
                if (!mainCalendar) return;
                const a = btn.getAttribute('data-cal-action');
                if (a === 'today') mainCalendar.today();
                if (a === 'prev') mainCalendar.prev();
                if (a === 'next') mainCalendar.next();
                actualizarBotonesVista();
            });
        });

        document.querySelectorAll('[data-cal-view]').forEach((btn) => {
            btn.addEventListener('click', () => {
                if (!mainCalendar) return;
                mainCalendar.changeView(btn.getAttribute('data-cal-view'));
                actualizarBotonesVista();
            });
        });

        document.getElementById('citas-rango')?.addEventListener('click', () => {
            if (mainCalendar) mainCalendar.today();
        });

        ['filtro-sucursal','filtro-servicio','filtro-mecanico','filtro-estado'].forEach((id) => {
            document.getElementById(id)?.addEventListener('change', () => {
                if (mainCalendar) mainCalendar.refetchEvents();
            });
        });

        document.getElementById('citas-limpiar-filtros')?.addEventListener('click', () => {
            ['filtro-sucursal','filtro-servicio','filtro-mecanico','filtro-estado'].forEach((id) => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            if (mainCalendar) mainCalendar.refetchEvents();
        });

        document.querySelectorAll('[data-tp-open-modal="nueva-cita"]').forEach((btn) => {
            btn.addEventListener('click', () => abrirFormulario({}));
        });
    }

    /* ===========================================================
       Mini calendario
       =========================================================== */

    let miniCalendar = null;

    function initMiniCalendar() {
        const el = document.getElementById('mini-calendario');
        if (!el) return;

        miniCalendar = new Calendar(el, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            initialDate: fechaInicial,
            locale: 'es',
            firstDay: 1,
            fixedWeekCount: false,
            showNonCurrentDates: true,
            headerToolbar: { left: 'prev', center: 'title', right: 'next' },
            height: 'auto',
            contentHeight: 'auto',
            dayMaxEventRows: 0,
            selectable: false,
            dateClick(info) {
                if (!mainCalendar) return;
                mainCalendar.gotoDate(info.dateStr);
                mainCalendar.changeView('timeGridDay');
                cargarCitasDelDia(info.dateStr);
            },
        });
        miniCalendar.render();
    }

    /* ===========================================================
       Citas del día + próximas
       =========================================================== */

    async function cargarCitasDelDia(fecha) {
        const cont = document.getElementById('citas-dia-contenido');
        if (!cont) return;
        cont.innerHTML = '<div class="citas-empty"><i class="bi bi-arrow-clockwise citas-empty__icon"></i><p class="citas-empty__text">Cargando…</p></div>';

        try {
            const url = new URL(urlTablaDia, window.location.origin);
            url.searchParams.set('fecha', fecha);
            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            renderCitasDelDia(fecha, data);
        } catch (err) {
            cont.innerHTML = '<div class="citas-empty"><i class="bi bi-exclamation-triangle citas-empty__icon"></i><p class="citas-empty__text">No se pudieron cargar las citas del día.</p></div>';
        }
    }

    function renderCitasDelDia(fecha, data) {
        const cont = document.getElementById('citas-dia-contenido');
        if (!cont) return;

        const titulo = document.getElementById('citas-dia-titulo');
        if (titulo) {
            const d = new Date(fecha + 'T00:00:00');
            titulo.textContent = `Citas del Día — ${DIAS_SEMANA[d.getDay()]} ${d.getDate()} de ${MESES[d.getMonth()]}`;
        }

        const citas = data.citas || [];
        if (!citas.length) {
            cont.innerHTML = '<div class="citas-empty"><i class="bi bi-calendar-x citas-empty__icon"></i><h3 class="citas-empty__title">No hay citas programadas para este día</h3><p class="citas-empty__text">Puedes registrar una nueva cita desde el botón "Nueva cita".</p></div>';
            return;
        }

        const rows = citas.map((c) => `
            <tr data-cita-id="${c.id}">
                <td>${escape(formatHora(c.hora))}${c.hora_fin ? ' – ' + escape(formatHora(c.hora_fin)) : ''}</td>
                <td><div class="cell-strong">${escape(c.cliente)}</div><div class="citas-dia-tabla__vehiculo">${escape(c.telefono || '')}</div></td>
                <td><div class="cell-strong">${escape(c.vehiculo || '—')}</div></td>
                <td>${escape(c.servicio || '—')}</td>
                <td>${escape(c.mecanico || '—')}</td>
                <td><span class="citas-estado-badge citas-estado-badge--${c.estado}">${escape(c.estado_label)}</span></td>
                <td>
                    <div class="citas-dia-acciones">
                        <button class="citas-icon-btn" title="Ver" data-accion="ver" data-id="${c.id}"><i class="bi bi-eye"></i></button>
                        ${data.puede_editar ? `<button class="citas-icon-btn" title="Editar" data-accion="editar" data-id="${c.id}"><i class="bi bi-pencil-square"></i></button>` : ''}
                        ${c.estado === 'pendiente' && data.puede_editar ? `<button class="citas-icon-btn" title="Confirmar" data-accion="confirmar" data-id="${c.id}"><i class="bi bi-check2-circle"></i></button>` : ''}
                        ${!['cancelada','atendida','no_asistio'].includes(c.estado) && data.puede_cancelar ? `<button class="citas-icon-btn citas-icon-btn--danger" title="Cancelar" data-accion="cancelar" data-id="${c.id}"><i class="bi bi-x-circle"></i></button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        cont.innerHTML = `<table class="citas-dia-tabla"><thead><tr><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Mecánico</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>${rows}</tbody></table>`;

        cont.querySelectorAll('[data-accion]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const a = btn.getAttribute('data-accion');
                const id = btn.getAttribute('data-id');
                if (a === 'ver') abrirDetalle(id);
                if (a === 'editar') abrirFormulario({ cita_id: id });
                if (a === 'confirmar') confirmarCita(id);
                if (a === 'cancelar') cancelarCita(id);
            });
        });
    }

    async function cargarProximasCitas() {
        const cont = document.getElementById('citas-proximas-lista');
        if (!cont) return;
        try {
            const res = await fetch(urlProximas, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            const citas = data.citas || [];
            if (!citas.length) {
                cont.innerHTML = '<li class="citas-empty"><i class="bi bi-calendar-event citas-empty__icon"></i><p class="citas-empty__text">No hay próximas citas.</p></li>';
                return;
            }
            cont.innerHTML = citas.map((c) => `
                <li class="citas-proximas__item">
                    <span class="citas-proximas__dot" style="background:${escape(c.estado_color || '#6B7280')}"></span>
                    <div>
                        <div class="citas-proximas__fecha">${escape(c.fecha_label)} · ${escape(formatHora(c.hora))}</div>
                        <div class="citas-proximas__cliente">${escape(c.cliente)}</div>
                        <div class="citas-proximas__servicio">${escape(c.servicio || '')}</div>
                        <a href="javascript:void(0)" class="citas-proximas__ver" data-ver-id="${c.id}">Ver <i class="bi bi-arrow-right"></i></a>
                    </div>
                </li>
            `).join('');
            cont.querySelectorAll('[data-ver-id]').forEach((a) => {
                a.addEventListener('click', () => abrirDetalle(a.getAttribute('data-ver-id')));
            });
        } catch (err) {
            cont.innerHTML = '<li class="citas-empty"><i class="bi bi-exclamation-triangle citas-empty__icon"></i><p class="citas-empty__text">No se pudieron cargar las próximas citas.</p></li>';
        }
    }

    /* ===========================================================
       Detalle
       =========================================================== */

    async function abrirDetalle(id) {
        const modalEl = document.getElementById('modal-detalle-cita');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        document.getElementById('modal-detalle-contenido').innerHTML = '<div class="text-center text-muted py-4">Cargando…</div>';
        modal.show();
        try {
            const res = await fetch(`${urlMostrar}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const c = await res.json();
            renderDetalle(c);
        } catch (err) {
            document.getElementById('modal-detalle-contenido').innerHTML = '<div class="alert alert-danger mb-0">No se pudo cargar el detalle.</div>';
        }
    }

    function renderDetalle(c) {
        const body = document.getElementById('modal-detalle-contenido');
        if (!body) return;
        const hora = formatHora(c.hora);
        const horaFin = c.hora_fin ? formatHora(c.hora_fin) : '';
        let actions = '';
        if (c.es_pasable_reprogramar) actions += `<button type="button" class="btn btn-outline-secondary" data-accion="reprogramar" data-id="${c.id}"><i class="bi bi-arrow-repeat"></i> Reprogramar</button>`;
        if (c.es_pasable_confirmar) actions += `<button type="button" class="btn btn-success" data-accion="confirmar" data-id="${c.id}"><i class="bi bi-check2-circle"></i> Confirmar</button>`;
        if (c.es_pasable_cancelar) actions += `<button type="button" class="btn btn-outline-danger" data-accion="cancelar" data-id="${c.id}"><i class="bi bi-x-circle"></i> Cancelar</button>`;
        if (c.es_pasable_no_asistio) actions += `<button type="button" class="btn btn-outline-warning" data-accion="no-asistio" data-id="${c.id}"><i class="bi bi-person-x"></i> No asistió</button>`;
        if (!c.tiene_orden && !['cancelada','no_asistio'].includes(c.estado)) actions += `<button type="button" class="btn btn-primary" data-accion="convertir-orden" data-id="${c.id}"><i class="bi bi-clipboard-check"></i> Convertir a orden</button>`;

        const extra = [];
        if (c.motivo_reprogramacion) extra.push(`<div class="citas-detalle-info-extra"><strong>Reprogramada</strong>${c.reprogramado_por ? ' por ' + escape(c.reprogramado_por) : ''}${c.reprogramado_en ? ' el ' + escape(c.reprogramado_en) : ''}<br><em>Motivo:</em> ${escape(c.motivo_reprogramacion)}</div>`);
        if (c.cancelado_motivo) extra.push(`<div class="citas-detalle-info-extra"><strong>Cancelada</strong>${c.cancelado_por ? ' por ' + escape(c.cancelado_por) : ''}${c.cancelado_en ? ' el ' + escape(c.cancelado_en) : ''}<br><em>Motivo:</em> ${escape(c.cancelado_motivo)}</div>`);
        if (c.tiene_orden) extra.push(`<div class="citas-detalle-info-extra"><i class="bi bi-clipboard-check"></i> Orden: <a href="/admin/ordenes/${c.orden_id}" class="fw-bold">${escape(c.orden_numero)}</a></div>`);

        body.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><span class="citas-estado-badge citas-estado-badge--${escape(c.estado)}">${escape(c.estado_label)}</span><div class="mt-2 small text-muted">Registrada el ${escape(c.created_at || '')}${c.usuario ? ' por ' + escape(c.usuario) : ''}</div></div>
                <div class="text-end"><div class="fw-bold">${c.fecha_label || c.fecha}</div><div class="small text-muted">${hora}${horaFin ? ' – ' + horaFin : ''}</div></div>
            </div>
            <dl class="citas-detalle-grid">
                <div><dt>Cliente</dt><dd>${escape(c.cliente || '—')}</dd></div>
                <div><dt>Teléfono</dt><dd>${escape(c.cliente_telefono || '—')}</dd></div>
                <div><dt>Vehículo</dt><dd>${escape(c.vehiculo || '—')}</dd></div>
                <div><dt>Servicio</dt><dd>${escape(c.servicio || '—')}</dd></div>
                <div><dt>Mecánico</dt><dd>${escape(c.mecanico || '—')}</dd></div>
                <div><dt>Sucursal</dt><dd>${escape(c.sucursal || '—')}</dd></div>
            </dl>
            <div class="citas-detalle-section">
                <dt>Problema</dt><dd>${escape(c.descripcion_problema || '—')}</dd>
                ${c.observaciones ? `<dt>Observaciones</dt><dd>${escape(c.observaciones)}</dd>` : ''}
            </div>
            ${extra.join('')}
            <div class="citas-detalle-actions">${actions || '<span class="text-muted small">No hay acciones disponibles.</span>'}</div>
        `;

        body.querySelectorAll('[data-accion]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const a = btn.getAttribute('data-accion');
                const id = btn.getAttribute('data-id');
                if (a === 'reprogramar') abrirReprogramar(id);
                if (a === 'confirmar') confirmarCita(id);
                if (a === 'cancelar') cancelarCita(id);
                if (a === 'no-asistio') marcarNoAsistio(id);
                if (a === 'convertir-orden') convertirEnOrden(id);
            });
        });
    }

    /* ===========================================================
       Acciones
       =========================================================== */

    async function peticionAccion(method, url, body) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: body ? JSON.stringify(body) : undefined,
        });
        const data = res.headers.get('content-type')?.includes('application/json') ? await res.json() : null;
        if (!res.ok) {
            const msg = data?.message || data?.errors ? Object.values(data.errors).flat().join(', ') : 'Error del servidor';
            const err = new Error(msg);
            err.errors = data?.errors || {};
            err.status = res.status;
            throw err;
        }
        return data;
    }

    async function confirmarCita(id) {
        const ok = await confirmarAccion('¿Confirmar esta cita?', 'La cita cambiará a estado confirmado.');
        if (!ok) return;
        try {
            const data = await peticionAccion('PATCH', `${urlAcciones}/${id}/confirmar`);
            mostrarExito(data.message || 'Cita confirmada.');
            mainCalendar?.refetchEvents();
            cerrarModal('modal-detalle-cita');
            recargarTablas();
        } catch (e) { mostrarError(e.message); }
    }

    async function cancelarCita(id) {
        const motivo = await pedirMotivo('cancelar', 'Motivo de cancelación (obligatorio)');
        if (!motivo) return;
        try {
            const data = await peticionAccion('PATCH', `${urlAcciones}/${id}/cancelar`, { cancelado_motivo: motivo });
            mostrarExito(data.message || 'Cita cancelada.');
            mainCalendar?.refetchEvents();
            cerrarModal('modal-detalle-cita');
            recargarTablas();
        } catch (e) { mostrarError(e.message); }
    }

    async function marcarNoAsistio(id) {
        const ok = await confirmarAccion('¿Marcar como no asistió?', 'Esta acción registra la inasistencia.');
        if (!ok) return;
        try {
            const data = await peticionAccion('PATCH', `${urlAcciones}/${id}/no-asistio`);
            mostrarExito(data.message || 'Marcado como no asistió.');
            mainCalendar?.refetchEvents();
            cerrarModal('modal-detalle-cita');
            recargarTablas();
        } catch (e) { mostrarError(e.message); }
    }

    async function convertirEnOrden(id) {
        const ok = await confirmarAccion('¿Convertir cita en orden?', 'Se creará una nueva orden de trabajo.');
        if (!ok) return;
        try {
            const data = await peticionAccion('POST', `${urlAcciones}/${id}/convertir-orden`);
            mostrarExito(data.message || 'Orden creada.');
            mainCalendar?.refetchEvents();
            cerrarModal('modal-detalle-cita');
            recargarTablas();
        } catch (e) { mostrarError(e.message); }
    }

    function abrirReprogramar(id) {
        abrirFormulario({ cita_id: id, accion: 'reprogramar' });
    }

    /* ===========================================================
       Formulario
       =========================================================== */

    function horaMasCercana() {
        const ahora = new Date();
        const min = ahora.getMinutes();
        const redondeo = Math.ceil(min / 30) * 30;
        ahora.setMinutes(redondeo, 0, 0);
        return ahora.toTimeString().slice(0, 5);
    }

    function abrirFormulario({ fecha, hora, cita_id, accion = 'crear' }) {
        const modalEl = document.getElementById('modal-formulario-cita');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const form = document.getElementById('formulario-cita');
        form.reset();
        form.classList.remove('was-validated');
        document.getElementById('formulario-errores').classList.add('d-none');
        document.getElementById('formulario-errores').innerHTML = '';
        document.getElementById('reprogramar-fields').classList.add('d-none');

        document.getElementById('form-cita_id').value = cita_id || '';
        document.getElementById('form-__accion').value = accion;

        if (accion === 'reprogramar') {
            document.getElementById('modal-formulario-titulo').textContent = 'Reprogramar cita';
            cargarDatosReprogramar(cita_id);
        } else if (cita_id) {
            document.getElementById('modal-formulario-titulo').textContent = 'Editar cita';
            cargarDatosEdicion(cita_id);
        } else {
            document.getElementById('modal-formulario-titulo').textContent = 'Nueva cita';
            const hoy = new Date().toISOString().slice(0, 10);
            const fechaFinal = fecha || hoy;
            document.getElementById('form-fecha').value = fechaFinal;
            document.getElementById('form-fecha').setAttribute('min', hoy);
            document.getElementById('form-hora').value = hora || horaMasCercana();
        }
        modal.show();
    }

    async function cargarDatosEdicion(id) {
        try {
            const res = await fetch(`${urlMostrar}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            poblarFormulario(await res.json());
        } catch (e) { mostrarError('No se pudo cargar la cita.'); }
    }

    async function cargarDatosReprogramar(id) {
        document.getElementById('reprogramar-fields').classList.remove('d-none');
        try {
            const res = await fetch(`${urlMostrar}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const c = await res.json();
            poblarFormulario(c);
            document.getElementById('reprogramar-info').textContent = `Cita original: ${c.cliente} – ${c.fecha_label} ${formatHora(c.hora)}`;
        } catch (e) { mostrarError('No se pudo cargar la cita.'); }
    }

    function poblarFormulario(c) {
        document.getElementById('form-cliente_id').value = c.cliente_id || '';
        document.getElementById('form-vehiculo_id').value = c.vehiculo_id || '';
        document.getElementById('form-sucursal_id').value = c.sucursal_id || '';
        document.getElementById('form-servicio_id').value = c.servicio_id || '';
        document.getElementById('form-mecanico_id').value = c.mecanico_id || '';
        document.getElementById('form-fecha').value = c.fecha || '';
        document.getElementById('form-hora').value = formatHora(c.hora) || '';
        document.getElementById('form-tipo').value = c.tipo || 'diagnostico';
        document.getElementById('form-estado').value = c.estado || 'pendiente';
        document.getElementById('form-descripcion_problema').value = c.descripcion_problema || '';
        document.getElementById('form-costo_consulta').value = c.costo_consulta || '0';
        document.getElementById('form-deja_vehiculo').checked = c.deja_vehiculo !== undefined ? !!c.deja_vehiculo : true;
        actualizarVehiculos();
        // Disparar actualizacion de tipo (servicio, deja_vehiculo, costo)
        var evt = new Event('change');
        document.getElementById('form-tipo')?.dispatchEvent(evt);
    }

    function actualizarVehiculos() {
        const clienteId = document.getElementById('form-cliente_id')?.value;
        const select = document.getElementById('form-vehiculo_id');
        if (!select) return;
        const current = select.value;
        let data = [];
        try { data = JSON.parse(document.getElementById('vehiculos-data')?.textContent || '[]'); } catch(e) {}
        select.innerHTML = '<option value="">— Selecciona un vehículo —</option>' +
            data.filter((v) => !clienteId || String(v.cliente_id) === String(clienteId))
                .map((v) => `<option value="${v.id}" data-cliente="${v.cliente_id}">${escape(v.label)}</option>`).join('');
        if (current && [...select.options].some((o) => o.value === current)) select.value = current;
    }

    async function enviarFormulario(e) {
        e.preventDefault();
        const form = document.getElementById('formulario-cita');
        const data = Object.fromEntries(new FormData(form).entries());
        const citaId = document.getElementById('form-cita_id').value;
        const accion = document.getElementById('form-__accion').value;

        const fecha = data.fecha;
        const hora = data.hora;
        if (fecha && hora && !citaId) {
            const fechaHora = new Date(fecha + 'T' + hora);
            if (fechaHora < new Date()) {
                mostrarError('No puedes agendar una cita en el pasado.');
                return;
            }
        }

        const erroresEl = document.getElementById('formulario-errores');
        erroresEl.classList.add('d-none');
        erroresEl.innerHTML = '';

        try {
            let url, method;
            if (accion === 'reprogramar' && citaId) { url = `${urlAcciones}/${citaId}/reprogramar`; method = 'PUT'; }
            else if (citaId) { url = `${urlAcciones}/${citaId}`; method = 'PUT'; }
            else { url = urlAcciones; method = 'POST'; }

            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify(data),
            });
            const json = await res.json();
            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    Object.entries(json.errors).forEach(([field, msgs]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            let fb = input.parentElement.querySelector('.invalid-feedback');
                            if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; input.parentElement.appendChild(fb); }
                            fb.textContent = Array.isArray(msgs) ? msgs.join(', ') : String(msgs);
                        }
                    });
                    erroresEl.innerHTML = '<i class="bi bi-exclamation-octagon-fill"></i> Revisa los datos.';
                    erroresEl.classList.remove('d-none');
                } else {
                    mostrarError(json.message || 'Error al guardar.');
                }
                return;
            }
            mostrarExito(json.message || 'Cita guardada.');
            cerrarModal('modal-formulario-cita');
            mainCalendar?.refetchEvents();
            recargarTablas();
        } catch (err) {
            mostrarError(err.message || 'Error de red.');
        }
    }

    /* ===========================================================
       Quick create Cliente / Vehículo
       =========================================================== */

    function limpiarQuickForm(id) {
        const form = document.getElementById(id);
        if (!form) return;
        form.reset();
        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        const err = form.querySelector('.alert-danger');
        if (err) { err.classList.add('d-none'); err.innerHTML = ''; }
    }

    function abrirQuickCliente() {
        limpiarQuickForm('form-quick-cliente');
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-quick-cliente'));
        modal.show();
    }

    async function enviarQuickCliente(e) {
        e.preventDefault();
        const form = document.getElementById('form-quick-cliente');
        const data = Object.fromEntries(new FormData(form).entries());
        const erroresEl = document.getElementById('quick-cliente-errores');
        erroresEl.classList.add('d-none');
        erroresEl.innerHTML = '';
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));

        try {
            const res = await fetch(urlAcciones + '/quick-cliente', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify(data),
            });
            const json = await res.json();
            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    Object.entries(json.errors).forEach(([field, msgs]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            let fb = input.parentElement.querySelector('.invalid-feedback');
                            if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; input.parentElement.appendChild(fb); }
                            fb.textContent = Array.isArray(msgs) ? msgs.join(', ') : String(msgs);
                        }
                    });
                    erroresEl.innerHTML = '<i class="bi bi-exclamation-octagon-fill"></i> Revisa los datos.';
                    erroresEl.classList.remove('d-none');
                } else {
                    mostrarError(json.message || 'Error al guardar cliente.');
                }
                return;
            }
            const c = json.cliente;
            const select = document.getElementById('form-cliente_id');
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.nombre_completo;
            select.appendChild(opt);
            select.value = c.id;
            select.dispatchEvent(new Event('change'));
            cerrarModal('modal-quick-cliente');
            mostrarExito(json.message || 'Cliente registrado.');
        } catch (err) {
            mostrarError(err.message || 'Error de red.');
        }
    }

    function abrirQuickVehiculo() {
        const clienteId = document.getElementById('form-cliente_id')?.value;
        if (!clienteId) {
            mostrarError('Primero selecciona o agrega un cliente.');
            return;
        }
        document.getElementById('quick-vehiculo-cliente_id').value = clienteId;
        limpiarQuickForm('form-quick-vehiculo');
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-quick-vehiculo'));
        modal.show();
    }

    async function enviarQuickVehiculo(e) {
        e.preventDefault();
        const form = document.getElementById('form-quick-vehiculo');
        const data = Object.fromEntries(new FormData(form).entries());
        const erroresEl = document.getElementById('quick-vehiculo-errores');
        erroresEl.classList.add('d-none');
        erroresEl.innerHTML = '';
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));

        try {
            const res = await fetch(urlAcciones + '/quick-vehiculo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify(data),
            });
            const json = await res.json();
            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    Object.entries(json.errors).forEach(([field, msgs]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            let fb = input.parentElement.querySelector('.invalid-feedback');
                            if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; input.parentElement.appendChild(fb); }
                            fb.textContent = Array.isArray(msgs) ? msgs.join(', ') : String(msgs);
                        }
                    });
                    erroresEl.innerHTML = '<i class="bi bi-exclamation-octagon-fill"></i> Revisa los datos.';
                    erroresEl.classList.remove('d-none');
                } else {
                    mostrarError(json.message || 'Error al guardar vehículo.');
                }
                return;
            }
            const v = json.vehiculo;
            const select = document.getElementById('form-vehiculo_id');
            const opt = document.createElement('option');
            opt.value = v.id;
            opt.textContent = v.label;
            opt.dataset.cliente = v.cliente_id;
            select.appendChild(opt);
            select.value = v.id;
            cerrarModal('modal-quick-vehiculo');
            mostrarExito(json.message || 'Vehículo registrado.');
            actualizarVehiculos();
        } catch (err) {
            mostrarError(err.message || 'Error de red.');
        }
    }

    /* ===========================================================
       Helpers UX
       =========================================================== */

    function cerrarModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        bootstrap.Modal.getInstance(el)?.hide();
    }

    async function confirmarAccion(titulo, mensaje) {
        if (window.TPConfirm?.ask) {
            return new Promise((resolve) => {
                window.TPConfirm.ask({ title: titulo, message: mensaje, confirmText: 'Confirmar', confirmClass: 'btn-danger', icon: 'warning' })
                    .then((ok) => resolve(!!ok));
            });
        }
        return Promise.resolve(confirm(mensaje + '\n\n¿Continuar?'));
    }

    async function pedirMotivo(accion, label) {
        return new Promise((resolve) => {
            const modalEl = document.getElementById('modal-motivo');
            if (!modalEl) { const m = prompt(label); resolve(m || null); return; }
            const input = document.getElementById('motivo-texto');
            const labelEl = document.getElementById('motivo-label');
            const titleEl = document.getElementById('motivo-titulo');
            const btnOk = document.getElementById('motivo-ok');
            if (input) input.value = '';
            if (labelEl) labelEl.textContent = label;
            if (titleEl) titleEl.textContent = accion === 'cancelar' ? 'Cancelar cita' : 'Reprogramar cita';
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            const handler = () => {
                const val = input?.value?.trim() || '';
                if (val.length < 3) { input?.classList.add('is-invalid'); return; }
                modal.hide();
                btnOk.removeEventListener('click', handler);
                resolve(val);
            };
            btnOk.addEventListener('click', handler);
        });
    }

    function recargarTablas() {
        const fecha = mainCalendar ? mainCalendar.getDate().toISOString().slice(0, 10) : fechaInicial;
        cargarCitasDelDia(fecha);
        cargarProximasCitas();
    }

    /* ===========================================================
       Boot
       =========================================================== */

    document.addEventListener('DOMContentLoaded', function () {
        initMainCalendar();
        initMiniCalendar();
        bindToolbar();
        recargarTablas();
        actualizarBotonesVista();

        let resizeTimer = null;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (!mainCalendar) return;
                if (window.innerWidth < 768 && !['timeGridDay','listWeek'].includes(mainCalendar.view.type)) {
                    mainCalendar.changeView('timeGridDay');
                    actualizarBotonesVista();
                }
            }, 200);
        });

        document.getElementById('form-cliente_id')?.addEventListener('change', actualizarVehiculos);
        document.getElementById('formulario-cita')?.addEventListener('submit', enviarFormulario);

        document.getElementById('btn-quick-cliente')?.addEventListener('click', abrirQuickCliente);
        document.getElementById('form-quick-cliente')?.addEventListener('submit', enviarQuickCliente);
        document.getElementById('btn-quick-vehiculo')?.addEventListener('click', abrirQuickVehiculo);
        document.getElementById('form-quick-vehiculo')?.addEventListener('submit', enviarQuickVehiculo);
    });
})();
