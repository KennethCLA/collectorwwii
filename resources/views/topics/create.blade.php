<x-layout>
    <x-slot name="content">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row">
                <aside class="w-1/4">
                    <h2 class="text-lg font-bold">Topics</h2>
                    <ul class="text-sm mt-2">
                        @foreach ($topics as $topic)
                            <li><a href="{{ route('books.index', ['topic' => $topic->id]) }}"
                                    class="hover:underline">{{ e($topic->name) }}</a></li>
                        @endforeach
                    </ul>
                </aside>
                <main class="w-3/4">
                    <h2 class="text-lg font-bold">Create Topic</h2>
                    <form action="{{ route('topics.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="mt-1 p-2 w-full border rounded-md" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Create</button>
                    </form>
                </main>
            </div>
        </div>
    </x-slot>
</x-layout>
