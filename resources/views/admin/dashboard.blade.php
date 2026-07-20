@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard administrativo')

@section('content')
    <section>
        <div
            class="rounded-2xl bg-gradient-to-r from-[#111827] to-[#1F2937] p-6 text-white shadow-lg sm:p-8"
        >
            <p class="text-sm font-medium text-red-300">
                Panel de control
            </p>

            <h2 class="mt-2 text-2xl font-bold sm:text-3xl">
                Bienvenido, {{ auth()->user()->nombre }}
            </h2>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300 sm:text-base">
                Desde este panel podrás administrar usuarios, empleados,
                roles, permisos, sucursales y consultar la actividad del sistema.
            </p>
        </div>

        <div
            class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4"
        >
            @php
                $cards = [
                    [
                        'titulo' => 'Usuarios activos',
                        'valor' => $totalUsuarios,
                        'detalle' => 'Accesos habilitados',
                    ],
                    [
                        'titulo' => 'Empleados',
                        'valor' => $totalEmpleados,
                        'detalle' => 'Personal registrado',
                    ],
                    [
                        'titulo' => 'Sucursales',
                        'valor' => $totalSucursales,
                        'detalle' => 'Sucursales activas',
                    ],
                    [
                        'titulo' => 'Roles',
                        'valor' => $totalRoles,
                        'detalle' => 'Roles configurados',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <article
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ $card['titulo'] }}
                            </p>

                            <p class="mt-3 text-3xl font-bold text-gray-900">
                                {{ $card['valor'] }}
                            </p>

                            <p class="mt-2 text-xs text-gray-400">
                                {{ $card['detalle'] }}
                            </p>
                        </div>

                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-[#E31E24]"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"
                                />
                            </svg>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm xl:col-span-2"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            Actividad reciente
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Más adelante mostraremos datos reales de auditoría.
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 rounded-xl border border-dashed border-gray-300 bg-gray-50 py-12 text-center"
                >
                    <p class="font-medium text-gray-600">
                        Todavía no hay actividad registrada.
                    </p>

                    <p class="mt-1 text-sm text-gray-400">
                        Esta sección se completará en la tarea de auditoría.
                    </p>
                </div>
            </section>

            <section
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
            >
                <h3 class="text-lg font-bold text-gray-900">
                    Acciones rápidas
                </h3>

                <div class="mt-5 space-y-3">
                    @foreach ([
                        'Registrar empleado',
                        'Crear usuario',
                        'Gestionar roles',
                        'Nueva sucursal',
                    ] as $accion)
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-left text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                        >
                            {{ $accion }}

                            <span>→</span>
                        </button>
                    @endforeach
                </div>
            </section>
        </div>
    </section>
@endsection
