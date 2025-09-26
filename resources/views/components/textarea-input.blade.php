@props([
    'disabled' => false,
    'hasError' => false,
    'rows' => 4,
])

<textarea @disabled($disabled) rows="{{ $rows }}"
    {{ $attributes->class([
        'w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
        'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800' => !$hasError,
        'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800' => $hasError,
    ]) }}>{{ $slot }}</textarea>
