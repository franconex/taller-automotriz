@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard administrativo')

@section('content')
    <section>
        <div class="rounded-2xl bg-gradient-to-r from-[#111827] to-[#1F2937] p-6 text-white shadow-lg sm:p-8">
            <p class="text-sm font-medium text-red-300">Panel de control</p>
            <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Bienvenido, {{ auth()->user()->nombre }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300 sm:text-base">
                Desde este panel podrás administrar usuarios, empleados, roles, permisos, sucursales y consultar la actividad del sistema.
            </p>
        </div>

        @if ($alertas->isNotEmpty())
            @foreach ($alertas as $alerta)
                <x-admin.alert type="warning">{{ $alerta }}</x-admin.alert>
            @endforeach
        @endif

        <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Usuarios activos</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">{{ $usuariosActivos }}</p>
                        <p class="mt-2 text-xs text-gray-400">{{ $usuariosInactivos }} inactivo(s)</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1 2 2 4-4"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Empleados</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">{{ $totalEmpleados }}</p>
                        <p class="mt-2 text-xs text-gray-400">Personal registrado</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-7 7a7 7 0 0 1 14 0"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sucursales</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">{{ $sucursalesActivas }}</p>
                        <p class="mt-2 text-xs text-gray-400">Sucursales activas</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21V5l8-2v18M4 9h8M4 13h8M4 17h8m4 4V9h4v12"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Roles</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">{{ $totalRoles }}</p>
                        <p class="mt-2 text-xs text-gray-400">Roles configurados</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 4 6v5c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-3Z"/></svg>
                    </span>
                </div>
            </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm xl:col-span-2">
                <h3 class="text-lg font-bold text-gray-900">Actividad reciente</h3>
                @if ($actividadReciente->isNotEmpty())
                    <div class="mt-4 space-y-3">
                        @foreach ($actividadReciente as $log)
                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50/50 p-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-red/10 text-brand-red text-xs font-bold">
                                    {{ strtoupper(substr($log->usuario?->nombre ?? 'S', 0, 1)) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $log->usuario?->nombre ?? 'Sistema' }}
                                        <span class="font-normal text-gray-500">— {{ $log->accion }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $log->entidad_afectada }}
                                        @if ($log->detalle)
                                            · {{ $log->detalle }}
                                        @endif
                                    </p>
                                </div>
                                <time class="text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</time>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-gray-50 py-12 text-center">
                        <p class="font-medium text-gray-600">Todavía no hay actividad registrada.</p>
                        <p class="mt-1 text-sm text-gray-400">Los cambios en el sistema aparecerán aquí.</p>
                    </div>
                @endif
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">Últimos accesos</h3>
                @if ($ultimosAccesos->isNotEmpty())
                    <div class="mt-4 space-y-3">
                        @foreach ($ultimosAccesos as $acceso)
                            <div class="flex items-center gap-3 py-2">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-600 text-sm font-bold">
                                    {{ strtoupper(substr($acceso->nombre, 0, 1)) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $acceso->nombre }}</p>
                                    <p class="text-xs text-gray-400">{{ $acceso->rol->nombre }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $acceso->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-gray-50 py-12 text-center">
                        <p class="font-medium text-gray-600">Sin accesos registrados.</p>
                    </div>
                @endif

                <h3 class="mt-8 text-lg font-bold text-gray-900">Acciones rápidas</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.usuarios.create') }}" class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                        Crear usuario <span>→</span>
                    </a>
                    <a href="{{ route('admin.empleados.create') }}" class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                        Registrar empleado <span>→</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                        Gestionar roles <span>→</span>
                    </a>
                    <a href="{{ route('admin.sucursales.create') }}" class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                        Nueva sucursal <span>→</span>
                    </a>
                </div>
            </section>
        </div>
    </section>
@endsection
