@extends('layouts.admin')

@section('title', 'Detalle de Sucursal')

@section('page-title', 'Detalle de Sucursal')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $sucursale->nombre }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $sucursale->direccion }}</p>
                </div>
                <x-admin.badge :type="$sucursale->estado ? 'active' : 'inactive'">{{ $sucursale->estado ? 'Activa' : 'Inactiva' }}</x-admin.badge>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Información de contacto</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Teléfono</dt><dd class="mt-1 text-sm text-gray-900">{{ $sucursale->telefono ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</dt><dd class="mt-1 text-sm text-gray-900">{{ $sucursale->email ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Horario</dt><dd class="mt-1 text-sm text-gray-900">{{ $sucursale->horario_atencion ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Empleados</dt><dd class="mt-1 text-sm text-gray-900">{{ $sucursale->empleados_count }}</dd></div>
            </dl>
        </div>

        @if ($sucursale->empleados->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Empleados en esta sucursal</h3>
                <div class="space-y-2">
                    @foreach ($sucursale->empleados as $emp)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50/50 px-4 py-2">
                            <span class="text-sm font-medium text-gray-900">{{ $emp->nombre }} {{ $emp->apellido }}</span>
                            <span class="text-xs text-gray-500">{{ $emp->cargo ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sucursales.edit', $sucursale) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar sucursal</a>
            <a href="{{ route('admin.sucursales.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Volver</a>
        </div>
    </div>
@endsection
