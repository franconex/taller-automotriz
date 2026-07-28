@extends('layouts.admin')

@section('title', 'Subservicios')
@section('navbar-title', 'Subservicios')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="servicio_id" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Todos los servicios</option>
                @foreach ($servicios as $s)
                    <option value="{{ $s->id }}" {{ request('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.subservicios.create') }}" class="btn btn-sm text-white" style="background:#E31E24;">
            <i class="bi bi-plus-lg"></i> Nuevo subservicio
        </a>
    </div>

    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Servicio</th>
                    <th>Tipo</th>
                    <th>Precio base</th>
                    <th>Duración</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subservicios as $sub)
                    <tr>
                        <td class="fw-semibold">{{ $sub->nombre }}</td>
                        <td>{{ $sub->servicio?->nombre ?? '—' }}</td>
                        <td class="small text-muted">{{ $sub->servicio?->tipoServicio?->nombre ?? '—' }}</td>
                        <td>${{ number_format($sub->precio_base, 2) }}</td>
                        <td>{{ $sub->duracion_estimada_minutos ? $sub->duracion_estimada_minutos . ' min' : '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $sub->estado ? 'success' : 'secondary' }}">
                                {{ $sub->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.subservicios.edit', $sub) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.subservicios.toggle', $sub) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-toggle-{{ $sub->estado ? 'on' : 'off' }}"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin subservicios registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $subservicios->links() }}
@endsection
