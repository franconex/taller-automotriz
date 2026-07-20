<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col transition-transform duration-300 lg:translate-x-0"
    style="background-color: var(--color-sidebar);"
>
    <div class="flex flex-col items-center pt-6 pb-6 px-5 border-b" style="border-color: rgba(255,255,255,0.06);">
        <a href="{{ route('admin.dashboard') }}" class="block">
            <img src="/img/logo-modo-oscuro.png" alt="Taller Pro" class="w-[160px] max-w-full h-auto object-contain">
        </a>
        <button type="button" class="mt-4 rounded-lg p-1.5 text-gray-500 hover:bg-white/5 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Cerrar menú">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    @php $u = auth()->user(); @endphp
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1" aria-label="Menú lateral">
        <x-admin.nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="dashboard">Dashboard</x-admin.nav-link>

        <div class="pt-4 pb-1">
            <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500">Personal</p>
        </div>

        @can('empleados.ver')
            <x-admin.nav-link href="{{ route('admin.empleados.index') }}" :active="request()->routeIs('admin.empleados.*')" icon="employee">Empleados</x-admin.nav-link>
        @endcan
        @can('usuarios.ver')
            <x-admin.nav-link href="{{ route('admin.usuarios.index') }}" :active="request()->routeIs('admin.usuarios.*')" icon="users">Usuarios</x-admin.nav-link>
        @endcan

        <div class="pt-4 pb-1">
            <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500">Seguridad</p>
        </div>

        @can('roles.ver')
            <x-admin.nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')" icon="shield">Roles</x-admin.nav-link>
        @endcan
        @can('auditoria.ver')
            <x-admin.nav-link href="{{ route('admin.auditoria.index') }}" :active="request()->routeIs('admin.auditoria.*')" icon="document">Auditoría</x-admin.nav-link>
        @endcan

        <div class="pt-4 pb-1">
            <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500">Gestión</p>
        </div>

        @can('clientes.ver')
            <x-admin.nav-link href="{{ route('admin.clientes.index') }}" :active="request()->routeIs('admin.clientes.*')" icon="users">Clientes</x-admin.nav-link>
        @endcan
        @can('sucursales.ver')
            <x-admin.nav-link href="{{ route('admin.sucursales.index') }}" :active="request()->routeIs('admin.sucursales.*')" icon="building">Sucursales</x-admin.nav-link>
        @endcan
        @can('especialidades.ver')
            <x-admin.nav-link href="{{ route('admin.especialidades.index') }}" :active="request()->routeIs('admin.especialidades.*')" icon="star">Especialidades</x-admin.nav-link>
        @endcan
        @can('tipo_servicios.ver')
            <x-admin.nav-link href="{{ route('admin.tipo-servicios.index') }}" :active="request()->routeIs('admin.tipo-servicios.*')" icon="service">Tipos de Servicio</x-admin.nav-link>
        @endcan
        @can('metodos_pago.ver')
            <x-admin.nav-link href="{{ route('admin.metodos-pago.index') }}" :active="request()->routeIs('admin.metodos-pago.*')" icon="payment">Métodos de Pago</x-admin.nav-link>
        @endcan

        @can('reportes.ver')
            <div class="pt-4 pb-1">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500">Reportes</p>
            </div>
            <x-admin.nav-link href="#" icon="document">Reportes</x-admin.nav-link>
        @endcan
    </nav>

    <div class="border-t px-3 py-3" style="border-color: rgba(255,255,255,0.05);">
        <a href="{{ route('admin.perfil.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-400 transition hover:bg-white/5 hover:text-white">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-800 text-xs font-semibold text-gray-300">{{ strtoupper(substr($u->nombre, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-gray-200">{{ $u->nombre }}</p>
                <p class="truncate text-xs text-gray-500">{{ $u->rol->nombre }}</p>
            </div>
        </a>
    </div>
</aside>
