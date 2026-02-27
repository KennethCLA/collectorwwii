{{-- resources/views/newspapers/index.blade.php --}}
<x-layout :mainClass="'w-full px-0 py-0'">
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-4 items-start">

            {{-- Sidebar --}}
            <aside class="hidden lg:block sticky self-start
                top-[var(--header-h,113px)]
                h-[calc(100vh_-_var(--header-h,113px))]
                bg-[#697367] border-r border-black/20 text-white">
                <div class="h-full overflow-y-auto px-4 pb-4">

                    @php $forSale = request('for_sale', null); @endphp

                    <div class="mt-0">
                        <div class="sticky top-0 z-10 -mx-4 px-4 py-2 bg-[#697367] border-b border-black/20 shadow-sm">
                            <h2 class="text-lg font-bold">Status</h2>
                        </div>
                        <ul class="mt-2 text-sm space-y-1">
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('newspapers.index', collect(request()->query())->except(['for_sale','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ !request()->filled('for_sale') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">All</a>
                            </li>
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('newspapers.index', array_merge(request()->query(), ['for_sale' => 1, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $forSale === '1' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">For sale</a>
                            </li>
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('newspapers.index', array_merge(request()->query(), ['for_sale' => 0, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $forSale === '0' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">Not for sale</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </aside>

            {{-- Main content --}}
            <div class="min-w-0 pr-4 pl-0">
                <div class="pt-2">
                    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-center">
                        <nav class="breadcrumbs flex items-center pl-1 space-x-2 text-sm text-white">
                            <a href="{{ route('home') }}" class="pr-2">Home</a> >
                            <span class="text-gray-800">Newspapers</span>
                        </nav>

                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <form method="GET" action="{{ route('newspapers.index') }}" class="flex">
                                @if(request()->filled('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                                @if(request()->filled('for_sale')) <input type="hidden" name="for_sale" value="{{ request('for_sale') }}"> @endif
                                <select name="sort" class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[150px]" onchange="this.form.submit()">
                                    <option value="" disabled selected>Sort by</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </form>

                            <form method="GET" action="{{ route('newspapers.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                                @if(request()->filled('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                                @if(request()->filled('for_sale')) <input type="hidden" name="for_sale" value="{{ request('for_sale') }}"> @endif
                                <div class="relative w-full sm:w-[320px]">
                                    <input type="text" name="search" placeholder="Search newspapers..."
                                        value="{{ request('search') }}"
                                        class="p-2 pr-10 rounded-md border bg-[#565e55] text-white border-gray-300 w-full"
                                        id="searchInput" autocomplete="off" />
                                    <button type="button" id="clearSearchBtn"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-white/70 hover:text-white
                                               {{ request()->filled('search') ? '' : 'hidden' }}"
                                        aria-label="Clear search">×</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @php
                $q = request()->query();
                $hasFilters = request()->filled('search') || request()->filled('sort') || request()->filled('for_sale');
                $remove = fn($key) => route('newspapers.index', collect($q)->except([$key, 'page'])->all());
                $clearAll = route('newspapers.index');
                $sortLabels = ['title_asc' => 'Title (A–Z)', 'title_desc' => 'Title (Z–A)', 'created_at_asc' => 'Newest first', 'created_at_desc' => 'Oldest first'];
                $sortLabel = $sortLabels[request('sort')] ?? request('sort');
                $forSaleName = request()->filled('for_sale') ? (request('for_sale') === '1' ? 'For sale' : 'Not for sale') : null;
                @endphp

                @if($hasFilters)
                <div class="mb-4 flex flex-wrap items-center gap-2 bg-[#565e55] text-white rounded-md px-3 py-2">
                    <span class="text-sm opacity-90 mr-1">Active filters:</span>
                    @if(request()->filled('search'))
                    <a href="{{ $remove('search') }}" class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Search: "{{ request('search') }}"</span><span class="text-white/80">×</span>
                    </a>
                    @endif
                    @if(request()->filled('sort'))
                    <a href="{{ $remove('sort') }}" class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Sort: {{ $sortLabel }}</span><span class="text-white/80">×</span>
                    </a>
                    @endif
                    @if(request()->filled('for_sale'))
                    <a href="{{ $remove('for_sale') }}" class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Status: {{ $forSaleName }}</span><span class="text-white/80">×</span>
                    </a>
                    @endif
                    <a href="{{ $clearAll }}" class="ml-auto text-sm underline text-white/90 hover:text-white">Clear all</a>
                </div>
                @endif

                @php $total = $newspapers->total(); $from = $newspapers->firstItem(); $to = $newspapers->lastItem(); @endphp
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm text-white/90">
                        @if($total > 0)
                        Showing <span class="font-semibold text-white">{{ $from }}</span>–<span class="font-semibold text-white">{{ $to }}</span>
                        of <span class="font-semibold text-white">{{ $total }}</span> newspapers
                        @else
                        <span class="font-semibold text-white">0</span> newspapers found
                        @endif
                    </p>
                </div>

                @if($newspapers->count() === 0)
                <div class="bg-[#697367] text-white rounded-md p-6">
                    <h3 class="text-lg font-bold">No results found</h3>
                    <p class="text-sm text-white/90 mt-2">Try adjusting your search or removing some filters.</p>
                    <div class="mt-4">
                        <a href="{{ route('newspapers.index') }}" class="bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm">Clear filters</a>
                    </div>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 h-full">
                    @foreach ($newspapers as $newspaper)
                    <a href="{{ route('newspapers.show', $newspaper) }}" target="_blank"
                        class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                        <div class="mb-1 flex-grow h-auto">
                            <h3 class="text-lg font-bold text-center">{{ $newspaper->title }}</h3>
                            @if($newspaper->for_sale)
                            <h5 class="text-xs italic text-center text-gray-300 pt-4">For sale</h5>
                            @endif
                        </div>
                        <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-1 h-20 flex flex-col justify-center">
                            <span class="block">{{ $newspaper->publisher ?? '—' }}</span>
                            @if($newspaper->publication_date)
                            <span class="block text-xs text-gray-300/90">{{ $newspaper->publication_date->format('d/m/Y') }}</span>
                            @endif
                        </p>
                        <div class="flex-1 flex justify-center items-center h-80">
                            <img src="{{ $newspaper->image_url ?? asset('images/error-image-not-found.png') }}"
                                alt="{{ $newspaper->title }}"
                                class="w-full h-48 object-contain">
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="text-white text-center mt-4">
                    {{ $newspapers->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        (() => {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearchBtn');
            if (!searchInput || !clearBtn) return;
            function toggleClearButton() {
                if (searchInput.value.trim().length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }
            searchInput.addEventListener('input', toggleClearButton);
            clearBtn.addEventListener('click', function() { searchInput.value = ''; toggleClearButton(); searchInput.form.submit(); });
            toggleClearButton();
        })();
    </script>
    <script>
        (() => {
            const nav = document.getElementById('main-navbar');
            if (!nav) return;
            const setHeaderH = () => { const h = nav.getBoundingClientRect().height || 0; document.documentElement.style.setProperty('--header-h', `${h}px`); };
            setHeaderH(); window.addEventListener('resize', setHeaderH);
            const btn = document.querySelector('[aria-controls="mobile-menu"]');
            if (btn) btn.addEventListener('click', () => setTimeout(setHeaderH, 50));
        })();
    </script>
</x-layout>
