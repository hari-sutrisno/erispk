@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-theme-xs text-error-500']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
