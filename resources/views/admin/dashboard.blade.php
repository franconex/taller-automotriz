@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <p class="text-sm" style="color: var(--color-muted);">Bienvenido, <span class="font-semibold" style="color: var(--color-text);">{{ auth()->user()->nombre }}</span></p>
    </div>

    {{-- Alertas --}}
    @if ($alertas->isNotEmpty())
        <div class="mb-6 rounded-lg border px-4 py-3" style="background-color: #FFFBEB; border-color: #FDE68A; color: #92400E;">
            <div class="flex items-start gap-2">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm font-medium">Alertas del sistema</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5 text-sm">
                        @foreach ($alertas as $alerta)
                            <li>{{ $alerta }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- SECCIÓN 1: Indicadores --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Usuarios activos</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">{{ $usuariosActivos }}</p>
            @if ($usuariosInactivos > 0)
                <p class="text-xs" style="color: var(--color-muted);">{{ $usuariosInactivos }} inactivo(s)</p>
            @endif
        </div>
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Empleados</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">{{ $totalEmpleados }}</p>
        </div>
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Clientes</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">{{ $totalClientes }}</p>
        </div>
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Sucursales</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">{{ $sucursalesActivas }}</p>
        </div>
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Roles</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">{{ $totalRoles }}</p>
        </div>
        <div class="card px-4 py-3.5">
            <p class="text-xs font-medium" style="color: var(--color-muted);">Órdenes</p>
            <p class="mt-0.5 text-xl font-bold" style="color: var(--color-text);">—</p>
        </div>
    </div>

    {{-- SECCIÓN 2: Gráficos + Actividad --}}
    <div class="mt-5 grid gap-5 xl:grid-cols-3">
        {{-- Gráfico --}}
        <div class="card xl:col-span-2">
            <div class="card-header">Órdenes por estado</div>
            <div class="p-5">
                <canvas id="ordenesChart" height="180"></canvas>
            </div>
        </div>

        {{-- Actividad reciente --}}
        <div class="card">
            <div class="card-header">Actividad reciente</div>
            <div class="divide-y" style="border-color: var(--color-border);">
                @forelse ($actividadReciente as $log)
                    <div class="flex items-center gap-3 px-4 py-2.5 text-sm">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold" style="color: var(--color-muted); background-color: var(--color-border);">
                            {{ strtoupper(substr($log->usuario?->nombre ?? 'S', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="font-medium" style="color: var(--color-text);">{{ $log->usuario?->nombre ?? 'Sistema' }}</span>
                            <span style="color: var(--color-muted);">— {{ $log->accion }}</span>
                        </div>
                        <span class="shrink-0 text-xs" style="color: var(--color-muted);">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm" style="color: var(--color-muted);">Sin actividad registrada.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: Últimos accesos + Acciones rápidas --}}
    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">Últimos accesos</div>
            @if ($ultimosAccesos->isNotEmpty())
                <div class="divide-y px-4" style="border-color: var(--color-border);">
                    @foreach ($ultimosAccesos as $acceso)
                        <div class="flex items-center gap-3 py-2.5 text-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold" style="color: var(--color-muted); background-color: var(--color-border);">{{ strtoupper(substr($acceso->nombre, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium" style="color: var(--color-text);">{{ $acceso->nombre }}</p>
                                <p class="text-xs" style="color: var(--color-muted);">{{ $acceso->rol->nombre }}</p>
                            </div>
                            <span class="shrink-0 text-xs" style="color: var(--color-muted);">{{ $acceso->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center text-sm" style="color: var(--color-muted);">Sin accesos registrados.</div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">Acciones rápidas</div>
            <div class="p-3 space-y-1">
                <a href="{{ route('admin.clientes.create') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition" style="color: var(--color-text);" hover="background-color: var(--color-border)">
                    <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Registrar cliente
                </a>
                <a href="{{ route('admin.empleados.create') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition" style="color: var(--color-text);">
                    <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Registrar empleado
                </a>
                <a href="#" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition" style="color: var(--color-text);">
                    <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Nueva orden
                </a>
                <a href="#" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition" style="color: var(--color-text);">
                    <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Nueva cita
                </a>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: Últimos registros --}}
    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">Últimos clientes</div>
            @if ($ultimosRegistros['clientes']->isNotEmpty())
                <div class="divide-y px-4">
                    @foreach ($ultimosRegistros['clientes'] as $c)
                        <div class="flex items-center gap-3 py-2.5 text-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold" style="color: var(--color-muted); background-color: var(--color-border);">{{ strtoupper(substr($c->nombre, 0, 1)) }}</span>
                            <span class="font-medium" style="color: var(--color-text);">{{ $c->nombre }} {{ $c->apellido }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-6 text-center text-sm" style="color: var(--color-muted);">Sin clientes registrados.</div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">Últimos usuarios</div>
            @if ($ultimosRegistros['usuarios']->isNotEmpty())
                <div class="divide-y px-4">
                    @foreach ($ultimosRegistros['usuarios'] as $u)
                        <div class="flex items-center gap-3 py-2.5 text-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold" style="color: var(--color-muted); background-color: var(--color-border);">{{ strtoupper(substr($u->nombre, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <span class="font-medium" style="color: var(--color-text);">{{ $u->nombre }}</span>
                                <span class="text-xs" style="color: var(--color-muted);">· {{ $u->email }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-6 text-center text-sm" style="color: var(--color-muted);">Sin usuarios registrados.</div>
            @endif
        </div>
    </div>

    {{-- Chart.js script --}}
    <x-admin.vite-assets :entry="['resources/js/charts.js']" />
@endsection
