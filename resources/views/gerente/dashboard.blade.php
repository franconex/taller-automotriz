@extends('layouts.admin')

@section('title', 'Panel de Gerencia')

@section('page-title', 'Panel de Gerencia')

@section('content')
    <section>
        <div class="rounded-2xl bg-gradient-to-r from-[#111827] to-[#1F2937] p-6 text-white shadow-lg sm:p-8">
            <p class="text-sm font-medium text-red-300">Panel de control</p>
            <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Bienvenido, {{ auth()->user()->nombre }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300 sm:text-base">
                Panel de gestión para el área de Gerencia. Reportes, estadísticas y supervisión general.
            </p>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reportes del mes</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ingresos</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Órdenes activas</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                    </span>
                </div>
            </article>
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Clientes atendidos</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">—</p>
                        <p class="mt-2 text-xs text-gray-400">Próximamente</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87m0-12a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                </div>
            </article>
        </div>
    </section>
@endsection