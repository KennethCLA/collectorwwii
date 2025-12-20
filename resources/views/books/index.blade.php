<!-- resources/views/books/index.blade.php -->
<x-layout :mainClass="'w-full px-2 sm:px-4 py-6'">
    <div class="w-full px-4 pt-6">
        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-center">
            {{-- Left column: breadcrumb --}}
            <nav class="breadcrumbs flex items-center pl-1 space-x-2 text-sm text-white">
                <a href="{{ route('home') }}" class="pr-2">Home</a> >
                <span class="text-gray-800">Books</span>
            </nav>

            {{-- Right column: sort + search --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                {{-- sort --}}
                <form method="GET" action="{{ route('books.index') }}" class="flex">
                    @if(request()->filled('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request()->filled('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                    @if(request()->filled('series')) <input type="hidden" name="series" value="{{ request('series') }}"> @endif
                    @if(request()->filled('cover')) <input type="hidden" name="cover" value="{{ request('cover') }}"> @endif

                    <select name="sort"
                        class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                        <option value="author_asc" {{ request('sort') == 'author_asc' ? 'selected' : '' }}>Author (A-Z)</option>
                        <option value="author_desc" {{ request('sort') == 'author_desc' ? 'selected' : '' }}>Author (Z-A)</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </form>

                {{-- search --}}
                <form method="GET" action="{{ route('books.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if(request()->filled('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request()->filled('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                    @if(request()->filled('series')) <input type="hidden" name="series" value="{{ request('series') }}"> @endif
                    @if(request()->filled('cover')) <input type="hidden" name="cover" value="{{ request('cover') }}"> @endif

                    <div class="relative w-full sm:w-[320px]">
                        <input type="text"
                            name="search"
                            placeholder="Search books..."
                            value="{{ request('search') }}"
                            class="p-2 pr-10 rounded-md border bg-[#565e55] text-white border-gray-300 w-full"
                            id="searchInput"
                            autocomplete="off" />

                        <button type="button"
                            id="clearSearchBtn"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-white/70 hover:text-white
                                   {{ request()->filled('search') ? '' : 'hidden' }}"
                            aria-label="Clear search"
                            title="Clear search">×</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="w-full px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-start">

            {{-- Sidebar --}}
            <aside class="lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)]">
                <div class="bg-[#697367] text-white p-4 rounded-md h-full overflow-y-auto">

                    {{-- TOPICS --}}
                    @php
                    $topicsLimit = 5;
                    $hasMoreTopics = $topics->count() > $topicsLimit;
                    $topicsOpenByDefault = request()->filled('topic'); // open als er een topic gekozen is
                    $activeTopicId = (int) request('topic');
                    @endphp

                    <h2 class="text-lg font-bold">Topics</h2>

                    <div x-data="{ topicsOpen: {{ $topicsOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            @foreach ($topics->take($topicsLimit) as $topic)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['topic' => $topic->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                          {{ $activeTopicId === (int)$topic->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $topic->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreTopics)
                        <ul x-show="topicsOpen" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($topics->skip($topicsLimit) as $topic)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['topic' => $topic->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                              {{ $activeTopicId === (int)$topic->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $topic->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="topicsOpen = !topicsOpen"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="topicsOpen ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- SERIES --}}
                    @php
                    $seriesLimit = 5;
                    $hasMoreSeries = $series->count() > $seriesLimit;
                    $seriesOpenByDefault = request()->filled('series'); // open als er een serie gekozen is
                    $activeSeriesId = (int) request('series');
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Series</h2>

                    <div x-data="{ seriesOpen: {{ $seriesOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            @foreach ($series->take($seriesLimit) as $serie)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['series' => $serie->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                          {{ $activeSeriesId === (int)$serie->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $serie->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreSeries)
                        <ul x-show="seriesOpen" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($series->skip($seriesLimit) as $serie)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['series' => $serie->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                              {{ $activeSeriesId === (int)$serie->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $serie->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="seriesOpen = !seriesOpen"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="seriesOpen ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- COVERS --}}
                    @php
                    $coversLimit = 5;
                    $hasMoreCovers = $covers->count() > $coversLimit;
                    $coversOpenByDefault = request()->filled('cover'); // open als er een cover gekozen is
                    $activeCoverId = (int) request('cover');
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Covers</h2>

                    <div x-data="{ coversOpen: {{ $coversOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            @foreach ($covers->take($coversLimit) as $cover)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['cover' => $cover->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                          {{ $activeCoverId === (int)$cover->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $cover->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreCovers)
                        <ul x-show="coversOpen" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($covers->skip($coversLimit) as $cover)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->query(), ['cover' => $cover->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                              {{ $activeCoverId === (int)$cover->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $cover->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="coversOpen = !coversOpen"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="coversOpen ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>
                </div>
            </aside>

            <!-- Books Grid -->
            <div class="min-w-0">
                {{-- Active filters bar --}}
                @php
                $q = request()->query();

                $hasFilters = request()->filled('search')
                || request()->filled('sort')
                || request()->filled('topic')
                || request()->filled('series')
                || request()->filled('cover');

                $remove = fn($key) => route('books.index', collect($q)->except([$key, 'page'])->all());
                $clearAll = route('books.index');

                // Map sort keys -> human labels
                $sortLabels = [
                'title_asc' => 'Title (A–Z)',
                'title_desc' => 'Title (Z–A)',
                'author_asc' => 'Author (A–Z)',
                'author_desc' => 'Author (Z–A)',
                'created_at_asc' => 'Newest first',
                'created_at_desc' => 'Oldest first',
                ];

                $sortLabel = $sortLabels[request('sort')] ?? request('sort');

                // Resolve ids -> names using collections you already have on the page
                $topicName = request()->filled('topic') ? optional($topics->firstWhere('id', (int) request('topic')))->name : null;
                $seriesName = request()->filled('series') ? optional($series->firstWhere('id', (int) request('series')))->name : null;
                $coverName = request()->filled('cover') ? optional($covers->firstWhere('id', (int) request('cover')))->name : null;
                @endphp

                @if($hasFilters)
                <div class="mb-4 flex flex-wrap items-center gap-2 bg-[#565e55] text-white rounded-md px-3 py-2">
                    <span class="text-sm opacity-90 mr-1">Active filters:</span>

                    @if(request()->filled('search'))
                    <a href="{{ $remove('search') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Search: “{{ request('search') }}”</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('sort'))
                    <a href="{{ $remove('sort') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Sort: {{ $sortLabel }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('topic'))
                    <a href="{{ $remove('topic') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Topic: {{ $topicName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('series'))
                    <a href="{{ $remove('series') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Series: {{ $seriesName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('cover'))
                    <a href="{{ $remove('cover') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Cover: {{ $coverName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    <a href="{{ $clearAll }}"
                        class="ml-auto text-sm underline text-white/90 hover:text-white">
                        Clear all
                    </a>
                </div>
                @endif

                @php
                $total = $books->total();
                $from = $books->firstItem();
                $to = $books->lastItem();

                $hasAnyFilter = request()->filled('search')
                || request()->filled('sort')
                || request()->filled('topic')
                || request()->filled('series')
                || request()->filled('cover');
                @endphp

                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm text-white/90">
                        @if($total > 0)
                        Showing <span class="font-semibold text-white">{{ $from }}</span>–<span class="font-semibold text-white">{{ $to }}</span>
                        of <span class="font-semibold text-white">{{ $total }}</span> books
                        @else
                        <span class="font-semibold text-white">0</span> books found
                        @endif
                    </p>

                    @if($hasAnyFilter)
                    <a href="{{ route('books.index') }}"
                        class="text-sm underline text-white/90 hover:text-white">
                        Clear all filters
                    </a>
                    @endif
                </div>

                @if($books->count() === 0)
                <div class="bg-[#697367] text-white rounded-md p-6">
                    <h3 class="text-lg font-bold">No results found</h3>
                    <p class="text-sm text-white/90 mt-2">
                        Try adjusting your search or removing some filters.
                    </p>

                    <div class="mt-4">
                        <a href="{{ route('books.index') }}"
                            class="bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm">
                            Clear filters
                        </a>
                    </div>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 h-full">
                    @foreach ($books as $book)
                    <a href="{{ route('books.show', $book) }}" target="_blank"
                        class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                        <div class="mb-1 flex-grow h-auto">
                            <h3 class="text-lg font-bold text-center">{{ $book->title }}</h3>
                            @if ($book->subtitle)
                            <h5 class="text-sm italic text-center text-gray-300">{{ $book->subtitle }}</h5>
                            @endif
                            @if ($book->issue_number)
                            <h5 class="text-xs italic text-center text-gray-300 pt-4">
                                {{ $book->issue_number }}
                            </h5>
                            @endif
                        </div>

                        <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-1 h-20">
                            @foreach ($book->authors as $author)
                            {{ $author->name }}@if (!$loop->last), @endif
                            @endforeach
                        </p>

                        <div class="flex-1 flex justify-center items-center h-80">
                            <img src="{{ $book->image_url }}" alt="{{ $book->title }}"
                                class="w-full h-48 object-contain">
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="text-white text-center mt-4">
                    {{ $books->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearchBtn');

        let debounceTimer = null;
        let lastValue = searchInput.value;

        function toggleClearButton() {
            if (!clearBtn) return;
            if (searchInput.value.trim().length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        function submitSearchForm() {
            // voorkom dubbele submits bij dezelfde waarde
            const current = searchInput.value;
            if (current === lastValue) return;
            lastValue = current;
            searchInput.form.submit();
        }

        // Type-to-search (debounce)
        searchInput.addEventListener('input', function() {
            toggleClearButton();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(submitSearchForm, 300);
        });

        // Enter: meteen submit
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(debounceTimer);
                submitSearchForm();
            }
        });

        // Clear button
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearButton();
                clearTimeout(debounceTimer);
                // lastValue updaten zodat submit zeker gebeurt
                lastValue = '__cleared__';
                searchInput.form.submit();
            });
        }

        // init
        toggleClearButton();
    </script>
</x-layout>