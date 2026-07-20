@extends('layouts.admin')

@section('title', 'Editar Rol')

@section('page-title', 'Editar Rol')

@section('content')
    <div class="max-w-4xl space-y-8">
        <form method="POST" action="{{ route('admin.roles.update', $rol) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">Datos del rol</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.form-input name="nombre" label="Nombre del rol" :value="$rol->nombre" :required="true" />
                    <x-admin.form-input name="descripcion" label="Descripción" :value="$rol->descripcion" />
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="estado" value="1" @checked($rol->estado) class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <span class="text-sm" style="color: var(--color-text);">Rol activo</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">Actualizar rol</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
            </div>
        </form>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold" style="color: var(--color-text);">Permisos del rol</h3>
                    <p class="text-sm mt-1" style="color: var(--color-muted);">Marca los permisos que tendrá el rol <strong>{{ $rol->nombre }}</strong></p>
                </div>
                <span class="text-sm" style="color: var(--color-muted);">Seleccionados: <strong id="permisos-count" class="text-brand-red">{{ $rol->permisos->count() }}</strong></span>
            </div>

            <form method="POST" action="{{ route('admin.roles.permisos', $rol) }}">
                @csrf
                @method('PUT')

                @php
                    $permisosPorModulo = $permisos->groupBy('modulo')->sortKeys();
                @endphp

                <div class="space-y-6">
                    @foreach ($permisosPorModulo as $modulo => $moduloPermisos)
                        <div class="rounded-xl border p-4" style="border-color: var(--color-border); background-color: var(--color-bg);">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold uppercase tracking-wider" style="color: var(--color-muted);">{{ $modulo }}</h4>
                                <label class="inline-flex items-center gap-2 text-xs cursor-pointer" style="color: var(--color-muted);">
                                    <input type="checkbox" class="modulo-toggle rounded border-gray-300 text-brand-red focus:ring-brand-red" data-modulo="{{ Str::slug($modulo) }}">
                                    <span>Seleccionar todo</span>
                                </label>
                            </div>
                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-modulo="{{ Str::slug($modulo) }}">
                                @foreach ($moduloPermisos as $permiso)
                                    <label class="inline-flex items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm transition cursor-pointer" style="border-color: var(--color-border); background-color: var(--color-surface);">
                                        <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                            @checked($rol->permisos->contains($permiso->id))
                                            class="permiso-checkbox rounded border-gray-300 text-brand-red focus:ring-brand-red"
                                            data-modulo="{{ Str::slug($modulo) }}">
                                        <div class="flex flex-col">
                                            <span class="font-medium" style="color: var(--color-text);">{{ $permiso->nombre }}</span>
                                            <span class="text-xs" style="color: var(--color-muted);">{{ $permiso->codigo }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
                        Guardar configuración de permisos
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.modulo-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const modulo = this.dataset.modulo;
                document.querySelectorAll(`.permiso-checkbox[data-modulo="${modulo}"]`).forEach(cb => {
                    cb.checked = this.checked;
                });
                actualizarContador();
            });
        });

        document.querySelectorAll('.permiso-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarContador);
        });

        function actualizarContador() {
            const count = document.querySelectorAll('.permiso-checkbox:checked').length;
            document.getElementById('permisos-count').textContent = count;
        }
    </script>
@endsection
