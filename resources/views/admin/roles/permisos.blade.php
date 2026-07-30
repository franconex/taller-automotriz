@extends('layouts.admin')

@section('title', 'Permisos del rol')
@section('navbar-title', 'Permisos del rol')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.roles.index') }}">Roles y permisos</a></li>
    <li class="active" aria-current="page">{{ $rol->nombre }}</li>
@endsection

@section('content')
    <x-admin.page-header
        :title="'Permisos de ' . $rol->nombre"
        description="Marca los permisos que tendrá asignado este perfil.">
        <x-slot:actions>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.roles.actualizar-permisos', $rol) }}">
        @csrf
        @method('PUT')

        @forelse ($permisos as $modulo => $lista)
            <div class="admin-card-modern mb-3 overflow-hidden">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background:#f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-module" style="background:#e8f4fd;color:#2563eb;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:0.85rem;">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <h2 class="fw-bold mb-0" style="font-size:0.85rem;letter-spacing:.4px;">
                            {{ ucfirst($modulo) }}
                        </h2>
                    </div>
                    <span class="badge" style="background:#e2e8f0;color:#475569;font-size:0.7rem;font-weight:600;">{{ $lista->count() }} permisos</span>
                </div>
                <div class="p-4">
                    <div class="row g-2">
                        @foreach ($lista as $permiso)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-check py-1 px-2 rounded" style="transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="permisos[]"
                                        value="{{ $permiso->id }}"
                                        id="permiso-{{ $permiso->id }}"
                                        @checked(in_array($permiso->id, $asignados))>
                                    <label class="form-check-label" for="permiso-{{ $permiso->id }}">
                                        {{ $permiso->nombre }}
                                        <span class="d-block cell-muted small" style="font-size:.75rem;">
                                            {{ $permiso->codigo }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <x-admin.empty-state
                icon="bi-shield-lock"
                title="Sin permisos definidos"
                message="Crea permisos en el sistema para poder asignarlos." />
        @endforelse

        @if ($permisos->isNotEmpty())
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2" aria-hidden="true"></i>
                    Guardar permisos
                </button>
            </div>
        @endif
    </form>
@endsection