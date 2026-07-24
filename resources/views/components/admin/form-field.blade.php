@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'help' => null,
    'icon' => null,
    'autocomplete' => null,
])

@php
    $fieldValue = $value ?? old($name);
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $hasError = $errors->has($errorKey);
    $isTextarea = $type === 'textarea';
    $isSelect = $type === 'select';
@endphp

<div class="mb-3">
    @if ($label)
        <label for="field-{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required)<span class="required" aria-hidden="true">*</span>@endif
        </label>
    @endif

    @if ($isTextarea)
        <textarea id="field-{{ $name }}"
                  name="{{ $name }}"
                  rows="{{ $attributes->get('rows', 3) }}"
                  @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                  @if ($required) required aria-required="true" @endif
                  {{ $attributes->except(['rows'])->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}
        >{{ $fieldValue }}</textarea>
    @elseif ($isSelect)
        <select id="field-{{ $name }}"
                name="{{ $name }}"
                @if ($required) required aria-required="true" @endif
                {{ $attributes->merge(['class' => 'form-select' . ($hasError ? ' is-invalid' : '')]) }}>
            {{ $slot }}
        </select>
    @elseif ($icon)
        <div class="input-group">
            <span class="input-group-text bg-surface" style="border-right: 0; color: var(--tp-text-secondary);">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
            <input id="field-{{ $name }}"
                   type="{{ $type }}"
                   name="{{ $name }}"
                   value="{{ $fieldValue }}"
                   @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                   @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                   @if ($required) required aria-required="true" @endif
                   {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}>
        </div>
    @else
        <input id="field-{{ $name }}"
               type="{{ $type }}"
               name="{{ $name }}"
               value="{{ $fieldValue }}"
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($required) required aria-required="true" @endif
               {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}>
    @endif

    @if ($help && ! $hasError)
        <div class="form-text">{{ $help }}</div>
    @endif

    @if ($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($errorKey) }}</div>
    @endif
</div>
