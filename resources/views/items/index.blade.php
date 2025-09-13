<x-layout>
    <x-slot:content>
        <div class="container">
            <div class="container flex justify-between">
                <div class="breadcrumbs flex items-center pl-6 md:pl-12 lg:pl-24 xl:pl-48 space-x-2 my-auto">
                    <a href="{{ route('home') }}" class="pr-2">Home</a> /
                    <a href="{{ route('items.index') }}">Items</a>
                </div>
                <div class="flex justify-end space-x-4 pr-4">
                    <!-- Sorteerformulier zonder knop -->
                    <form method="GET" action="{{ route('items.index') }}" class="flex">
                        <select name="sort"
                            class="p-2 rounded-md border text-white border-gray-300 bg-[#565e55] w-auto min-w-[150px]"
                            onchange="this.form.submit()">
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)
                            </option>
                            <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title
                                (Z-A)</option>
                            <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                Newest First</option>
                            <option value="created_at_desc"
                                {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                                Oldest First</option>
                        </select>
                    </form>

                    <!-- Zoekformulier zonder knop -->
                    <form method="GET" action="{{ route('items.index') }}" class="flex">
                        <input type="text" name="search" placeholder="Search items..."
                            value="{{ request('search') }}"
                            class="p-2 rounded-md border bg-[#565e55] text-white border-gray-300 w-full"
                            id="searchInput" />
                    </form>
                </div>
            </div>

            <div class="w-full py-6">
                <div class="flex gap-6">
                    <!-- aside met flex-shrink-0 voor automatische hoogte -->
                    <aside class="sticky top-24 left-0 w-64 shrink-0 bg-[#4f574d] text-white p-4 pt-6
         md:max-h-[calc(100vh-6rem)] md:overflow-y-auto">
                        <h2 class="text-lg font-bold">Categories</h2>
                        <ul class="text-sm mt-2" x-data="{ showCategories: false }">
                            @foreach ($categories->take(5) as $category)
                            <li><a href="{{ route('items.index', ['category' => $category->id]) }}"
                                    class="hover:underline">{{ e($category->name) }}</a></li>
                            @endforeach

                            <div x-show="showCategories" x-collapse>
                                @foreach ($categories->skip(5) as $category)
                                <li><a href="{{ route('items.index', ['category' => $category->id]) }}"
                                        class="hover:underline">{{ e($category->name) }}</a></li>
                                @endforeach
                            </div>

                            <li>
                                <button @click="showCategories = !showCategories" class="hover:underline text-blue-400">
                                    <span x-show="!showCategories">Show More</span>
                                    <span x-show="showCategories">Show Less</span>
                                </button>
                            </li>
                        </ul>

                        <h2 class="text-lg font-bold mt-4">Origins</h2>
                        <ul class="text-sm mt-2" x-data="{ showOrigins: false }">
                            @foreach ($origins->take(5) as $origin)
                            <li><a href="{{ route('items.index', ['origin' => $origin->id]) }}"
                                    class="hover:underline">{{ e($origin->name) }}</a></li>
                            @endforeach

                            <div x-show="showOrigins">
                                @foreach ($origins->skip(5) as $origin)
                                <li><a href="{{ route('items.index', ['origin' => $origin->id]) }}"
                                        class="hover:underline">{{ e($origin->name) }}</a></li>
                                @endforeach
                            </div>

                            <li>
                                <button @click="showOrigins = !showOrigins" class="hover:underline text-blue-400">
                                    <span x-show="!showOrigins">Show More</span>
                                    <span x-show="showOrigins">Show Less</span>
                                </button>
                            </li>
                        </ul>

                        <h2 class="text-lg font-bold mt-4">Organizations</h2>
                        <ul class="text-sm mt-2" x-data="{ showOrganizations: false }">
                            @foreach ($organizations->take(5) as $organization)
                            <li><a href="{{ route('items.index', ['organization' => $organization->id]) }}"
                                    class="hover:underline">{{ e($organization->name) }}</a></li>
                            @endforeach

                            <div x-show="showOrganizations">
                                @foreach ($organizations->skip(5) as $organization)
                                <li><a href="{{ route('items.index', ['organization' => $organization->id]) }}"
                                        class="hover:underline">{{ e($organization->name) }}</a></li>
                                @endforeach
                            </div>

                            <li>
                                <button @click="showOrganizations = !showOrganizations"
                                    class="hover:underline text-blue-400">
                                    <span x-show="!showOrganizations">Show More</span>
                                    <span x-show="showOrganizations">Show Less</span>
                                </button>
                            </li>
                        </ul>

                        <h2 class="text-lg font-bold mt-4">Nationalities</h2>
                        <ul class="text-sm mt-2" x-data="{ showNationalities: false }">
                            @foreach ($nationalities->take(5) as $nationality)
                            <li><a href="{{ route('items.index', ['nationality' => $nationality->id]) }}"
                                    class="hover:underline">{{ e($nationality->name) }}</a></li>
                            @endforeach

                            <div x-show="showNationalities">
                                @foreach ($nationalities->skip(5) as $nationality)
                                <li><a href="{{ route('items.index', ['nationality' => $nationality->id]) }}"
                                        class="hover:underline">{{ e($nationality->name) }}</a></li>
                                @endforeach
                            </div>

                            <li>
                                <button @click="showNationalities = !showNationalities"
                                    class="hover:underline text-blue-400">
                                    <span x-show="!showNationalities">Show More</span>
                                    <span x-show="showNationalities">Show Less</span>
                                </button>
                            </li>
                        </ul>
                    </aside>

                    <!-- Items Grid -->
                    <div class="flex-1 min-w-0">
                        <div class="mx-auto max-w-screen-xl px-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 h-full">
                                @foreach ($items as $item)
                                <a href="{{ route('items.show', $item) }}"
                                    class="bg-[#697367] text-white p-4 rounded-md shadow-md flex flex-col h-full hover:bg-[#5a6452]">
                                    <!-- Title -->
                                    <div class="mb-1 flex-grow h-auto">
                                        <h3 class="text-lg font-bold text-center mb-2">{{ $item->title }}</h3>
                                    </div>

                                    <!-- Photo -->
                                    <div class="flex-1 flex justify-center items-center h-80">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                            class="w-full h-48 object-contain"
                                            width="100%" height="192">
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            <!-- Pagination -->
                            <div class="text-white text-center col-span-5 mt-4">
                                {{ $items->links('pagination::tailwind') }}
                            </div>
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