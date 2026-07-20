@extends('layouts.admin')

@section('title', 'Detalle de Sucursal')

@section('page-title', 'Detalle de Sucursal')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color: var(--color-text);">{{ $sucursale->nombre }}</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">{{ $sucursale->direccion }}</p>
                </div>
                <x-admin.badge :type="$sucursale->estado ? 'active' : 'inactive'">{{ $sucursale->estado ? 'Activa' : 'Inactiva' }}</x-admin.badge>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información de contacto</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Teléfono</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $sucursale->telefono ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Email</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $sucursale->email ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Horario</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $sucursale->horario_atencion ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Empleados</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $sucursale->empleados_count }}</dd></div>
            </dl>
        </div>

        @if ($sucursale->empleados->isNotEmpty())
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Empleados en esta sucursal</h3>
                <div class="space-y-2">
                    @foreach ($sucursale->empleados as $emp)
                        <div class="flex items-center justify-between rounded-lg px-4 py-2" style="background-color: var(--color-bg);">
                            <span class="text-sm font-medium" style="color: var(--color-text);">{{ $emp->nombre }} {{ $emp->apellido }}</span>
                            <span class="text-xs" style="color: var(--color-muted);">{{ $emp->cargo ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sucursales.edit', $sucursale) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar sucursal</a>
            <a href="{{ route('admin.sucursales.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Volver</a>
        </div>
    </div>
@endsection
