@extends('layouts.cliente-sidebar')

@section('title', 'Historial')
@section('navbar-title', 'Historial')

@section('content')
    @if ($ordenes->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-clock-history display-4 d-block mb-3"></i>
            <p>No tienes órdenes anteriores.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th># Orden</th>
                        <th>Vehículo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $o)
                        <tr>
                            <td class="fw-semibold">{{ $o->numero_orden }}</td>
                            <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                            <td>{{ $o->fecha_emision?->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $o->estado === 'finalizada' ? 'success' : ($o->estado === 'entregada' ? 'success' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->estado)) }}
                                </span>
                            </td>
                            <td>${{ number_format($o->total_general, 2) }}</td>
                            <td>
                                @can('view', $o)
                                <a href="{{ route('cliente.orden-show', $o) }}" class="btn btn-sm text-white" style="background:#E31E24;">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
