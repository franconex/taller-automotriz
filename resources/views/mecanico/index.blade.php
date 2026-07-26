@extends('layouts.admin')

@section('title', 'Mis Órdenes Asignadas')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"> Mis Órdenes Asignadas</h1>
            <p class="text-muted small mb-0">Gestión e inspección técnica de vehículos a tu cargo.</p>
        </div>
        <a href="{{ route('mecanico.dashboard') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            ← Volver al Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4"># Órden</th>
                        <th>Vehículo</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $orden)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $orden->id }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $orden->vehiculo->marca ?? 'N/A' }} {{ $orden->vehiculo->modelo ?? '' }}</span>
                                <br><small class="text-muted">Placa: {{ $orden->vehiculo->placa ?? 'S/N' }}</small>
                            </td>
                            <td>{{ $orden->cliente->nombre ?? 'Cliente General' }}</td>
                            <td>
                                @if($orden->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                                @elseif($orden->estado == 'en_proceso')
                                    <span class="badge bg-primary px-3 py-2">En Proceso</span>
                                @elseif($orden->estado == 'completado')
                                    <span class="badge bg-success px-3 py-2">Completado</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">{{ ucfirst($orden->estado) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('mecanico.atender', $orden->id) }}" class="btn btn-danger btn-sm px-3 fw-bold shadow-sm">
                                    Atender Órden →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                <span>No tienes órdenes de trabajo asignadas en este momento.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection