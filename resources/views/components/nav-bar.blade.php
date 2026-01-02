   {{-- resources/views/components/nav-bar.blade.php --}}
   <div id="main-navbar" class="sticky top-0 z-50 transition-shadow">
       {{-- BAR 1 --}}
       <div class="bg-[#4f5750]">
           <div class="relative h-16">

               {{-- LEFT: Logo --}}
               <div class="absolute inset-y-0 left-0 flex items-center pl-4 sm:pl-6 lg:pl-8">
                   <a href="{{ url('/') }}" class="flex items-center">
                       <img class="h-6 w-auto opacity-90 hover:opacity-100 transition"
                           src="{{ asset('images/wwii-collector-logo.png') }}" alt="CollectorWWII logo">
                   </a>
               </div>

               {{-- CENTER: Main links --}}
               <nav class="hidden md:flex h-full items-center justify-center gap-3">
                   <x-nav-link href="/blog" :active="request()->is('blog')">Blog</x-nav-link>
                   {{-- <x-nav-link href="/for-sale" :active="request()->is('for-sale')">For Sale</x-nav-link>
                   <x-nav-link href="/map" :active="request()->is('map')">Map</x-nav-link> --}}
                   <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link>
               </nav>

               {{-- RIGHT: User actions --}}
               <div class="absolute inset-y-0 right-0 flex items-center pr-4 sm:pr-6 lg:pr-8 gap-4">
                   <div class="hidden md:flex items-center gap-4">
                       @guest
                       <a href="{{ route('login') }}" class="opacity-90 hover:opacity-100 transition" aria-label="Login">
                           <img class="size-4" src="{{ asset('images/icon-user-regular.svg') }}" alt="">
                       </a>
                       @else
                       @can('viewAny', \App\Models\Book::class)
                       <a href="{{ route('admin.dashboard') }}" class="opacity-90 hover:opacity-100 transition" aria-label="Admin dashboard">
                           <img class="size-4" src="{{ asset('images/icon-user-regular.svg') }}" alt="">
                       </a>
                       @endcan

                       <form method="POST" action="{{ route('logout') }}">
                           @csrf
                           <button type="submit" class="text-white/80 hover:text-white text-sm transition">Logout</button>
                       </form>
                       @endguest
                   </div>

                   {{-- Mobile toggle --}}
                   <button type="button"
                       class="md:hidden inline-flex items-center justify-center rounded-md bg-black/20 p-2 text-white/80 hover:bg-black/30 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/30"
                       aria-controls="mobile-menu" aria-expanded="false">
                       <span class="sr-only">Open main menu</span>
                       <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                       </svg>
                   </button>
               </div>

           </div>
       </div>

       <div class="h-px bg-black"></div>

       {{-- BAR 2 (desktop + mobile) --}}
       <div class="bg-[#636c65] hidden md:block">
           <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
               <div class="h-12 flex items-center justify-center gap-2">

                   @if(config('collector.enabled_sections.books'))
                   <x-nav-link href="{{ route('books.index') }}"
                       :active="request()->routeIs('books.*')"
                       class="tracking-wide">
                       Books
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.items'))
                   <x-nav-link href="{{ route('items.index') }}"
                       :active="request()->routeIs('items.*')"
                       class="tracking-wide">
                       Items
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.magazines'))
                   <x-nav-link href="/magazines" :active="request()->is('magazines')" class="tracking-wide">
                       Magazines
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.banknotes'))
                   <x-nav-link href="/banknotes" :active="request()->is('banknotes')" class="tracking-wide">
                       Banknotes
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.coins'))
                   <x-nav-link href="/coins" :active="request()->is('coins')" class="tracking-wide">
                       Coins
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.postcards'))
                   <x-nav-link href="/postcards" :active="request()->is('postcards')" class="tracking-wide">
                       Postcards
                   </x-nav-link>
                   @endif

                   @if(config('collector.enabled_sections.stamps'))
                   <x-nav-link href="/stamps" :active="request()->is('stamps')" class="tracking-wide">
                       Stamps
                   </x-nav-link>
                   @endif

               </div>
           </div>

           {{-- Mobile menu (hidden by default) --}}
           <div class="md:hidden hidden" id="mobile-menu">
               <div class="bg-[#636c65] border-t border-black/30">
                   <div class="px-4 py-4 space-y-4">

                       <div class="rounded-xl bg-black/20 ring-1 ring-black/30 p-3">
                           <div class="text-xs tracking-[0.2em] text-white/70 mb-2">MAIN</div>
                           <div class="flex flex-col gap-2">
                               <x-nav-link href="/blog" :active="request()->is('blog')">Blog</x-nav-link>
                               <x-nav-link href="/for-sale" :active="request()->is('for-sale')">For Sale</x-nav-link>
                               <x-nav-link href="/map" :active="request()->is('map')">Map</x-nav-link>
                               <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link>
                           </div>
                       </div>

                       <div class="rounded-xl bg-black/20 ring-1 ring-black/30 p-3">
                           <div class="text-xs tracking-[0.2em] text-white/70 mb-2">COLLECTION</div>
                           <div class="flex flex-col gap-2">
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

                       <div class="rounded-xl bg-black/20 ring-1 ring-black/30 p-3">
                           <div class="text-xs tracking-[0.2em] text-white/70 mb-2">ACCOUNT</div>

                           @guest
                           <a href="{{ route('login') }}"
                               class="block rounded-md px-3 py-2 text-sm font-medium text-gray-200 hover:bg-black/20 hover:text-white transition">
                               Login
                           </a>
                           @else
                           @can('viewAny', \App\Models\Book::class)
                           <a href="{{ route('admin.dashboard') }}"
                               class="block rounded-md px-3 py-2 text-sm font-medium text-gray-200 hover:bg-black/20 hover:text-white transition">
                               Dashboard
                           </a>
                           @endcan

                           <form method="POST" action="{{ route('logout') }}">
                               @csrf
                               <button type="submit"
                                   class="w-full text-left rounded-md px-3 py-2 text-sm font-medium text-gray-200 hover:bg-black/20 hover:text-white transition">
                                   Logout
                               </button>
                           </form>
                           @endguest
                       </div>
                   </div>
               </div>
           </div>
       </div>