@props(['type' => 'submit', 'color' => 'blue'])

<button type="{{ $type }}"
    class="px-4 py-2 rounded-md text-white bg-{{ $color }}-600 hover:bg-{{ $color }}-700">
    {{ $slot }}
</button>
