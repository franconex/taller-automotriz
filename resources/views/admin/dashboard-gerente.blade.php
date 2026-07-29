@extends('layouts.admin')

@section('title', 'Dashboard — Gerente')
@section('navbar-title', 'Dashboard')

@section('content')
    <section class="admin-stats mb-4">
        <div class="admin-stats__item"><span class="admin-stats__label">En taller</span><span class="admin-stats__value">{{ $enTaller }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-danger);"><span class="admin-stats__label">Atrasadas +7d</span><span class="admin-stats__value">{{ $atrasadas }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-warning);"><span class="admin-stats__label">Citas pendientes</span><span class="admin-stats__value">{{ $citasPendientes }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-info);"><span class="admin-stats__label">Autorizaciones pend.</span><span class="admin-stats__value">{{ $autorizacionesPendientes }}</span></div>
        <div class="admin-stats__item"><span class="admin-stats__label">Ingresos hoy</span><span class="admin-stats__value">${{ number_format($ingresosDia, 0) }}</span></div>
        <div class="admin-stats__item"><span class="admin-stats__label">Ingresos del mes</span><span class="admin-stats__value">${{ number_format($ingresosMes, 0) }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-warning);"><span class="admin-stats__label">Stock bajo</span><span class="admin-stats__value">{{ $stockBajo }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-info);"><span class="admin-stats__label">Compras pendientes</span><span class="admin-stats__value">{{ $comprasPendientes }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-success);"><span class="admin-stats__label">Mec. disponibles</span><span class="admin-stats__value">{{ $mecanicosDisponibles }}</span></div>
        <div class="admin-stats__item" style="border-left:3px solid var(--tp-danger);"><span class="admin-stats__label">Mec. ocupados</span><span class="admin-stats__value">{{ $mecanicosOcupados }}</span></div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="admin-table-wrap p-3">
                <h6 class="fw-bold mb-3">Ingresos mensuales</h6>
                <canvas id="chart-ingresos" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-table-wrap p-3">
                <h6 class="fw-bold mb-3">Servicios más solicitados</h6>
                <canvas id="chart-servicios" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-table-wrap p-3">
                <h6 class="fw-bold mb-3">Órdenes por estado</h6>
                <canvas id="chart-ordenes" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-table-wrap p-3">
                <h6 class="fw-bold mb-3">Rendimiento mecánicos</h6>
                <div class="d-flex align-items-center gap-4 mt-2">
                    <div><span class="badge bg-success" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span> Disponibles: <strong>{{ $mecanicosDisponibles }}</strong></div>
                    <div><span class="badge bg-danger" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span> Ocupados: <strong>{{ $mecanicosOcupados }}</strong></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('chart-ingresos'), {type:'line',data:{labels:{!! json_encode($ingresosMensuales->pluck('mes')) !!},datasets:[{label:'Ingresos',data:{!! json_encode($ingresosMensuales->pluck('total')) !!},borderColor:'#E31E24',fill:false,tension:.3}]},options:{responsive:true}});
    new Chart(document.getElementById('chart-servicios'), {type:'doughnut',data:{labels:{!! json_encode($serviciosTop->pluck('nombre')) !!},datasets:[{data:{!! json_encode($serviciosTop->pluck('citas_count')) !!},backgroundColor:['#E31E24','#F59E0B','#16A34A','#0891B2','#6B7280']}]},options:{responsive:true}});
    new Chart(document.getElementById('chart-ordenes'), {type:'bar',data:{labels:{!! json_encode($ordenesPorEstado->keys()) !!},datasets:[{label:'Órdenes',data:{!! json_encode($ordenesPorEstado->values()) !!},backgroundColor:'#E31E24'}]},options:{responsive:true,plugins:{legend:{display:false}}}});
});
</script>
@endpush
