@extends('layouts.admin')

@section('title', 'Panel de Recepción')

@section('page-title', 'Panel de Recepción')

@section('content')
    <section>
        <div class="rounded-2xl bg-gradient-to-r from-[#111827] to-[#1F2937] p-6 text-white shadow-lg sm:p-8">
            <p class="text-sm font-medium text-red-300">Panel de control</p>
            <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Bienvenido, {{ auth()->user()->nombre }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300 sm:text-base">
                Gestión de clientes, citas y ordenes de servicio.
            </p>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Citas del día</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Clientes registrados</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Vehículos en taller</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Órdenes pendientes</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/></svg>
                    </span>
                </div>
            </article>
        </div>
    </section>
@endsection