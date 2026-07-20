@extends('layouts.admin')

@section('title', 'Detalle de Cliente')

@section('page-title', 'Detalle de Cliente')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color: var(--color-text);">{{ $cliente->nombre }} {{ $cliente->apellido }}</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">{{ $cliente->ci }}</p>
                </div>
                <x-admin.badge :type="$cliente->estado ? 'active' : 'inactive'">{{ $cliente->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información de contacto</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Teléfono</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $cliente->telefono ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Email</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $cliente->email ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Dirección</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $cliente->direccion ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Información fiscal</h3>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">NIT</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $cliente->nit ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-muted);">Razón social</dt><dd class="mt-1 text-sm" style="color: var(--color-text);">{{ $cliente->razon_social ?? '—' }}</dd></div>
            </dl>
        </div>

        @if ($cliente->observaciones)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-2" style="color: var(--color-text);">Observaciones</h3>
                <p class="text-sm" style="color: var(--color-muted);">{{ $cliente->observaciones }}</p>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.clientes.edit', $cliente) }}" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Editar cliente</a>
            <form method="POST" action="{{ route('admin.clientes.estado', $cliente) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" onclick="return confirm('¿{{ $cliente->estado ? 'Desactivar' : 'Activar' }} a {{ $cliente->nombre }} {{ $cliente->apellido }}?')" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                    {{ $cliente->estado ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <a href="{{ route('admin.clientes.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Volver</a>
        </div>
    </div>
@endsection
