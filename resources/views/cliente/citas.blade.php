@extends('layouts.cliente-sidebar')

@section('title', 'Mis citas')
@section('navbar-title', 'Mis citas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Tus citas registradas</p>
        <a href="{{ route('cliente.citas.crear') }}" class="btn btn-sm text-white" style="background:#E31E24;">
            <i class="bi bi-plus-lg me-1"></i>Solicitar cita
        </a>
    </div>

    @if ($citas->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-check display-4 d-block mb-3"></i>
            <p>No tienes citas registradas.</p>
            <a href="{{ route('cliente.citas.crear') }}" class="btn text-white" style="background:#E31E24;">Solicitar una cita</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Vehículo</th>
                        <th>Servicio</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citas as $c)
                        <tr>
                            <td>{{ $c->fecha ? \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') : '—' }}</td>
                            <td>{{ $c->hora ? \Carbon\Carbon::parse($c->hora)->format('H:i') : '—' }}</td>
                            <td>{{ $c->vehiculo?->placa ?? '—' }}</td>
                            <td>{{ $c->servicio?->nombre ?? $c->tipo ?? '—' }}</td>
                            <td>{{ $c->sucursal?->nombre ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $c->estado === 'confirmada' ? 'success' : ($c->estado === 'cancelada' || $c->estado === 'rechazada' ? 'danger' : ($c->estado === 'atendida' ? 'info' : ($c->estado === 'solicitada' ? 'secondary' : 'warning'))) }}">
                                    {{ $c->estado_label }}
                                </span>
                            </td>
                            <td>
                                @if ($c->esPasableCancelar())
                                    <form method="POST" action="{{ route('cliente.citas.cancelar', $c) }}" class="d-inline" onsubmit="return confirm('¿Cancelar esta cita?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar cita">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
