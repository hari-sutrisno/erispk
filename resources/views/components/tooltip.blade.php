@props([
    'content' => '',
    'position' => 'top', {{-- top, bottom, left, right --}}
])

<div class="relative inline-block"
     x-data="{ open: false }"
     @mouseenter="open = true"
     @mouseleave="open = false">

    {{-- Slot tombol --}}
    {{ $slot }}

    {{-- Tooltip --}}
    <div x-show="open" x-transition
         class="absolute px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg whitespace-nowrap z-50
            @if($position === 'top') bottom-full mb-2 left-1/2 -translate-x-1/2
            @elseif($position === 'bottom') top-full mt-2 left-1/2 -translate-x-1/2
            @elseif($position === 'left') right-full mr-2 top-1/2 -translate-y-1/2
            @elseif($position === 'right') left-full ml-2 top-1/2 -translate-y-1/2
            @endif">
        {{ $content }}
    </div>
</div>
