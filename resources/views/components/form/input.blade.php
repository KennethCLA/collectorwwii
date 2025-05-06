@props(['type' => 'text', 'name', 'id' => '', 'value' => '', 'required' => false])

<input type="{{ $type }}" id="{{ $id ?: $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]']) }} />
