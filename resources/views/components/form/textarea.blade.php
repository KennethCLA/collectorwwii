@props(['label', 'name', 'value' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
        class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100">{{ old($name, $value) }}</textarea>
</div>
