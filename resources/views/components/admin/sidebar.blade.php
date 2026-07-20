<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-[#111827] text-white transition-transform duration-300 lg:translate-x-0"
>
    <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Taller Pro" class="h-11 w-11 object-contain">
            <div>
                <p class="font-bold tracking-wide">TALLER PRO</p>
                <p class="text-xs text-gray-400">Panel administrativo</p>
            </div>
        </a>
        <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Cerrar menú">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    @php $user = auth()->user(); @endphp

    <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Menú lateral">
        <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Principal</p>

        <x-admin.nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="dashboard">
            Dashboard
        </x-admin.nav-link>

        <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Personal</p>

        @can('empleados.ver')
            <x-admin.nav-link href="{{ route('admin.empleados.index') }}" :active="request()->routeIs('admin.empleados.*')" icon="employee">
                Empleados
            </x-admin.nav-link>
        @endcan

        @can('usuarios.ver')
            <x-admin.nav-link href="{{ route('admin.usuarios.index') }}" :active="request()->routeIs('admin.usuarios.*')" icon="users">
                Usuarios
            </x-admin.nav-link>
        @endcan

        <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Gestión</p>

        @can('clientes.ver')
            <x-admin.nav-link href="{{ route('admin.clientes.index') }}" :active="request()->routeIs('admin.clientes.*')" icon="users">
                Clientes
            </x-admin.nav-link>
        @endcan

        @can('roles.ver')
            <x-admin.nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')" icon="shield">
                Roles y permisos
            </x-admin.nav-link>
        @endcan

        @can('sucursales.ver')
            <x-admin.nav-link href="{{ route('admin.sucursales.index') }}" :active="request()->routeIs('admin.sucursales.*')" icon="building">
                Sucursales
            </x-admin.nav-link>
        @endcan

        @can('especialidades.ver')
            <x-admin.nav-link href="{{ route('admin.especialidades.index') }}" :active="request()->routeIs('admin.especialidades.*')" icon="star">
                Especialidades
            </x-admin.nav-link>
        @endcan

        @can('tipo_servicios.ver')
            <x-admin.nav-link href="{{ route('admin.tipo-servicios.index') }}" :active="request()->routeIs('admin.tipo-servicios.*')" icon="service">
                Tipos de Servicio
            </x-admin.nav-link>
        @endcan

        @can('metodos_pago.ver')
            <x-admin.nav-link href="{{ route('admin.metodos-pago.index') }}" :active="request()->routeIs('admin.metodos-pago.*')" icon="payment">
                Métodos de Pago
            </x-admin.nav-link>
        @endcan

        <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Monitoreo</p>

        @can('auditoria.ver')
            <x-admin.nav-link href="{{ route('admin.auditoria.index') }}" :active="request()->routeIs('admin.auditoria.*')" icon="document">
                Auditoría
            </x-admin.nav-link>
        @endcan

        @can('reportes.ver')
            <x-admin.nav-link href="#" icon="document">
                Reportes
            </x-admin.nav-link>
        @endcan
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="rounded-xl bg-white/5 p-4">
            <p class="truncate text-sm font-semibold">{{ $user->nombre }}</p>
            <p class="mt-1 truncate text-xs text-gray-400">{{ $user->email }}</p>
            <p class="mt-3 inline-flex rounded-full bg-red-500/15 px-2.5 py-1 text-xs font-medium text-red-300">{{ $user->rol->nombre }}</p>
            <a href="{{ route('admin.perfil.edit') }}" class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-white/5 px-3 py-1.5 text-xs text-gray-300 transition hover:bg-white/10 hover:text-white">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zm-4 7a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg>
                Mi perfil
            </a>
        </div>
    </div>
</aside>
