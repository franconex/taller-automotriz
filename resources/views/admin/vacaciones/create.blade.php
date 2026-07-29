@extends('layouts.admin')

@section('title', 'Solicitar vacaciones')
@section('navbar-title', 'Solicitar vacaciones')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.vacaciones.index') }}">Vacaciones</a></li>
    <li class="active" aria-current="page">Solicitar</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.vacaciones.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio') }}" required min="{{ now()->toDateString() }}">
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin') }}" required min="{{ now()->toDateString() }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <textarea name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror"
                                      rows="4" required maxlength="500">{{ old('motivo') }}</textarea>
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
