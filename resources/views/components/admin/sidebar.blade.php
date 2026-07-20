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

    <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Menú lateral">
        <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Principal</p>

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                {{ request()->routeIs('admin.dashboard')
                    ? 'bg-brand-red text-white shadow-lg shadow-red-950/30'
                    : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
            aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12 12 3l9 9M5 10v10h5v-6h4v6h5V10"/></svg>
            Dashboard
        </a>

        <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Gestión</p>

        @php
            $menuItems = [
                ['texto' => 'Usuarios', 'icono' => 'users'],
                ['texto' => 'Empleados', 'icono' => 'employee'],
                ['texto' => 'Roles', 'icono' => 'shield'],
                ['texto' => 'Permisos', 'icono' => 'key'],
                ['texto' => 'Sucursales', 'icono' => 'building'],
                ['texto' => 'Auditoría', 'icono' => 'document'],
            ];
        @endphp

        @foreach ($menuItems as $item)
            <div class="group relative mb-1">
                <button type="button" disabled
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-medium text-gray-500 cursor-not-allowed"
                    aria-disabled="true">
                    <span class="flex h-5 w-5 items-center justify-center text-gray-600">
                        @switch($item['icono'])
                            @case('users')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1 2 2 4-4"/></svg>
                                @break
                            @case('employee')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-7 7a7 7 0 0 1 14 0"/></svg>
                                @break
                            @case('shield')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 4 6v5c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-3Z"/></svg>
                                @break
                            @case('key')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a4 4 0 1 1-7.5 2H3v4h3v3h3v-3l2.5-2.5A4 4 0 0 1 15 7Z"/></svg>
                                @break
                            @case('building')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21V5l8-2v18M4 9h8M4 13h8M4 17h8m4 4V9h4v12"/></svg>
                                @break
                            @default
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3h9l3 3v15H6V3Zm3 6h6M9 13h6M9 17h4"/></svg>
                        @endswitch
                    </span>
                    {{ $item['texto'] }}
                </button>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold uppercase tracking-wider text-gray-600 bg-gray-800/50 px-2 py-0.5 rounded-md">Próximamente</span>
            </div>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="rounded-xl bg-white/5 p-4">
            <p class="truncate text-sm font-semibold">{{ auth()->user()->nombre }}</p>
            <p class="mt-1 truncate text-xs text-gray-400">{{ auth()->user()->email }}</p>
            <p class="mt-3 inline-flex rounded-full bg-red-500/15 px-2.5 py-1 text-xs font-medium text-red-300">{{ auth()->user()->rol->nombre }}</p>
        </div>
    </div>
</aside>