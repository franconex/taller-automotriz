@extends('layouts.admin')

@section('title', 'Autorizaciones')
@section('navbar-title', 'Autorizaciones')

@section('content')
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Orden</th>
                    <th>Cliente</th>
                    <th>Título</th>
                    <th>Importe</th>
                    <th>Estado</th>
                    <th>Solicitud</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($autorizaciones as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ $a->ordenTrabajo?->numero_orden ?? '—' }}</td>
                        <td>{{ $a->ordenTrabajo?->cliente?->nombre_completo ?? '—' }}</td>
                        <td>{{ $a->titulo }}</td>
                        <td>${{ number_format($a->importe, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $a->estado === 'autorizada' ? 'success' : ($a->estado === 'rechazada' || $a->estado === 'cancelada' ? 'danger' : ($a->estado === 'pendiente' ? 'warning' : 'info')) }}">
                                {{ $a->estado_label }}
                            </span>
                        </td>
                        <td>{{ $a->fecha_solicitud?->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.autorizaciones.show', $a) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Sin solicitudes de autorización</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $autorizaciones->links() }}
@endsection
