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
            <div class="admin-card-modern p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="badge-module" style="background:#fffbeb;color:#d97706;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:1.1rem;">
                        <i class="bi bi-sun-fill"></i>
                    </span>
                    <div>
                        <h2 class="fw-bold mb-0" style="font-size:1.05rem;">Solicitar vacaciones</h2>
                        <p class="cell-secondary small mb-0">Completa los datos para enviar tu solicitud.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.vacaciones.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label fw-medium">
                            Fecha de inicio <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;">
                                <i class="bi bi-calendar2" style="color:#64748b;"></i>
                            </span>
                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio') }}" required min="{{ now()->toDateString() }}"
                                   style="border-left:0;">
                        </div>
                        @error('fecha_inicio')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label fw-medium">
                            Fecha de fin <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" style="border-right:0;">
                                <i class="bi bi-calendar2" style="color:#64748b;"></i>
                            </span>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin') }}" required min="{{ now()->toDateString() }}"
                                   style="border-left:0;">
                        </div>
                        @error('fecha_fin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label fw-medium">
                            Motivo <span class="required" aria-hidden="true">*</span>
                        </label>
                        <textarea name="motivo" id="motivo"
                                  class="form-control @error('motivo') is-invalid @enderror"
                                  rows="4" required maxlength="500" placeholder="Describe el motivo de tu solicitud...">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send" aria-hidden="true"></i>
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection