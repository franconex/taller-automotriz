@extends('layouts.admin')

@section('title', 'Mover stock entre sucursales')
@section('navbar-title', 'Mover stock')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.inventario.index') }}">Inventario</a></li>
    <li><a href="{{ route('admin.inventario.consolidado') }}">Consolidado</a></li>
    <li class="active" aria-current="page">Mover</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Mover stock entre sucursales"
        description="Transfiere unidades de una sucursal a otra. El stock se descuenta del origen y se suma al destino automáticamente.">
        <x-slot:actions>
            <a href="{{ route('admin.inventario.consolidado') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="GET" action="{{ route('admin.inventario.mover') }}" class="mb-4">
            <label for="repuesto_id" class="form-label">Producto</label>
            <div class="d-flex gap-2 flex-wrap">
                <select name="repuesto_id" id="repuesto_id" class="form-select" required style="max-width:500px;">
                    <option value="">— Seleccionar producto —</option>
                    @foreach (\App\Models\Repuesto::where('estado', true)->orderBy('nombre')->get() as $r)
                        <option value="{{ $r->id }}" @selected($repuesto && $repuesto->id === $r->id)>
                            {{ $r->nombre }} ({{ $r->codigo }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Seleccionar
                </button>
            </div>
        </form>

        @if ($repuesto)
            <hr class="my-4">

            <div class="mb-3">
                <h3 class="h6 fw-bold mb-1">{{ $repuesto->nombre }}</h3>
                <p class="cell-muted small mb-0">
                    Cód: <strong>{{ $repuesto->codigo }}</strong>
                    @if ($repuesto->codigo_barras)
                        · Barras: <strong>{{ $repuesto->codigo_barras }}</strong>
                    @endif
                </p>
            </div>

            @if ($sucursalesOrigen->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                    No hay stock disponible de este producto en ninguna sucursal para mover.
                </div>
            @else
                <form method="POST" action="{{ route('admin.inventario.procesar-movimiento') }}">
                    @csrf
                    <input type="hidden" name="repuesto_id" value="{{ $repuesto->id }}">

                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label for="sucursal_origen_id" class="form-label">Sucursal de origen</label>
                            <select name="sucursal_origen_id" id="sucursal_origen_id" class="form-select" required>
                                <option value="">— Seleccionar —</option>
                                @foreach ($sucursalesOrigen as $so)
                                    <option value="{{ $so->id }}" @selected(request('origen_id') == $so->id)>
                                        {{ $so->nombre }} — {{ $so->disponible }} disponibles
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Sucursales con stock disponible de este producto.</div>
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end justify-content-center">
                            <i class="bi bi-arrow-right fs-2 text-muted" aria-hidden="true"></i>
                        </div>

                        <div class="col-12 col-md-5">
                            <label for="sucursal_destino_id" class="form-label">Sucursal de destino</label>
                            <select name="sucursal_destino_id" id="sucursal_destino_id" class="form-select" required>
                                <option value="">— Seleccionar —</option>
                                @foreach ($sucursales as $sd)
                                    <option value="{{ $sd->id }}">
                                        {{ $sd->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">La sucursal que recibirá el stock.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="cantidad" class="form-label">Cantidad a mover</label>
                            <input type="number" name="cantidad" id="cantidad" min="1" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label for="motivo" class="form-label">Motivo del traslado</label>
                            <textarea name="motivo" id="motivo" class="form-control" rows="2" required
                                      placeholder="Ej: Reposición de stock, Sucursal destino sin stock, etc."></textarea>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        <strong>Lo que va a pasar:</strong> se descontará de la sucursal de origen y se sumará al stock de la sucursal de destino. Ambos movimientos quedarán registrados con el motivo.
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('¿Confirmar el traslado de stock?')">
                            <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                            Mover stock
                        </button>
                        <a href="{{ route('admin.inventario.consolidado', ['repuesto_id' => $repuesto->id]) }}"
                           class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            @endif
        @else
            <div class="text-center cell-muted py-4">
                <i class="bi bi-arrow-up-circle fs-1" aria-hidden="true"></i>
                <p class="mt-2 mb-0">Selecciona un producto arriba para ver dónde tiene stock y poder moverlo.</p>
            </div>
        @endif
    </div>
@endsection
