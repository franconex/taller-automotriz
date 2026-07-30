@extends('layouts.admin')

@section('title', 'Métodos de pago')
@section('navbar-title', 'Métodos de pago')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Métodos de pago</li>
@endsection

@section('content')
    <x-admin.page-header title="Métodos de pago" description="Formas de pago aceptadas por el taller." />

    <x-admin.filters :action="route('admin.metodos-pago.index')" search-name="q" search-placeholder="Buscar método de pago">
        <x-slot:filters>
            <select name="estado" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                <option value="">Todos</option><option value="1" @selected(request('estado')==='1')>Activos</option><option value="0" @selected(request('estado')==='0')>Inactivos</option>
            </select>
        </x-slot:filters>
    </x-admin.filters>

    @if ($metodos->isEmpty() && !request()->has('q') && !request()->has('estado'))
        <x-admin.empty-state icon="bi-credit-card" title="No hay métodos de pago" message="Los métodos disponibles son Efectivo, Tarjeta y QR." />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table" aria-label="Listado de métodos de pago">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="d-none d-md-table-cell">Pagos</th>
                        <th>Estado</th>
                        <th class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($metodos as $m)
                        @php
                            $iconos = ['efectivo' => 'bi-cash-stack', 'tarjeta' => 'bi-credit-card-2-front', 'qr' => 'bi-qr-code-scan'];
                            $icono = 'bi-coin';
                            $nombreLower = strtolower($m->nombre);
                            foreach ($iconos as $key => $ic) { if (str_contains($nombreLower, $key)) { $icono = $ic; break; } }
                        @endphp
                        <tr style="border-left:3px solid {{ $m->estado ? '#16a34a' : '#94a3b8' }};">
                            <td>
                                <div class="cell-label">
                                    <i class="{{ $icono }}" style="color:{{ $m->estado ? '#2563eb' : '#94a3b8' }};"></i>
                                    <div>
                                        <div class="cell-strong">{{ $m->nombre }}</div>
                                        <div class="cell-secondary">Método #{{ $m->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell cell-secondary">{{ $m->descripcion ?? '—' }}</td>
                            <td class="d-none d-md-table-cell"><span class="badge" style="background:#e2e8f0;color:#475569;">{{ $m->pagos_count }}</span></td>
                            <td>
                                <x-admin.status-badge :tone="$m->estado ? 'success' : 'neutral'" :icon="$m->estado ? 'bi-check-circle-fill' : 'bi-pause-circle-fill'" :label="$m->estado ? 'Activo' : 'Inactivo'" />
                            </td>
                            <td>
                                <div class="row-actions">
                                    @php $esEfectivo = strcasecmp($m->nombre, 'Efectivo') === 0; @endphp
                                    @if (!$esEfectivo)
                                        @if (Auth::user()->tienePermiso('metodos-pago.editar'))
                                        <a href="{{ route('admin.metodos-pago.edit', $m) }}" class="btn-icon btn-icon--primary" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="{{ route('admin.metodos-pago.toggle', $m) }}" class="d-inline">@csrf @method('PATCH')<button type="submit" class="btn-icon" title="{{ $m->estado ? 'Desactivar' : 'Activar' }}"><i class="bi {{ $m->estado ? 'bi-pause-circle' : 'bi-play-circle' }}"></i></button></form>
                                        @endif
                                    @else
                                        <span class="d-inline-flex align-items-center gap-1 cell-secondary small" title="Método fijo"><i class="bi bi-lock"></i> Fijo</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-0"><x-admin.empty-state icon="bi-search" title="Sin resultados" message="No se encontraron métodos de pago." /></td></tr>
                    @endforelse
                </tbody>
            </table>
            <x-admin.table-pagination :paginator="$metodos" />
        </div>
    @endif
@endsection