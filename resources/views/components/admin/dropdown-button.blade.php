@props(['danger' => false])

<button type="submit" role="menuitem" {{ $attributes->merge(['class' => 'flex w-full items-center gap-2 px-4 py-2 text-sm transition hover-surface ' . ($danger ? 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20' : '')]) }} style="color: {{ $danger ? '' : 'var(--color-text)' }};">
    {{ $slot }}
</button>
