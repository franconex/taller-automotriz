<header
    class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-8"
>
    <div class="flex items-center gap-4">
        <button
            type="button"
            class="rounded-xl border border-gray-200 p-2.5 text-gray-600 hover:bg-gray-100 lg:hidden"
            @click="sidebarOpen = true"
            aria-label="Abrir menú"
        >
            <svg
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>

        <div>
            <h1 class="text-lg font-bold text-gray-900 sm:text-xl">
                @yield('page-title', 'Dashboard')
            </h1>

            <p class="hidden text-sm text-gray-500 sm:block">
                Administración general del sistema
            </p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="hidden text-right sm:block">
            <p class="text-sm font-semibold text-gray-900">
                {{ auth()->user()->nombre }}
            </p>

            <p class="text-xs text-gray-500">
                {{ auth()->user()->rol->nombre }}
            </p>
        </div>

        <div
            class="flex h-11 w-11 items-center justify-center rounded-full bg-[#111827] font-bold text-white"
        >
            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
            >
                Salir
            </button>
        </form>
    </div>
</header>
