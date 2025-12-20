<x-layout>
    @php
    $b2 = rtrim(env('B2_BUCKET_URL'), '/');
    @endphp

    <div class="container flex justify-between">
        <nav class="breadcrumbs flex items-center pl-4 space-x-2 my-auto text-sm text-white">
            <a href="{{ route('home') }}" class="pr-2">Home</a> >
            <span class="text-gray-800">Items</span>
        </nav>

        <div class="flex justify-end space-x-4 pr-4">
            <form method="GET" action="{{ route('items.index') }}" class="flex">
                <input type="text" name="search" placeholder="Search items..."
                    value="{{ request('search') }}"
                    class="p-2 rounded-md border bg-[#565e55] text-white border-gray-300 w-full"
                    id="searchInput" />
            </form>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($items as $item)
            @php
            // Neem eerste image als thumbnail (of pas aan naar jouw "main image" logica)
            $img = $item->images?->first();
            $src = $img ? ($b2 . '/' . ltrim($img->image_path, '/')) : asset('images/error-image-not-found.png');
            @endphp

            <a href="{{ route('items.show', $item) }}" target="_blank"
                class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">

                <div class="mb-2">
                    <h3 class="text-lg font-bold text-center">
                        {{ $item->name ?? $item->title ?? ('Item #' . $item->id) }}
                    </h3>
                </div>

                <div class="flex-1 flex justify-center items-center h-64 bg-[#343933] rounded">
                    <img src="{{ $src }}"
                        alt="{{ $item->name ?? $item->title ?? ('Item #' . $item->id) }}"
                        class="w-full h-48 object-contain">
                </div>
            </a>
            @empty
            <div class="text-white">
                No items found.
            </div>
            @endforelse
        </div>

        <div class="text-white text-center mt-4">
            {{ $items->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        searchInput?.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') searchInput.form.submit();
        });
    </script>
</x-layout>