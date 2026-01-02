{{-- resources/views/items/index.blade.php --}}
<x-layout :mainClass="'w-full px-2 sm:px-4 py-6'">
    <div class="w-full px-4 pt-6">
        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-center">
            {{-- Left column: breadcrumb --}}
            <nav class="breadcrumbs flex items-center pl-1 space-x-2 text-sm text-white">
                <a href="{{ route('home') }}" class="pr-2">Home</a> >
                <span class="text-gray-800">Items</span>
            </nav>

            {{-- Right column: sort + search --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                {{-- sort --}}
                <form method="GET" action="{{ route('items.index') }}" class="flex">
                    @if(request()->filled('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request()->filled('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
                    @if(request()->filled('nationality_id')) <input type="hidden" name="nationality_id" value="{{ request('nationality_id') }}"> @endif
                    @if(request()->filled('origin_id')) <input type="hidden" name="origin_id" value="{{ request('origin_id') }}"> @endif
                    @if(request()->filled('organization_id')) <input type="hidden" name="organization_id" value="{{ request('organization_id') }}"> @endif
                    @if(request()->filled('for_sale')) <input type="hidden" name="for_sale" value="{{ request('for_sale') }}"> @endif

                    <select name="sort"
                        class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="" disabled selected>Sort by</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </form>

                {{-- search --}}
                <form method="GET" action="{{ route('items.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if(request()->filled('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request()->filled('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
                    @if(request()->filled('nationality_id')) <input type="hidden" name="nationality_id" value="{{ request('nationality_id') }}"> @endif
                    @if(request()->filled('origin_id')) <input type="hidden" name="origin_id" value="{{ request('origin_id') }}"> @endif
                    @if(request()->filled('organization_id')) <input type="hidden" name="organization_id" value="{{ request('organization_id') }}"> @endif
                    @if(request()->filled('for_sale')) <input type="hidden" name="for_sale" value="{{ request('for_sale') }}"> @endif

                    <div class="relative w-full sm:w-[320px]">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search items..."
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
                    @php
                    $hasAnyFilter =
                    request()->filled('search')
                    || request()->filled('sort')
                    || request()->filled('category_id')
                    || request()->filled('nationality_id')
                    || request()->filled('origin_id')
                    || request()->filled('organization_id')
                    || request()->filled('for_sale');
                    @endphp

                    @if($hasAnyFilter)
                    <a href="{{ route('items.index') }}"
                        class="block text-sm underline text-white/90 hover:text-white mb-4">
                        Clear all filters
                    </a>
                    @endif

                    {{-- CATEGORY --}}
                    @php
                    $categoriesLimit = 5;
                    $hasMoreCategories = $categories->count() > $categoriesLimit;
                    $categoriesOpenByDefault = request()->filled('category_id');
                    $activeCategoryId = (int) request('category_id');
                    @endphp

                    <h2 class="text-lg font-bold">Categories</h2>

                    <div x-data="{ open: {{ $categoriesOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            <li>
                                <a href="{{ route('items.index', collect(request()->query())->except(['category_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
       {{ !request()->filled('category_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    All
                                </a>
                            </li>
                            @foreach ($categories->take($categoriesLimit) as $c)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['category_id' => $c->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $activeCategoryId === (int)$c->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $c->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreCategories)
                        <ul x-show="open" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($categories->skip($categoriesLimit) as $c)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['category_id' => $c->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                            {{ $activeCategoryId === (int)$c->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $c->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="open = !open"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="open ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- NATIONALITIES --}}
                    @php
                    $nationalitiesLimit = 5;
                    $hasMoreNationalities = $nationalities->count() > $nationalitiesLimit;
                    $nationalitiesOpenByDefault = request()->filled('nationality_id');
                    $activeNationalityId = (int) request('nationality_id');
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Nationalities</h2>

                    <div x-data="{ open: {{ $nationalitiesOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            <li>
                                <a href="{{ route('items.index', collect(request()->query())->except(['nationality_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
       {{ !request()->filled('nationality_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    All
                                </a>
                            </li>
                            @foreach ($nationalities->take($nationalitiesLimit) as $n)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['nationality_id' => $n->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $activeNationalityId === (int)$n->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $n->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreNationalities)
                        <ul x-show="open" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($nationalities->skip($nationalitiesLimit) as $n)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['nationality_id' => $n->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                            {{ $activeNationalityId === (int)$n->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $n->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="open = !open"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="open ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- ORIGINS --}}
                    @php
                    $originsLimit = 5;
                    $hasMoreOrigins = $origins->count() > $originsLimit;
                    $originsOpenByDefault = request()->filled('origin_id');
                    $activeOriginId = (int) request('origin_id');
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Origins</h2>

                    <div x-data="{ open: {{ $originsOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            <li>
                                <a href="{{ route('items.index', collect(request()->query())->except(['origin_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
       {{ !request()->filled('origin_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    All
                                </a>
                            </li>
                            @foreach ($origins->take($originsLimit) as $o)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['origin_id' => $o->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $activeOriginId === (int)$o->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $o->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreOrigins)
                        <ul x-show="open" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($origins->skip($originsLimit) as $o)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['origin_id' => $o->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                            {{ $activeOriginId === (int)$o->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $o->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="open = !open"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="open ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- ORGANIZATIONS --}}
                    @php
                    $organizationsLimit = 5;
                    $hasMoreOrganizations = $organizations->count() > $organizationsLimit;
                    $organizationsOpenByDefault = request()->filled('organization_id');
                    $activeOrganizationId = (int) request('organization_id');
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Organizations</h2>

                    <div x-data="{ open: {{ $organizationsOpenByDefault ? 'true' : 'false' }} }" class="mt-2">
                        <ul class="text-sm space-y-1">
                            <li>
                                <a href="{{ route('items.index', collect(request()->query())->except(['organization_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
       {{ !request()->filled('organization_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    All
                                </a>
                            </li>
                            @foreach ($organizations->take($organizationsLimit) as $org)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['organization_id' => $org->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                        {{ $activeOrganizationId === (int)$org->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $org->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @if($hasMoreOrganizations)
                        <ul x-show="open" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($organizations->skip($organizationsLimit) as $org)
                            <li>
                                <a href="{{ route('items.index', array_merge(request()->query(), ['organization_id' => $org->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
                                            {{ $activeOrganizationId === (int)$org->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                                    {{ $org->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        <button type="button"
                            @click="open = !open"
                            class="mt-2 hover:underline text-blue-300 text-sm">
                            <span x-text="open ? 'Show Less' : 'Show More'"></span>
                        </button>
                        @endif
                    </div>


                    {{-- FOR SALE --}}
                    @php
                    $forSale = request('for_sale', null); // '1' | '0' | null
                    @endphp

                    <h2 class="text-lg font-bold mt-6">Status</h2>
                    <div class="mt-2 space-y-1 text-sm">
                        <a href="{{ route('items.index', collect(request()->query())->except(['for_sale','page'])->all()) }}"
                            class="block rounded px-2 py-1 hover:bg-white/10 hover:underline
   {{ !request()->filled('for_sale') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                            All
                        </a>
                        <a href="{{ route('items.index', array_merge(request()->query(), ['for_sale' => 1, 'page' => 1])) }}"
                            class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $forSale === '1' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                            For sale
                        </a>
                        <a href="{{ route('items.index', array_merge(request()->query(), ['for_sale' => 0, 'page' => 1])) }}"
                            class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $forSale === '0' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">
                            Not for sale
                        </a>
                        @if(request()->filled('for_sale'))
                        <a href="{{ route('items.index', collect(request()->query())->except(['for_sale','page'])->all()) }}"
                            class="block rounded px-2 py-1 hover:bg-white/10 hover:underline text-white/90">
                            Clear status
                        </a>
                        @endif
                    </div>

                </div>
            </aside>

            {{-- Items Grid --}}
            <div class="min-w-0">
                {{-- Active filters bar --}}
                @php
                $q = request()->query();

                $hasFilters =
                request()->filled('search')
                || request()->filled('sort')
                || request()->filled('category_id')
                || request()->filled('nationality_id')
                || request()->filled('origin_id')
                || request()->filled('organization_id')
                || request()->filled('for_sale');

                $remove = fn($key) => route('items.index', collect($q)->except([$key, 'page'])->all());
                $clearAll = route('items.index');

                $sortLabels = [
                'title_asc' => 'Title (A–Z)',
                'title_desc' => 'Title (Z–A)',
                'created_at_asc' => 'Newest first',
                'created_at_desc' => 'Oldest first',
                ];
                $sortLabel = $sortLabels[request('sort','created_at_asc')] ?? request('sort');

                $categoryName = request()->filled('category_id') ? optional($categories->firstWhere('id', (int) request('category_id')))->name : null;
                $nationalityName = request()->filled('nationality_id') ? optional($nationalities->firstWhere('id', (int) request('nationality_id')))->name : null;
                $originName = request()->filled('origin_id') ? optional($origins->firstWhere('id', (int) request('origin_id')))->name : null;
                $organizationName = request()->filled('organization_id') ? optional($organizations->firstWhere('id', (int) request('organization_id')))->name : null;
                $forSaleName = request()->filled('for_sale') ? (request('for_sale') === '1' ? 'For sale' : 'Not for sale') : null;
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

                    @if(request()->filled('category_id'))
                    <a href="{{ $remove('category_id') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Category: {{ $categoryName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('nationality_id'))
                    <a href="{{ $remove('nationality_id') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Nationality: {{ $nationalityName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('origin_id'))
                    <a href="{{ $remove('origin_id') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Origin: {{ $originName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('organization_id'))
                    <a href="{{ $remove('organization_id') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Organization: {{ $organizationName ?? 'Unknown' }}</span>
                        <span class="text-white/80">×</span>
                    </a>
                    @endif

                    @if(request()->filled('for_sale'))
                    <a href="{{ $remove('for_sale') }}"
                        class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Status: {{ $forSaleName ?? 'Unknown' }}</span>
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
                $total = $items->total();
                $from = $items->firstItem();
                $to = $items->lastItem();

                $hasAnyFilter = $hasFilters;
                @endphp

                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm text-white/90">
                        @if($total > 0)
                        Showing <span class="font-semibold text-white">{{ $from }}</span>–<span class="font-semibold text-white">{{ $to }}</span>
                        of <span class="font-semibold text-white">{{ $total }}</span> items
                        @else
                        <span class="font-semibold text-white">0</span> items found
                        @endif
                    </p>

                    @if($hasAnyFilter)
                    <a href="{{ route('items.index') }}"
                        class="text-sm underline text-white/90 hover:text-white">
                        Clear all filters
                    </a>
                    @endif
                </div>

                @if($items->count() === 0)
                <div class="bg-[#697367] text-white rounded-md p-6">
                    <h3 class="text-lg font-bold">No results found</h3>
                    <p class="text-sm text-white/90 mt-2">
                        Try adjusting your search or removing some filters.
                    </p>

                    <div class="mt-4">
                        <a href="{{ route('items.index') }}"
                            class="bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm">
                            Clear filters
                        </a>
                    </div>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 h-full">
                    @foreach ($items as $item)
                    <a href="{{ route('items.show', $item) }}" target="_blank"
                        class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">

                        {{-- Title block (zoals Books) --}}
                        <div class="mb-1 flex-grow h-auto">
                            <h3 class="text-lg font-bold text-center">{{ $item->title }}</h3>

                            @if($item->for_sale)
                            <h5 class="text-xs italic text-center text-gray-300 pt-4">
                                For sale
                            </h5>
                            @endif
                        </div>

                        {{-- Meta block (zelfde hoogte-idee als authors bij books) --}}
                        <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-1 h-20 flex flex-col justify-center">
                            <span class="block">
                                {{ $item->nationality?->name ?? '—' }}
                            </span>
                            <span class="block text-xs text-gray-300/90">
                                {{ $item->category?->name ?? '—' }}
                            </span>
                        </p>

                        {{-- Image block (exact zoals Books) --}}
                        <div class="flex-1 flex justify-center items-center h-80">
                            <img
                                src="{{ $item->image_url ?? asset('images/error-image-not-found.png') }}"
                                alt="{{ $item->title }}"
                                class="w-full h-48 object-contain">
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="text-white text-center mt-4">
                    {{ $items->links('pagination::tailwind') }}
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
                lastValue = '__cleared__';
                searchInput.form.submit();
            });
        }

        // init
        toggleClearButton();
    </script>
</x-layout>