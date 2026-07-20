@extends('layouts.admin')

@section('title', 'Detalle de Cliente')

@section('page-title', 'Detalle de Cliente')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $cliente->nombre }} {{ $cliente->apellido }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $cliente->ci }}</p>
                </div>
                <x-admin.badge :type="$cliente->estado ? 'active' : 'inactive'">{{ $cliente->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Información de contacto</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Teléfono</dt><dd class="mt-1 text-sm text-gray-900">{{ $cliente->telefono ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</dt><dd class="mt-1 text-sm text-gray-900">{{ $cliente->email ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dirección</dt><dd class="mt-1 text-sm text-gray-900">{{ $cliente->direccion ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Información fiscal</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">NIT</dt><dd class="mt-1 text-sm text-gray-900">{{ $cliente->nit ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Razón social</dt><dd class="mt-1 text-sm text-gray-900">{{ $cliente->razon_social ?? '—' }}</dd></div>
            </dl>
        </div>

        @if ($cliente->observaciones)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Observaciones</h3>
                <p class="text-sm text-gray-600">{{ $cliente->observaciones }}</p>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.clientes.edit', $cliente) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar cliente</a>
            <form method="POST" action="{{ route('admin.clientes.estado', $cliente) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" onclick="return confirm('¿{{ $cliente->estado ? 'Desactivar' : 'Activar' }} a {{ $cliente->nombre }} {{ $cliente->apellido }}?')" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    {{ $cliente->estado ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <a href="{{ route('admin.clientes.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Volver</a>
        </div>
    </div>
@endsection
