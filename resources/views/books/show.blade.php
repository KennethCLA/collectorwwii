<x-layout>
    <x-slot:content>
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs flex items-center mb-4 space-x-2 my-auto text-sm text-white">
                <a href="{{ route('home') }}" class="hover:underline">Home</a>
                <div>></div>
                <a href="{{ route('books.index') }}" class="hover:underline">Books</a>
                <div>></div>
                <span class="text-gray-800">{{ $book->title }}</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Linker Kolom (Afbeelding) -->
                <div class="flex-1 bg-[#697367] p-6 rounded-md shadow-md flex flex-col h-full">
                    <!-- Hoofdafbeelding (klikbaar en onderdeel van de gallery) -->
                    <div class="md:col-span-2 p-6 rounded-md">
                        <div class="p-2 rounded-lg">
                            @if ($book->images && $book->images->count())
                                <a href="{{ asset('books/' . $book->id . '/' . $book->mainImage->image_path) }}"
                                    data-fancybox="gallery">
                                    <img src="{{ asset('books/' . $book->id . '/' . $book->mainImage->image_path) }}"
                                        class="w-full object-contain rounded-lg max-h-96">
                                </a>
                            @else
                                <img src="{{ asset('images/error-image-not-found.png') }}" alt="{{ $book->title }}"
                                    class="w-full object-contain rounded-lg max-h-96">
                            @endif
                        </div>
                    </div>

                    <!-- Thumbnail Gallerij (inclusief hoofdafbeelding, zonder duplicaat in Fancybox) -->
                    <div class="flex justify-center space-x-2 mt-4 mx-auto">
                        @if ($book->images && $book->images->count())
                            <!-- Hoofdafbeelding als eerste thumbnail, maar zonder extra Fancybox-entry -->
                            <div
                                class="w-16 h-16 border rounded overflow-hidden flex items-center justify-center bg-[#343933]">
                                <img src="{{ asset('books/' . $book->id . '/' . $book->mainImage->image_path) }}"
                                    class="w-full h-full object-contain cursor-pointer hover:opacity-75"
                                    onclick="document.querySelector('[data-fancybox=gallery]').click();">
                            </div>

                            <!-- Overige afbeeldingen in de Fancybox -->
                            @foreach ($book->images as $image)
                                @if ($image->id !== $book->mainImage->id)
                                    <a href="{{ asset('books/' . $book->id . '/' . $image->image_path) }}"
                                        data-fancybox="gallery" class="bg-[#343933]">
                                        <div
                                            class="w-16 h-16 border rounded overflow-hidden flex items-center justify-center">
                                            <img src="{{ asset('books/' . $book->id . '/' . $image->image_path) }}"
                                                class="w-full h-full object-contain cursor-pointer hover:opacity-75">
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Rechterkant: Boekdetails -->
                <div class="w-full md:w-1/3 bg-[#697367] text-white p-8 rounded-lg shadow-lg flex flex-col h-full">
                    <h1 class="text-3xl text-center font-extrabold mb-4">{{ $book->title }}</h1>
                    <p class="text-lg text-center italic mb-6">{{ $book->subtitle }}</p>

                    <div class="space-y-4">
                        <hr class=" border-gray-600">
                        <p><span class="font-semibold text-lg text-gray-300">ISBN:</span> {{ $book->isbn }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Author(s):</span>
                            {{ $book->authors->pluck('name')->implode(', ') }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Translator:</span> {{ $book->translator }}
                        </p>
                        <p><span class="font-semibold text-lg text-gray-300">Publisher:</span>
                            {{ $book->publisher_name }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Topic:</span>
                            {{ $book->topic ? $book->topic->name : 'N/A' }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Year of Publication:</span>
                            {{ $book->copyright_year }}
                        </p>
                        <p><span class="font-semibold text-lg text-gray-300">Edition number:</span>
                            {{ $book->issue_number }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Edition Year:</span>
                            {{ $book->issue_year }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Series:</span>
                            {{ $book->series ? $book->series->name : 'N/A' }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Series number:</span>
                            {{ $book->series_number }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Cover Type:</span>
                            {{ $book->cover ? $book->cover->name : 'N/A' }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Pages:</span> {{ $book->pages }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Publisher first edition:</span>
                            {{ $book->publisher_first_issue }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Copyright year first edition:</span>
                            {{ $book->copyright_year_first_issue }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Title first edition:</span>
                            {{ $book->title_first_edition }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Subtitle first edition:</span>
                            {{ $book->subtitle_first_edition }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">Description:</span>
                            {{ $book->description }}</p>
                        <p><span class="font-semibold text-lg text-gray-300">For sale:</span>
                            {{ $book->for_sale ? 'Yes' : 'No' }}
                        </p>
                    </div>

                    <!-- Admin only fields -->
                    @if (auth()->check() && auth()->user()->isAdmin())
                        <hr class="my-6 border-gray-600">
                        <p class="text-2xl font-semibold text-gray-300 mb-4">Admin Details</p>
                        <div class="space-y-3 text-lg">
                            <p><span class="font-semibold text-gray-300">Book ID:</span> {{ $book->id }}</p>
                            <p><span class="font-semibold text-gray-300">Purchase Date:</span>
                                {{ $book->purchase_date ? \Carbon\Carbon::parse($book->purchase_date)->format('d-m-Y') : 'N/A' }}
                            </p>
                            <p><span class="font-semibold text-gray-300">Purchase Price:</span>
                                €{{ number_format($book->purchase_price, 2) }}</p>
                            <p><span class="font-semibold text-gray-300">Notes:</span> {{ $book->notes }}</p>
                            <p><span class="font-semibold text-gray-300">Storage location:</span>
                                {{ $book->storage_location }}</p>
                            <p><span class="font-semibold text-gray-300">Weight:</span> {{ $book->Weight }}</p>
                            <p><span class="font-semibold text-gray-300">Dimensions:</span> {{ $book->dimensions }}</p>
                        </div>

                        <!-- Admin buttons -->
                        <hr class="my-6 border-gray-600">
                        <div class="flex justify-between space-x-4">
                            <a href="{{ route('books.edit', $book->id) }}"
                                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out shadow-md">
                                Edit Book
                            </a>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition duration-300 ease-in-out shadow-md">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigatie knoppen (vorige en volgende boek) -->
        @if ($previousBook)
            <a href="{{ route('books.show', $previousBook->id) }}"
                class="fixed left-0 top-1/2 transform -translate-y-1/2 text-white bg-[#343933] hover:bg-gray-600 px-4 py-4 rounded-tr-xl rounded-br-xl">
                &#11164;
            </a>
        @endif

        @if ($nextBook)
            <a href="{{ route('books.show', $nextBook->id) }}"
                class="fixed right-0 top-1/2 transform -translate-y-1/2 text-white bg-[#343933] hover:bg-gray-600 px-4 py-4 rounded-tl-xl rounded-bl-xl">
                &#11166;
            </a>
        @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Fancybox.bind("[data-fancybox='gallery']", {
                    Toolbar: true,
                    zoom: true,
                    infinite: true,
                    wheel: "zoom"
                });
            });
        </script>
    </x-slot>
</x-layout>
