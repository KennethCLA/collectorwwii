@props(['href', 'color' => 'gray'])

<a href="{{ $href }}"
    class="px-4 py-2 rounded-md text-white bg-{{ $color }}-600 hover:bg-{{ $color }}-700">
    {{ $slot }}
</a>
