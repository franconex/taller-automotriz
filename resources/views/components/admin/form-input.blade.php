@props(['name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => '', 'help' => ''])

<div>
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-1.5">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="3"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder }}"
            {{ $attributes->class(['w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-brand-red focus:ring-2 focus:ring-brand-red/10']) }}
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->class(['w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-brand-red focus:ring-2 focus:ring-brand-red/10']) }}
        >
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder }}"
            {{ $attributes->class(['w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-brand-red focus:ring-2 focus:ring-brand-red/10']) }}
        >
    @endif
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
    @if ($help)
        <p class="mt-1 text-xs text-gray-400">{{ $help }}</p>
    @endif
</div>
