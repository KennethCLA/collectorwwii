<div class="bg-[#565e55]">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            <div class="flex-1 flex justify-start">
                <a href="{{ url('/') }}">
                    <img class="h-5 w-auto" src="{{ asset('storage/images/wwii-collector-logo.png') }}" alt="Logo">
                </a>
            </div>

            <div class="flex-1 flex justify-end space-x-4">
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <x-nav-link href="/blog" :active="request()->is('blog')">Blog</x-nav-link>
                        <x-nav-link href="/for-sale" :active="request()->is('for-sale')">For Sale</x-nav-link>
                        <x-nav-link href="/map" :active="request()->is('map')">Map</x-nav-link>
                        <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#697367]">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-10 items-center justify-between">
            <div class="flex-1 flex justify-end space-x-4">
                <!-- Navigatie -->
                <div class="flex-1 flex justify-center">
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <x-nav-link href="/books" :active="request()->is('books')">Books</x-nav-link>
                            <x-nav-link href="/items" :active="request()->is('items')">Items</x-nav-link>
                            <x-nav-link href="/newspapers" :active="request()->is('newspapers')">Newspapers</x-nav-link>
                            <x-nav-link href="/magazines" :active="request()->is('magazines')">Magazines</x-nav-link>
                            <x-nav-link href="/banknotes" :active="request()->is('banknotes')">Banknotes</x-nav-link>
                            <x-nav-link href="/coins" :active="request()->is('coins')">Coins</x-nav-link>
                            <x-nav-link href="/postcards" :active="request()->is('postcards')">Postcards</x-nav-link>
                            <x-nav-link href="/stamps" :active="request()->is('stamps')">Stamps</x-nav-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
