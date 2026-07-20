<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b px-4 sm:px-6" style="background-color: var(--color-surface); border-color: var(--color-border);">
    <div class="flex items-center gap-3">
        <button type="button" class="rounded-lg p-2 transition lg:hidden" style="color: var(--color-muted);" hover="background-color: var(--color-border)" @click="sidebarOpen = true" aria-label="Abrir menú">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div>
            <h1 class="text-base font-semibold" style="color: var(--color-text);">@yield('page-title', 'Dashboard')</h1>
        </div>
    </div>

    <div class="flex items-center gap-1 sm:gap-2">
        {{-- Tema --}}
        <button type="button" @click="cycleTheme" class="rounded-lg p-2 transition" :style="'color: var(--color-muted)'" x-data x-tooltip="'Tema: ' + (theme === 'system' ? 'Sistema' : theme === 'dark' ? 'Oscuro' : 'Claro')" aria-label="Cambiar tema">
            <template x-if="resolved === 'dark'">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </template>
            <template x-if="resolved !== 'dark'">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </template>
        </button>

        {{-- Notificaciones --}}
        <button type="button" class="relative rounded-lg p-2 transition" style="color: var(--color-muted);" aria-label="Notificaciones">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-red text-[10px] font-bold text-white">3</span>
        </button>

        {{-- Avatar + menú usuario --}}
        <div x-data="{ open: false }" @click.outside="open = false" class="relative ml-2">
            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-lg p-1.5 transition hover:bg-gray-100 dark:hover:bg-gray-800" aria-haspopup="true" :aria-expanded="open">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </span>
                <div class="hidden text-left sm:block">
                    <p class="text-sm font-medium" style="color: var(--color-text);">{{ auth()->user()->nombre }}</p>
                    <p class="text-xs" style="color: var(--color-muted);">{{ auth()->user()->rol->nombre }}</p>
                </div>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg border py-1 shadow-lg" style="background-color: var(--color-surface); border-color: var(--color-border);" role="menu" @click="open = false">
                <a href="{{ route('admin.perfil.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm transition" style="color: var(--color-text);" role="menuitem">
                    <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mi perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm transition" style="color: var(--color-text);" role="menuitem">
                        <svg class="h-4 w-4" style="color: var(--color-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
