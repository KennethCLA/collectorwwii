<x-layout>
    <x-form-layout>
        <form action="{{ route('admin.books.update', $book) }}" method="POST" class="w-full mx-auto max-w-7xl">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @include('admin.books._fields', [
                'book' => $book,
                'bookData' => [],
                ])

                <div class="flex justify-end mt-4 gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.books.index') }}"
                        class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </div>
        </form>

        @include('admin.books._media', ['book' => $book])
    </x-form-layout>
</x-layout>