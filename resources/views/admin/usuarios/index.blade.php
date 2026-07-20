@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')

@section('content')
    <x-admin.page-header
        title="Usuarios del sistema"
        subtitle="{{ $usuarios->total() }} registro(s)"
        :button="['label' => 'Nuevo usuario', 'url' => route('admin.usuarios.create')]"
    />

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--color-border);">
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Usuario</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Correo</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Rol</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Estado</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium" style="color: var(--color-muted);">Último acceso</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium" style="color: var(--color-muted);">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse ($usuarios as $usuario)
                        <tr class="transition hover-surface">
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold" style="color: var(--color-muted); background-color: var(--color-border);">
                                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-medium" style="color: var(--color-text);">{{ $usuario->nombre }}</p>
                                        <p class="text-xs" style="color: var(--color-muted);">{{ $usuario->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5" style="color: var(--color-muted);">{{ $usuario->email }}</td>
                            <td class="px-4 py-2.5"><x-admin.badge type="role">{{ $usuario->rol->nombre }}</x-admin.badge></td>
                            <td class="px-4 py-2.5">
                                <x-admin.badge :type="$usuario->estado ? 'active' : 'inactive'">{{ $usuario->estado ? 'Activo' : 'Inactivo' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-2.5 text-xs" style="color: var(--color-muted);">{{ $usuario->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <x-admin.dropdown>
                                    <x-admin.dropdown-link :href="route('admin.usuarios.show', $usuario)">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver detalle
                                    </x-admin.dropdown-link>
                                    <x-admin.dropdown-link :href="route('admin.usuarios.edit', $usuario)">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editar
                                    </x-admin.dropdown-link>
                                    <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}" onsubmit="return confirm('¿{{ $usuario->estado ? 'Desactivar' : 'Activar' }} a {{ $usuario->nombre }}?')">
                                        @csrf @method('PATCH')
                                        <x-admin.dropdown-button :danger="!$usuario->estado">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                                        </x-admin.dropdown-button>
                                    </form>
                                </x-admin.dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm" style="color: var(--color-muted);">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($usuarios->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--color-border);">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
@endsection
