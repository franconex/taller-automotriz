import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const isDark = () => document.documentElement.classList.contains('dark');

const textColor = () => (isDark() ? '#9CA3AF' : '#6B7280');
const gridColor = () => (isDark() ? '#1F2937' : '#E5E7EB');

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('ordenesChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'En progreso', 'Completadas', 'Canceladas'],
            datasets: [{
                data: [12, 8, 45, 3],
                backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor(),
                        padding: 16,
                        usePointStyle: true,
                        font: { size: 12 },
                    },
                },
            },
        },
    });

    // Observar cambios de tema
    const observer = new MutationObserver(() => {
        Chart.instances.forEach(instance => instance.update());
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
