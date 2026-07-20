@php
    $action ??= '';
    $method ??= 'POST';
    $item ??= null;
    $fields ??= ['nombre', 'descripcion'];
    $labels ??= ['Nombre', 'Descripción'];
    $placeholders ??= ['', ''];
    $badge_field ??= 'estado';
    $badge_label ??= 'Activo';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="card p-6">
        <h3 class="text-lg font-bold mb-4" style="color: var(--color-text);">{{ $item ? 'Editar' : 'Nuevo' }} registro</h3>

        @foreach ($fields as $i => $field)
            <div class="{{ $i > 0 ? 'mt-5' : '' }}">
                <x-admin.form-input
                    name="{{ $field }}"
                    label="{{ $labels[$i] ?? $field }}"
                    :value="$item?->{$field}"
                    :required="$i === 0"
                    placeholder="{{ $placeholders[$i] ?? '' }}"
                />
            </div>
        @endforeach

        @if ($badge_field)
            <div class="mt-5">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="{{ $badge_field }}" value="1" @checked($item ? $item->{$badge_field} : true)
                        class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                    <span class="text-sm" style="color: var(--color-text);">{{ $badge_label }}</span>
                </label>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-xl bg-brand-red px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-red-dark">
            {{ $item ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ url()->previous() }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold transition hover-surface" style="color: var(--color-text);">Cancelar</a>
    </div>
</form>
