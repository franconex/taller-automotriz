@props(['name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => '', 'help' => ''])

@php
$error = $errors->has($name) ? 'border-red-300 bg-red-50 text-red-900 dark:bg-red-900/20 dark:text-red-300' : '';
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium" style="color: var(--color-text);">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => "input-field {$error}"]) }}>{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => "input-field {$error}"]) }}>
            {{ $slot }}
        </select>
    @else
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => "input-field {$error}"]) }}>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @if ($help)
        <p class="mt-1 text-xs" style="color: var(--color-muted);">{{ $help }}</p>
    @endif
</div>
