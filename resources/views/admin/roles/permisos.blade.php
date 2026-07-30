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
        description="Selecciona un módulo y marca los permisos que tendrá asignado este perfil.">
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

        <div class="admin-card-modern mb-3">
            <div class="p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="modulo-selector" class="form-label fw-medium">Módulo</label>
                        <select id="modulo-selector" class="form-select">
                            <option value="all">— Todos los módulos —</option>
                            @foreach ($permisos as $modulo => $lista)
                                <option value="mod-{{ Str::slug($modulo) }}">{{ ucfirst($modulo) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-7 d-flex gap-2 flex-wrap">
                        <button type="button" id="btn-select-all-module" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-check-all"></i> Seleccionar todos (módulo actual)
                        </button>
                        <button type="button" id="btn-deselect-all-module" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x"></i> Deseleccionar todos (módulo actual)
                        </button>
                        <button type="button" id="btn-select-all-global" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-all"></i> Seleccionar todos los permisos
                        </button>
                        <button type="button" id="btn-deselect-all-global" class="btn btn-sm btn-secondary">
                            <i class="bi bi-x-circle"></i> Deseleccionar todos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @forelse ($permisos as $modulo => $lista)
            @php $modId = 'mod-' . Str::slug($modulo); @endphp
            <div class="admin-card-modern mb-3 overflow-hidden modulo-card" id="{{ $modId }}" data-modulo="{{ $modId }}">
                <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center" style="width:30px;height:30px;border-radius:8px;background:#e8f4fd;color:#2563eb;font-size:0.9rem;">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <h2 class="fw-bold mb-0" style="font-size:0.9rem;letter-spacing:.3px;text-transform:uppercase;">
                            {{ ucfirst($modulo) }}
                        </h2>
                    </div>
                    <span class="d-inline-flex align-items-center gap-1" style="background:#e2e8f0;color:#475569;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;">
                        <i class="bi bi-check2-square"></i>
                        {{ $lista->count() }}
                    </span>
                </div>
                <div class="p-4">
                    <div class="row g-2">
                        @foreach ($lista as $permiso)
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="d-flex align-items-start gap-2 p-2 rounded" style="transition:all 0.15s;cursor:pointer;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background=''">
                                    <input type="checkbox"
                                           name="permisos[]"
                                           value="{{ $permiso->id }}"
                                           id="permiso-{{ $permiso->id }}"
                                           class="form-check-input mt-0 permiso-checkbox"
                                           style="cursor:pointer;"
                                           data-modulo="{{ $modId }}"
                                           @checked(in_array($permiso->id, $asignados))>
                                    <div>
                                        <div class="fw-medium" style="font-size:0.85rem;line-height:1.3;">{{ $permiso->nombre }}</div>
                                        <div style="font-size:0.7rem;color:#94a3b8;">{{ $permiso->codigo }}</div>
                                    </div>
                                </label>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var selector = document.getElementById('modulo-selector');
        var cards = document.querySelectorAll('.modulo-card');

        // Filter by module
        function filtrar() {
            var selected = selector.value;
            cards.forEach(function (card) {
                if (selected === 'all' || card.id === selected) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        selector.addEventListener('change', filtrar);

        // Select all / deselect all for current module
        document.getElementById('btn-select-all-module').addEventListener('click', function () {
            var selected = selector.value;
            document.querySelectorAll('.permiso-checkbox').forEach(function (cb) {
                if (selected === 'all' || cb.getAttribute('data-modulo') === selected) {
                    cb.checked = true;
                }
            });
        });

        document.getElementById('btn-deselect-all-module').addEventListener('click', function () {
            var selected = selector.value;
            document.querySelectorAll('.permiso-checkbox').forEach(function (cb) {
                if (selected === 'all' || cb.getAttribute('data-modulo') === selected) {
                    cb.checked = false;
                }
            });
        });

        // Global select all / deselect all
        document.getElementById('btn-select-all-global').addEventListener('click', function () {
            document.querySelectorAll('.permiso-checkbox').forEach(function (cb) { cb.checked = true; });
        });

        document.getElementById('btn-deselect-all-global').addEventListener('click', function () {
            document.querySelectorAll('.permiso-checkbox').forEach(function (cb) { cb.checked = false; });
        });
    });
</script>
@endpush