@extends('layouts.cliente-sidebar')

@section('title', 'Solicitar cita')
@section('navbar-title', 'Solicitar cita')

@section('content')
    <div class="mb-3">
        <a href="{{ route('cliente.citas') }}" class="text-decoration-none small">&larr; Volver a mis citas</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Nueva solicitud de cita</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.citas.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vehiculo_id" class="form-label small fw-semibold">Vehículo <span class="text-danger">*</span></label>
                        <select name="vehiculo_id" id="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                            <option value="">Seleccionar vehículo</option>
                            @foreach ($vehiculos as $v)
                                <option value="{{ $v->id }}" {{ old('vehiculo_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->placa }} — {{ $v->marca ?? '' }} {{ $v->modelo ?? '' }} ({{ $v->anio ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="servicio_id" class="form-label small fw-semibold">Servicio</label>
                        <select name="servicio_id" id="servicio_id" class="form-select @error('servicio_id') is-invalid @enderror">
                            <option value="">Seleccionar servicio</option>
                            @foreach ($servicios as $s)
                                <option value="{{ $s->id }}" {{ old('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        @error('servicio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label small fw-semibold">Tipo de servicio <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="diagnostico" {{ old('tipo') === 'diagnostico' ? 'selected' : '' }}>Diagnóstico</option>
                            <option value="mantenimiento" {{ old('tipo') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="reparacion" {{ old('tipo') === 'reparacion' ? 'selected' : '' }}>Reparación</option>
                            <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sucursal_id" class="form-label small fw-semibold">Sucursal <span class="text-danger">*</span></label>
                        <select name="sucursal_id" id="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror" required>
                            <option value="">Seleccionar sucursal</option>
                            @foreach ($sucursales as $s)
                                <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        @error('sucursal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha" class="form-label small fw-semibold">Fecha preferida <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha') }}" min="{{ now()->format('Y-m-d') }}" required>
                        @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="hora" class="form-label small fw-semibold">Hora preferida <span class="text-danger">*</span></label>
                        <input type="time" name="hora" id="hora" class="form-control @error('hora') is-invalid @enderror"
                               value="{{ old('hora') }}" required>
                        @error('hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion_problema" class="form-label small fw-semibold">Descripción del problema</label>
                        <textarea name="descripcion_problema" id="descripcion_problema" class="form-control @error('descripcion_problema') is-invalid @enderror"
                                  rows="3" placeholder="Describe el problema o el servicio que necesitas...">{{ old('descripcion_problema') }}</textarea>
                        @error('descripcion_problema')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="deja_vehiculo" id="deja_vehiculo" value="1" {{ old('deja_vehiculo') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="deja_vehiculo">Dejaré el vehículo en el taller</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="observaciones" class="form-label small fw-semibold">Observaciones</label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                               value="{{ old('observaciones') }}" placeholder="Ej: llegaré después de las 14:00">
                        @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <hr>
                        <p class="small text-muted">Tu cita se registrará como <strong>solicitada</strong>. El taller la confirmará a la brevedad y, si es necesario, podrá proponer un horario alternativo.</p>
                        <button type="submit" class="btn text-white" style="background:#E31E24;">
                            <i class="bi bi-calendar-check me-1"></i>Solicitar cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
