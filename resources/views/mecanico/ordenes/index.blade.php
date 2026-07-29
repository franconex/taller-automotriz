@extends('layouts.admin')

@section('title', 'Mis órdenes')
@section('navbar-title', 'Mis órdenes')

@section('content')
    @if ($ordenes->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-clipboard-check display-4 d-block mb-3"></i>
            <p>No tienes órdenes asignadas.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr><th>Orden</th><th>Cliente</th><th>Vehículo</th><th>Placa</th><th>Emisión</th><th>Estado</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $o)
                        <tr>
                            <td class="fw-semibold">{{ $o->numero_orden }}</td>
                            <td>{{ $o->cliente?->nombre_completo ?? '—' }}</td>
                            <td>{{ $o->vehiculo?->marca ?? '' }} {{ $o->vehiculo?->modelo ?? '' }}</td>
                            <td>{{ $o->vehiculo?->placa ?? '—' }}</td>
                            <td class="small">{{ $o->fecha_emision?->format('d/m/Y H:i') }}</td>
                            <td>
                                @php $colores = ['programada'=>'secondary','recibida'=>'info','diagnostico'=>'warning','en_proceso'=>'primary','esperando_repuesto'=>'purple','pausada'=>'dark','pendiente_autorizacion'=>'danger','finalizada_mecanico'=>'success','lista_entrega'=>'success','entregada'=>'secondary','cancelada'=>'danger']; @endphp
                                <span class="badge bg-{{ $colores[$o->estado] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $o->estado)) }}</span>
                            </td>
                            <td><a href="{{ route('mecanico.ordenes.show', $o) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $ordenes->links() }}
    @endif
@endsection
