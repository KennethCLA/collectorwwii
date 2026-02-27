{{-- resources/views/stamps/index.blade.php --}}
<x-layout :mainClass="'w-full px-0 py-0'">
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-4 items-start">

            <aside class="hidden lg:block sticky self-start
                top-[var(--header-h,113px)]
                h-[calc(100vh_-_var(--header-h,113px))]
                bg-[#697367] border-r border-black/20 text-white">
                <div class="h-full overflow-y-auto px-4 pb-4">

                    {{-- COUNTRIES --}}
                    @php
                    $countriesLimit = 5;
                    $hasMoreCountries = $countries->count() > $countriesLimit;
                    $countriesOpenByDefault = request()->filled('country_id');
                    $activeCountryId = (int) request('country_id');
                    $countriesMoreCount = max(0, $countries->count() - $countriesLimit);
                    @endphp

                    <div x-data="{ countriesOpen: {{ $countriesOpenByDefault ? 'true' : 'false' }} }" class="mt-0">
                        <div class="sticky top-0 z-10 -mx-4 px-4 py-2 bg-[#697367] border-b border-black/20 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold">Countries</h2>
                                @if($hasMoreCountries)
                                <button type="button" @click="countriesOpen = !countriesOpen" class="text-blue-300 text-sm hover:underline">
                                    <span x-text="countriesOpen ? 'Show Less' : 'Show More'"></span>
                                </button>
                                @endif
                            </div>
                            @if($hasMoreCountries)<div x-show="!countriesOpen" class="mt-1 text-[11px] text-white/60">+ {{ $countriesMoreCount }} more</div>@endif
                        </div>
                        <ul class="mt-2 text-sm space-y-1">
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', collect(request()->query())->except(['country_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ !request()->filled('country_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">All</a>
                            </li>
                            @foreach ($countries->take($countriesLimit) as $c)
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['country_id' => $c->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $activeCountryId === (int)$c->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">{{ $c->name }}</a>
                            </li>
                            @endforeach
                            @if($hasMoreCountries)<li x-show="!countriesOpen" class="px-2 py-1 text-white/50 select-none cursor-pointer hover:text-white/70" @click="countriesOpen = true">…</li>@endif
                        </ul>
                        @if($hasMoreCountries)
                        <ul x-show="countriesOpen" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($countries->skip($countriesLimit) as $c)
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['country_id' => $c->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $activeCountryId === (int)$c->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">{{ $c->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    {{-- STAMP TYPES --}}
                    @php
                    $typesLimit = 5;
                    $hasMoreTypes = $stampTypes->count() > $typesLimit;
                    $typesOpenByDefault = request()->filled('type_id');
                    $activeTypeId = (int) request('type_id');
                    $typesMoreCount = max(0, $stampTypes->count() - $typesLimit);
                    @endphp

                    <div x-data="{ typesOpen: {{ $typesOpenByDefault ? 'true' : 'false' }} }" class="mt-6">
                        <div class="sticky top-0 z-10 -mx-4 px-4 py-2 bg-[#697367] border-b border-black/20 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold">Types</h2>
                                @if($hasMoreTypes)
                                <button type="button" @click="typesOpen = !typesOpen" class="text-blue-300 text-sm hover:underline">
                                    <span x-text="typesOpen ? 'Show Less' : 'Show More'"></span>
                                </button>
                                @endif
                            </div>
                            @if($hasMoreTypes)<div x-show="!typesOpen" class="mt-1 text-[11px] text-white/60">+ {{ $typesMoreCount }} more</div>@endif
                        </div>
                        <ul class="mt-2 text-sm space-y-1">
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', collect(request()->query())->except(['type_id','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ !request()->filled('type_id') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">All</a>
                            </li>
                            @foreach ($stampTypes->take($typesLimit) as $t)
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['type_id' => $t->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $activeTypeId === (int)$t->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">{{ $t->name }}</a>
                            </li>
                            @endforeach
                            @if($hasMoreTypes)<li x-show="!typesOpen" class="px-2 py-1 text-white/50 select-none cursor-pointer hover:text-white/70" @click="typesOpen = true">…</li>@endif
                        </ul>
                        @if($hasMoreTypes)
                        <ul x-show="typesOpen" x-collapse class="text-sm space-y-1 mt-1">
                            @foreach ($stampTypes->skip($typesLimit) as $t)
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['type_id' => $t->id, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $activeTypeId === (int)$t->id ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">{{ $t->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    {{-- FOR SALE --}}
                    @php $forSale = request('for_sale', null); @endphp
                    <div class="mt-6">
                        <div class="sticky top-0 z-10 -mx-4 px-4 py-2 bg-[#697367] border-b border-black/20 shadow-sm">
                            <h2 class="text-lg font-bold">Status</h2>
                        </div>
                        <ul class="mt-2 text-sm space-y-1">
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', collect(request()->query())->except(['for_sale','page'])->all()) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ !request()->filled('for_sale') ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">All</a>
                            </li>
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['for_sale' => 1, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $forSale === '1' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">For sale</a>
                            </li>
                            <li class="relative after:block after:mx-3 after:border-b after:border-white/10">
                                <a href="{{ route('stamps.index', array_merge(request()->query(), ['for_sale' => 0, 'page' => 1])) }}"
                                    class="block rounded px-2 py-1 hover:bg-white/10 hover:underline {{ $forSale === '0' ? 'bg-white/15 font-semibold ring-1 ring-white/30' : '' }}">Not for sale</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </aside>

            <div class="min-w-0 pr-4 pl-0">
                <div class="pt-2">
                    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 items-center">
                        <nav class="breadcrumbs flex items-center pl-1 space-x-2 text-sm text-white">
                            <a href="{{ route('home') }}" class="pr-2">Home</a> >
                            <span class="text-gray-800">Stamps</span>
                        </nav>
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <form method="GET" action="{{ route('stamps.index') }}" class="flex">
                                @if(request()->filled('country_id')) <input type="hidden" name="country_id" value="{{ request('country_id') }}"> @endif
                                @if(request()->filled('type_id')) <input type="hidden" name="type_id" value="{{ request('type_id') }}"> @endif
                                @if(request()->filled('for_sale')) <input type="hidden" name="for_sale" value="{{ request('for_sale') }}"> @endif
                                <select name="sort" class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] min-w-[150px]" onchange="this.form.submit()">
                                    <option value="" disabled selected>Sort by</option>
                                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                @php
                $q = request()->query();
                $hasFilters = request()->filled('sort') || request()->filled('country_id') || request()->filled('type_id') || request()->filled('for_sale');
                $remove = fn($key) => route('stamps.index', collect($q)->except([$key, 'page'])->all());
                $clearAll = route('stamps.index');
                $countryName = request()->filled('country_id') ? optional($countries->firstWhere('id', (int) request('country_id')))->name : null;
                $typeName    = request()->filled('type_id')    ? optional($stampTypes->firstWhere('id',  (int) request('type_id')))->name    : null;
                $forSaleName = request()->filled('for_sale')   ? (request('for_sale') === '1' ? 'For sale' : 'Not for sale') : null;
                @endphp

                @if($hasFilters)
                <div class="mb-4 flex flex-wrap items-center gap-2 bg-[#565e55] text-white rounded-md px-3 py-2">
                    <span class="text-sm opacity-90 mr-1">Active filters:</span>
                    @if(request()->filled('country_id'))
                    <a href="{{ $remove('country_id') }}" class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Country: {{ $countryName ?? 'Unknown' }}</span><span class="text-white/80">×</span>
                    </a>
                    @endif
                    @if(request()->filled('type_id'))
                    <a href="{{ $remove('type_id') }}" class="inline-flex items-center gap-2 text-sm bg-[#697367] hover:bg-[#5a6452] rounded-full px-3 py-1">
                        <span>Type: {{ $typeName ?? 'Unknown' }}</span><span class="text-white/80">×</span>
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

                @php $total = $stamps->total(); $from = $stamps->firstItem(); $to = $stamps->lastItem(); @endphp
                <div class="mb-3"><p class="text-sm text-white/90">
                    @if($total > 0)
                    Showing <span class="font-semibold text-white">{{ $from }}</span>–<span class="font-semibold text-white">{{ $to }}</span>
                    of <span class="font-semibold text-white">{{ $total }}</span> stamps
                    @else
                    <span class="font-semibold text-white">0</span> stamps found
                    @endif
                </p></div>

                @if($stamps->count() === 0)
                <div class="bg-[#697367] text-white rounded-md p-6">
                    <h3 class="text-lg font-bold">No results found</h3>
                    <p class="text-sm text-white/90 mt-2">Try adjusting your filters.</p>
                    <div class="mt-4"><a href="{{ route('stamps.index') }}" class="bg-[#565e55] hover:bg-[#5a6452] text-white px-4 py-2 rounded-md text-sm">Clear filters</a></div>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 h-full">
                    @foreach ($stamps as $stamp)
                    <a href="{{ route('stamps.show', $stamp) }}" target="_blank"
                        class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                        <div class="mb-1 flex-grow h-auto">
                            <h3 class="text-lg font-bold text-center">{{ $stamp->card_title }}</h3>
                            @if($stamp->for_sale)<h5 class="text-xs italic text-center text-gray-300 pt-4">For sale</h5>@endif
                        </div>
                        <p class="text-sm text-center text-gray-300 border-t border-gray-400 py-1 h-20 flex flex-col justify-center">
                            <span class="block">{{ $stamp->country?->name ?? '—' }}</span>
                            <span class="block text-xs text-gray-300/90">{{ $stamp->stampType?->name ?? '—' }}</span>
                        </p>
                        <div class="flex-1 flex justify-center items-center h-80">
                            <img src="{{ $stamp->image_url ?? asset('images/error-image-not-found.png') }}"
                                alt="{{ $stamp->card_title }}" class="w-full h-48 object-contain">
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="text-white text-center mt-4">
                    {{ $stamps->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>
    </div>

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
