@extends('layouts.admin')

@section('title', 'Nueva solicitud de permiso')
@section('navbar-title', 'Nueva solicitud')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.solicitudes-permiso.index') }}">Solicitudes de permiso</a></li>
    <li class="active" aria-current="page">Nueva</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Realizar una solicitud de permiso"
        description="Selecciona el permiso que necesitas y explica el motivo.">
        <x-slot:actions>
            <a href="{{ route('admin.solicitudes-permiso.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4">
        <form method="POST" action="{{ route('admin.solicitudes-permiso.store') }}">
            @csrf

            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Selecciona el permiso</h3>
                @if ($permisosAgrupados->isEmpty() || $permisosAgrupados->every(fn ($g) => $g->isEmpty()))
                    <div class="text-muted small">Ya tienes todos los permisos disponibles. No hay permisos nuevos para solicitar.</div>
                @else
                    <div class="row g-3">
                        @foreach ($permisosAgrupados as $modulo => $permisos)
                            @if ($permisos->isNotEmpty())
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <h4 class="h6 fw-bold mb-2 text-uppercase" style="letter-spacing:0.04em;color:var(--muted,#64748b);">{{ $modulo }}</h4>
                                        <div class="row g-2">
                                            @foreach ($permisos as $p)
                                                <div class="col-12 col-md-6 col-lg-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="permiso_id"
                                                               id="permiso_{{ $p->id }}" value="{{ $p->id }}"
                                                               @checked(old('permiso_id') == $p->id) required>
                                                        <label class="form-check-label" for="permiso_{{ $p->id }}">
                                                            <strong>{{ $p->nombre }}</strong>
                                                            <br><small class="text-muted">{{ $p->codigo }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="admin-form-section">
                <h3 class="admin-form-section__title">Motivo</h3>
                <div class="mb-3">
                    <textarea name="motivo"
                              id="field-motivo"
                              class="form-control @error('motivo') is-invalid @enderror"
                              rows="4"
                              maxlength="500"
                              placeholder="Explica por qué necesitas este permiso..." required>{{ old('motivo') }}</textarea>
                    @error('motivo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.solicitudes-permiso.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send" aria-hidden="true"></i>
                    Enviar solicitud
                </button>
            </div>
        </form>
    </div>
@endsection
