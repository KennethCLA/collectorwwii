{{-- resources/views/for-sale/index.blade.php --}}
<x-layout :mainClass="'w-full px-2 sm:px-4 py-6'">
    <div class="w-full px-4 pt-6">
        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-center">
            {{-- Left column: breadcrumb --}}
            <nav class="breadcrumbs flex items-center pl-1 space-x-2 text-sm text-white">
                <a href="{{ route('home') }}" class="pr-2">Home</a> >
                <span class="text-gray-800">For Sale</span>
            </nav>

            {{-- Right column: type + sort + search --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                {{-- type filter --}}
                <form method="GET" action="{{ route('for-sale.index') }}" class="flex">
                    @if(request()->filled('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request()->filled('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <select name="type"
                        class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="all" {{ request('type','all') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="books" {{ request('type') == 'books' ? 'selected' : '' }}>Books</option>
                        <option value="items" {{ request('type') == 'items' ? 'selected' : '' }}>Items</option>
                    </select>
                </form>

                {{-- sort --}}
                <form method="GET" action="{{ route('for-sale.index') }}" class="flex">
                    @if(request()->filled('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request()->filled('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif

                    <select name="sort"
                        class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[170px]"
                        onchange="this.form.submit()">
                        <option value="" disabled {{ request()->filled('sort') ? '' : 'selected' }}>Sort by</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low → High)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High → Low)</option>
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </form>

                {{-- search --}}
                <form method="GET" action="{{ route('for-sale.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if(request()->filled('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request()->filled('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif

                    <div class="relative w-full sm:w-[320px]">
                        <input type="text"
                            name="q"
                            placeholder="Search for sale..."
                            value="{{ request('q') }}"
                            class="p-2 pr-10 rounded-md border bg-[#565e55] text-white border-gray-300 w-full"
                            id="searchInput"
                            autocomplete="off" />

                        <button type="button"
                            id="clearSearchBtn"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-white/70 hover:text-white
                                   {{ request()->filled('q') ? '' : 'hidden' }}"
                            aria-label="Clear search"
                            title="Clear search">×</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="w-full px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-start">

            {{-- Sidebar (optioneel: later uitbreiden met categorieën/filters) --}}
            <aside class="lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)]">
                <div class="bg-[#697367] text-white p-4 rounded-md h-full overflow-y-auto">
                    <h2 class="text-lg font-bold">For Sale</h2>

                    <p class="text-sm text-white/90 mt-2">
                        Browse all books and items currently marked as <span class="font-semibold">for sale</span>.
                    </p>
                    {{--
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between rounded bg-white/10 px-3 py-2">
                            <span>Type</span>
                            <span class="font-semibold">
                                {{ ucfirst(request('type','all')) }}
                    </span>
                </div>

                @if(request()->filled('q'))
                <div class="rounded bg-white/10 px-3 py-2">
                    <div class="text-white/80 text-xs">Search</div>
                    <div class="font-semibold break-words">“{{ request('q') }}”</div>
                </div>
                @endif

                @if(request()->filled('sort'))
                <div class="rounded bg-white/10 px-3 py-2">
                    <div class="text-white/80 text-xs">Sort</div>
                    <div class="font-semibold">{{ request('sort') }}</div>
                </div>
                @endif

                <a href="{{ route('for-sale.index') }}"
                    class="inline-flex w-full items-center justify-center bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm mt-2">
                    Clear all
                </a>
        </div>
        --}}
    </div>
    </aside>

    {{-- Grid --}}
    <div class="min-w-0">
        @php
        $total = $forSale->total();
        $from = $forSale->firstItem();
        $to = $forSale->lastItem();

        $hasAnyFilter = request()->filled('q')
        || request()->filled('sort')
        || request()->filled('type');
        @endphp

        <div class="mb-3 flex items-center justify-between">
            <p class="text-sm text-white/90">
                @if($total > 0)
                Showing <span class="font-semibold text-white">{{ $from }}</span>–<span class="font-semibold text-white">{{ $to }}</span>
                of <span class="font-semibold text-white">{{ $total }}</span> results
                @else
                <span class="font-semibold text-white">0</span> results found
                @endif
            </p>

            @if($hasAnyFilter)
            <a href="{{ route('for-sale.index') }}"
                class="text-sm underline text-white/90 hover:text-white">
                Clear all filters
            </a>
            @endif
        </div>

        @if($forSale->count() === 0)
        <div class="bg-[#697367] text-white rounded-md p-6">
            <h3 class="text-lg font-bold">No results found</h3>
            <p class="text-sm text-white/90 mt-2">
                Try adjusting your search or removing filters.
            </p>

            <div class="mt-4">
                <a href="{{ route('for-sale.index') }}"
                    class="bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm">
                    Clear filters
                </a>
            </div>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 h-full">
            @foreach ($forSale as $row)
            <a href="{{ $row['url'] }}" target="_blank"
                class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                <div class="mb-2 flex items-start justify-between gap-2">
                    <span class="text-xs bg-white/15 rounded-full px-3 py-1">
                        {{ $row['type'] === 'book' ? 'Book' : 'Item' }}
                    </span>

                    @if(!is_null($row['price']))
                    <span class="text-xs font-semibold bg-black/20 rounded-full px-3 py-1">
                        €{{ number_format((float)$row['price'], 2, ',', '.') }}
                    </span>
                    @endif
                </div>

                <div class="mb-2 flex-grow h-auto">
                    <h3 class="text-base font-bold text-center line-clamp-2">{{ $row['title'] }}</h3>

                    @if(($row['type'] ?? null) === 'book' && !empty($row['subtitle']))
                    <h5 class="text-xs italic text-center text-gray-300 mt-1 line-clamp-2">
                        {{ $row['subtitle'] }}
                    </h5>
                    @endif
                    @if(($row['type'] ?? null) === 'book')
                    <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-2 mt-2 min-h-[56px]">
                        @php $authors = $row['authors'] ?? []; @endphp

                        @if(count($authors))
                        {{ implode(', ', $authors) }}
                        @else
                        <span class="italic text-white/60">Unknown author</span>
                        @endif
                    </p>
                    @endif
                </div>

                <div class="flex-1 flex justify-center items-center h-80">
                    <img
                        src="{{ $row['image'] ?? asset('images/error-image-not-found.png') }}"
                        alt="{{ $row['title'] }}"
                        class="w-full h-48 object-contain">
                </div>
            </a>
            @endforeach
        </div>

        <div class="text-white text-center mt-4">
            {{ $forSale->appends(request()->query())->links('pagination::tailwind') }}
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
            const current = searchInput.value;
            if (current === lastValue) return;
            lastValue = current;
            searchInput.form.submit();
        }

        searchInput.addEventListener('input', function() {
            toggleClearButton();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(submitSearchForm, 300);
        });

        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(debounceTimer);
                submitSearchForm();
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearButton();
                clearTimeout(debounceTimer);
                lastValue = '__cleared__';
                searchInput.form.submit();
            });
        }

        toggleClearButton();
    </script>
</x-layout>