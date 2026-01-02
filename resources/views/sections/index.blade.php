{{-- resources/views/sections/index.blade.php --}}

<x-layout>
    <h1 class="text-2xl font-semibold text-white">
        {{ $title }}
    </h1>

    <div class="mt-6 rounded-xl border border-black/20 bg-black/10 p-6">
        <p class="text-white font-medium">
            Coming soon
        </p>

        <p class="mt-2 text-white/80">
            The {{ strtolower($title) }} section is being prepared. Content will be added soon.
        </p>

        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ route('books.index') }}"
                class="inline-flex items-center rounded-md border border-black/20 bg-black/10 px-4 py-2 text-sm text-white hover:bg-black/15">
                Browse Books
            </a>

            <a href="{{ route('items.index') }}"
                class="inline-flex items-center rounded-md border border-black/20 bg-black/10 px-4 py-2 text-sm text-white hover:bg-black/15">
                Browse Items
            </a>
        </div>
    </div>
</x-layout>