<div class="bg-[#565e55]">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            <div class="flex-1 flex justify-start">
                <a href="{{ url('/') }}">
                    <img class="h-5 w-auto" src="{{ asset('images/wwii-collector-logo.png') }}" alt="CollectorWWII logo">
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

                {{-- Rechts icon (login/profiel/logout) --}}
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                    <a href="{{ route('login') }}" class="flex items-center">
                        <img class="size-4 rounded-sm" src="{{ asset('images/icon-user-regular.svg') }}" alt="Login">
                    </a>
                    @else
                    @can('viewAny', \App\Models\Book::class)
                    <a href="{{ route('admin.dashboard') }}" class="text-white/90 hover:text-white text-sm">
                        Admin
                    </a>
                    @endcan

                    <a href="{{ route('profile.index') }}" class="flex items-center">
                        <img class="size-4 rounded-sm" src="{{ asset('images/icon-user-regular.svg') }}" alt="Profile">
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/90 hover:text-white text-sm">
                            Logout
                        </button>
                    </form>
                    @endguest
                </div>

                {{-- mobile toggle button (zorg dat dit in je layout JS zichtbaar is) --}}
                <div class="-mr-2 flex md:hidden">
                    <button type="button"
                        class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- tweede (onderste) nav-balk – als je die wilt behouden --}}
<div class="bg-[#697367]">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-10 items-center justify-between">
            <div class="flex-1 flex justify-center">
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <x-nav-link href="{{ route('books.index') }}" :active="request()->routeIs('books.*')">Books</x-nav-link>
                        <x-nav-link href="{{ route('items.index') }}" :active="request()->routeIs('items.*')">Items</x-nav-link>
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

{{-- Mobile menu container (hidden by default) --}}
<div class="md:hidden" id="mobile-menu"></div>