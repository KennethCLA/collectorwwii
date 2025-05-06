<!-- resources/views/books/index.blade.php -->
<x-layout>
    <x-slot:content>
        <div class="container flex justify-between">
            <nav class="breadcrumbs flex items-center pl-4 space-x-2 my-auto text-sm text-white">
                <a href="{{ route('home') }}" class="pr-2">Home</a> >
                <span class="text-gray-800">Books</span>
            </nav>
            <div class="flex justify-end space-x-4 pr-4">
                <!-- Sorteerformulier zonder knop -->
                <form method="GET" action="{{ route('books.index') }}" class="flex">
                    <select name="sort"
                        class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] w-auto min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)
                        </option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title
                            (Z-A)</option>
                        <option value="author_asc" {{ request('sort') == 'author_asc' ? 'selected' : '' }}>Author
                            (A-Z)</option>
                        <option value="author_desc" {{ request('sort') == 'author_desc' ? 'selected' : '' }}>Author
                            (Z-A)</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                            Newest First</option>
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                            Oldest First</option>
                    </select>
                </form>

                <!-- Zoekformulier zonder knop -->
                <form method="GET" action="{{ route('books.index') }}" class="flex">
                    <input type="text" name="search" placeholder="Search books..." value="{{ request('search') }}"
                        class="p-2 rounded-md border bg-[#565e55] text-white border-gray-300 w-full" id="searchInput" />
                </form>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <div class="flex gap-4">
                <!-- aside met flex-shrink-0 voor automatische hoogte -->
                <aside class="flex-shrink-0 bg-[#697367] text-white p-4 rounded-md w-full md:w-1/4 h-full">
                    <h2 class="text-lg font-bold">Topics</h2>
                    <ul class="text-sm mt-2" x-data="{ showTopics: false }">
                        @foreach ($topics->take(5) as $topic)
                            <li><a href="{{ route('books.index', ['topic' => $topic->id]) }}"
                                    class="hover:underline">{{ $topic->name }}</a></li>
                        @endforeach
                        <div x-show="showTopics" x-collapse>
                            @foreach ($topics->skip(5) as $topic)
                                <li><a href="{{ route('books.index', ['topic' => $topic->id]) }}"
                                        class="hover:underline">{{ $topic->name }}</a></li>
                            @endforeach
                        </div>
                        <li>
                            <button @click="showTopics = !showTopics" class="hover:underline text-blue-400">
                                <span x-show="!showTopics">Show More</span>
                                <span x-show="showTopics">Show Less</span>
                            </button>
                        </li>
                    </ul>

                    <h2 class="text-lg font-bold mt-4">Series</h2>
                    <ul class="text-sm mt-2" x-data="{ showSeries: false }">
                        @foreach ($series->take(5) as $serie)
                            <li><a href="{{ route('books.index', ['series' => $serie->id]) }}"
                                    class="hover:underline">{{ $serie->name }}</a></li>
                        @endforeach
                        <div x-show="showSeries">
                            @foreach ($series->skip(5) as $serie)
                                <li><a href="{{ route('books.index', ['series' => $serie->id]) }}"
                                        class="hover:underline">{{ $serie->name }}</a></li>
                            @endforeach
                        </div>
                        <li>
                            <button @click="showSeries = !showSeries" class="hover:underline text-blue-400">
                                <span x-show="!showSeries">Show More</span>
                                <span x-show="showSeries">Show Less</span>
                            </button>
                        </li>
                    </ul>

                    <h2 class="text-lg font-bold mt-4">Covers</h2>
                    <ul class="text-sm mt-2" x-data="{ showCovers: false }">
                        @foreach ($covers->take(5) as $cover)
                            <li><a href="{{ route('books.index', ['cover' => $cover->id]) }}"
                                    class="hover:underline">{{ $cover->name }}</a></li>
                        @endforeach
                        <div x-show="showCovers">
                            @foreach ($covers->skip(5) as $cover)
                                <li><a href="{{ route('books.index', ['cover' => $cover->id]) }}"
                                        class="hover:underline">{{ $cover->name }}</a></li>
                            @endforeach
                        </div>
                        <li>
                            <button @click="showCovers = !showCovers" class="hover:underline text-blue-400">
                                <span x-show="!showCovers">Show More</span>
                                <span x-show="showCovers">Show Less</span>
                            </button>
                        </li>
                    </ul>
                </aside>

                <!-- Books Grid -->
                <div class="w-full md:w-3/4 flex-grow">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 h-full">
                        @foreach ($books as $book)
                            <a href="{{ route('books.show', $book) }}" target="_blank"
                                class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                                <!-- Title -->
                                <div class="mb-1 flex-grow h-auto">
                                    <h3 class="text-lg font-bold text-center">{{ $book->title }}</h3>
                                    @if ($book->subtitle)
                                        <h5 class="text-sm italic text-center text-gray-300">{{ $book->subtitle }}</h5>
                                    @endif
                                    @if ($book->issue_number)
                                        <h5 class="text-xs italic text-center text-gray-300 pt-4">
                                            {{ $book->issue_number }}</h5>
                                    @endif
                                </div>
                                <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-1 h-20">
                                    @foreach ($book->authors as $author)
                                        {{ $author->name }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </p>

                                <!-- Photo -->
                                <div class="flex-1 flex justify-center items-center h-80">
                                    <img src="{{ $book->mainImage ? asset('books/' . $book->id . '/' . $book->mainImage->image_path) : asset('images/error-image-not-found.png') }}"
                                        alt="{{ $book->title }}" class="w-full h-48 object-contain">
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="text-white text-center col-span-5 mt-4">
                        {{ $books->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>

        <script>
            const searchInput = document.getElementById('searchInput');

            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    searchInput.form.submit(); // Alleen indienen bij Enter-toets
                }
            });
        </script>
    </x-slot:content>
</x-layout>
