@props(['label', 'name', 'options' => [], 'selected' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}"
        class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100">
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ $key == $selected ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div>
