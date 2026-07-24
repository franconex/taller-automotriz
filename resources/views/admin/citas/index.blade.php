@extends('layouts.admin')

@section('title', 'Calendario de Citas')
@section('navbar-title', 'Calendario de Citas')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Calendario de Citas</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Calendario de Citas"
        description="Gestiona y agenda citas de clientes" />

    @php
        $puedeCrear = $puedeCrear ?? false;
    @endphp

    {{-- Toolbar superior --}}
    <div class="citas-toolbar" role="toolbar" aria-label="Controles del calendario">
        <div class="citas-toolbar__group">
            <button type="button" class="citas-toolbar__btn" data-cal-action="today" aria-label="Ir a hoy">Hoy</button>
            <button type="button" class="citas-toolbar__btn citas-toolbar__btn--icon" data-cal-action="prev" aria-label="Anterior">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="citas-toolbar__btn citas-toolbar__btn--icon" data-cal-action="next" aria-label="Siguiente">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        <div class="citas-toolbar__rango" id="citas-rango" tabindex="0" role="button" aria-label="Rango visible">—</div>

        <div class="citas-toolbar__group">
            <div class="citas-views" role="tablist" aria-label="Cambiar vista">
                <button type="button" class="citas-views__btn" data-cal-view="timeGridDay" role="tab" aria-label="Vista día">Día</button>
                <button type="button" class="citas-views__btn is-active" data-cal-view="timeGridWeek" role="tab" aria-label="Vista semana">Semana</button>
                <button type="button" class="citas-views__btn" data-cal-view="dayGridMonth" role="tab" aria-label="Vista mes">Mes</button>
            </div>
        </div>

    </div>

    <div id="citas-app"
         data-fecha="{{ $fechaSeleccionada }}"
         data-url-eventos="{{ route('admin.citas.eventos') }}"
         data-url-mostrar="{{ url('admin/citas') }}"
         data-url-acciones="{{ url('admin/citas') }}"
         data-url-nueva="{{ url('admin/citas') }}"
         data-url-editar="{{ url('admin/citas') }}"
         data-url-tabla-dia="{{ route('admin.citas.tabla-dia') }}"
         data-url-proximas="{{ route('admin.citas.proximas') }}"
         data-crear="{{ $puedeCrear ? '1' : '0' }}"
         data-reprogramar="{{ ($puedeReprogramar ?? false) ? '1' : '0' }}">
        <div class="citas-layout">
            {{-- Columna principal --}}
            <div class="citas-main">
                <div id="citas-loading" class="citas-loading" aria-hidden="true">
                    <div class="citas-loading__spinner"></div>
                </div>
                <div id="calendario-citas" role="region" aria-label="Calendario de citas"></div>

                <h2 class="citas-dia-titulo" id="citas-dia-titulo">Citas del Día</h2>
                <div id="citas-dia-contenido" aria-live="polite">
                    <div class="citas-empty">
                        <i class="bi bi-arrow-clockwise citas-empty__icon" aria-hidden="true"></i>
                        <p class="citas-empty__text">Cargando…</p>
                    </div>
                </div>
            </div>

            {{-- Columna lateral --}}
            <aside class="citas-aside" aria-label="Panel lateral de citas">
                @if ($puedeCrear)
                    <button type="button" class="citas-aside__nueva" data-tp-open-modal="nueva-cita" aria-label="Crear nueva cita">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i> Nueva Cita
                    </button>
                @endif

                <div class="citas-aside__card">
                    <h3 class="citas-aside__title">
                        <i class="bi bi-calendar3" aria-hidden="true"></i> Mini calendario
                    </h3>
                    <div id="mini-calendario" role="region" aria-label="Mini calendario"></div>
                </div>

                <div class="citas-aside__card">
                    <h3 class="citas-aside__title">Filtros</h3>

                    @if (! empty($mostrarFiltroSucursal) && $mostrarFiltroSucursal)
                        <div class="citas-filter">
                            <label class="citas-filter__label" for="filtro-sucursal">Sucursal</label>
                            <select id="filtro-sucursal" class="citas-filter__select">
                                <option value="">Todas las sucursales</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="citas-filter">
                        <label class="citas-filter__label" for="filtro-servicio">Servicio</label>
                        <select id="filtro-servicio" class="citas-filter__select">
                            <option value="">Todos los servicios</option>
                            @foreach ($servicios as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="citas-filter">
                        <label class="citas-filter__label" for="filtro-mecanico">Mecánico</label>
                        <select id="filtro-mecanico" class="citas-filter__select">
                            <option value="">Todos los mecánicos</option>
                            @foreach ($mecanicos as $m)
                                <option value="{{ $m->id }}">{{ $m->empleado->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="citas-filter">
                        <label class="citas-filter__label" for="filtro-estado">Estado</label>
                        <select id="filtro-estado" class="citas-filter__select">
                            <option value="">Todas las citas</option>
                            @foreach ($estados as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" id="citas-limpiar-filtros" class="citas-filter__clear">
                        <i class="bi bi-x-circle" aria-hidden="true"></i> Limpiar filtros
                    </button>
                </div>

                <div class="citas-aside__card">
                    <h3 class="citas-aside__title">Leyenda</h3>
                    <ul class="citas-legend">
                        <li><span class="citas-legend__dot" style="background:#16A34A"></span> Confirmada</li>
                        <li><span class="citas-legend__dot" style="background:#F59E0B"></span> Pendiente</li>
                        <li><span class="citas-legend__dot" style="background:#0891B2"></span> Atendida</li>
                        <li><span class="citas-legend__dot" style="background:#9CA3AF"></span> Cancelada</li>
                        <li><span class="citas-legend__dot" style="background:#B91C1C"></span> No asistió</li>
                    </ul>
                </div>

                <div class="citas-aside__card">
                    <h3 class="citas-aside__title">Próximas Citas</h3>
                    <ul class="citas-proximas" id="citas-proximas-lista">
                        <li class="citas-empty">
                            <i class="bi bi-arrow-clockwise citas-empty__icon" aria-hidden="true"></i>
                            <p class="citas-empty__text">Cargando…</p>
                        </li>
                    </ul>
                    <a href="{{ route('admin.citas.index') }}" class="citas-proximas__ver">
                        Ver todas <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </aside>
        </div>
    </div>

    @include('admin.citas.partials.formulario')
    @include('admin.citas.partials.detalle')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/admin/citas.css') }}">
@endpush
@push('scripts')
    <script type="module" src="{{ Vite::asset('resources/js/admin/citas-calendario.js') }}"></script>
@endpush
