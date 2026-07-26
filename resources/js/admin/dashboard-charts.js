import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const COLORS = {
    red: '#E31E24',
    redSoft: 'rgba(227, 30, 36, 0.12)',
    green: '#16A34A',
    greenSoft: 'rgba(22, 163, 74, 0.12)',
    blue: '#0891B2',
    blueSoft: 'rgba(8, 145, 178, 0.12)',
    amber: '#F59E0B',
    amberSoft: 'rgba(245, 158, 11, 0.12)',
    gray: '#6B7280',
    purple: '#7C3AED',
    teal: '#0D9488',
    pink: '#DB2777',
    orange: '#EA580C',
};

const ESTADO_COLORS = {
    recibida: '#0891B2',
    diagnostico: '#F59E0B',
    en_proceso: '#7C3AED',
    finalizada: '#16A34A',
    entregada: '#0D9488',
    anulada: '#9CA3AF',
};

const ETIQUETAS_ESTADO = {
    recibida: 'Recibida',
    diagnostico: 'Diagnóstico',
    en_proceso: 'En Proceso',
    finalizada: 'Finalizada',
    entregada: 'Entregada',
    anulada: 'Anulada',
};

function init() {
    const chartData = document.getElementById('dashboard-chart-data');
    if (!chartData) return;
    const data = JSON.parse(chartData.textContent);

    initOrdenesPorEstado(data.ordenes_por_estado);
    initIngresosMensuales(data.ingresos_mensuales);
    initCitasProximas(data.citas_proximas);
    initServiciosTop(data.servicios_top);
}

function initOrdenesPorEstado(data) {
    const canvas = document.getElementById('chart-ordenes-estado');
    if (!canvas || !data || !data.length) return;
    const labels = data.map(d => ETIQUETAS_ESTADO[d.estado] || d.estado);
    const values = data.map(d => d.total);
    const bgColors = data.map(d => ESTADO_COLORS[d.estado] || COLORS.gray);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Órdenes',
                data: values,
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } },
            },
        },
    });
}

function initIngresosMensuales(data) {
    const canvas = document.getElementById('chart-ingresos');
    if (!canvas || !data || !data.length) return;
    const labels = data.map(d => {
        const [y, m] = d.mes.split('-');
        const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        return meses[parseInt(m) - 1] + ' ' + y;
    });
    const values = data.map(d => parseFloat(d.total));

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Ingresos',
                data: values,
                borderColor: COLORS.green,
                backgroundColor: COLORS.greenSoft,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: COLORS.green,
                pointRadius: 4,
                pointHoverRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Bs ' + ctx.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 }),
                    },
                },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Bs ' + v.toLocaleString('es-BO') } },
                x: { grid: { display: false } },
            },
        },
    });
}

function initCitasProximas(data) {
    const canvas = document.getElementById('chart-citas');
    if (!canvas || !data || !data.length) return;
    const dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const labels = data.map(d => {
        const fecha = new Date(d.fecha + 'T12:00:00');
        return dias[fecha.getDay()] + ' ' + fecha.getDate();
    });
    const values = data.map(d => d.total);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Citas',
                data: values,
                backgroundColor: COLORS.blue,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } },
            },
        },
    });
}

function initServiciosTop(data) {
    const canvas = document.getElementById('chart-servicios');
    if (!canvas || !data || !data.length) return;
    const labels = data.map(d => d.nombre);
    const values = data.map(d => d.citas_count);
    const palette = [COLORS.red, COLORS.blue, COLORS.amber, COLORS.purple, COLORS.teal];
    const bgColors = values.map((_, i) => palette[i % palette.length]);

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: bgColors,
                borderWidth: 3,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 12, font: { size: 12 } },
                },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', init);
