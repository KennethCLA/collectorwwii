{{-- resources/views/layouts/app.blade.php --}}
@php
$title = $title ?? config('app.name', 'CollectorWWII');
$bodyClass = $bodyClass ?? '';
$mainClass = $mainClass ?? 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6';

// Automatisch admin header op login/password/admin*
$autoAdmin =
request()->routeIs('login') ||
request()->is('password/*') ||
request()->is('admin*') ||
request()->routeIs('admin.*');
$isHome = request()->routeIs('home');

$useAdminHeader = $useAdminHeader ?? $autoAdmin;
@endphp

<!doctype html>
<html lang="en-GB" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? 'CollectorWWII – A curated catalogue of WWII books, items, banknotes, coins, magazines, newspapers, postcards and stamps.' }}">
    <title>{{ $title }}</title>

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'CollectorWWII – A curated catalogue of WWII books, items, banknotes, coins, magazines, newspapers, postcards and stamps.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/wwii-collector-logo.png') }}">
    <meta property="og:site_name" content="CollectorWWII">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Share+Tech+Mono&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('images/wwii-tank-icon.ico') }}" type="image/x-icon">
</head>

<body id="app-body" class="min-h-screen flex flex-col bg-sage-500 {{ $bodyClass }}">
    <a href="#app-main"
        class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[70] rounded-md bg-black px-3 py-2 text-sm text-white">
        Skip to content
    </a>
    {{-- Fixed header wrapper (1 plek die de hoogte bepaalt) --}}
    <header id="main-navbar" class="fixed top-0 left-0 w-full z-50 transition-shadow">
        @if($useAdminHeader)
        <x-admin-header />
        @else
        <x-nav-bar />
        @endif
    </header>

    {{-- Mobile menu: lives outside the fixed header so it can scroll freely --}}
    <div id="mobile-menu"
         class="md:hidden"
         x-data="{ open: false, savedY: 0 }"
         @toggle-mobile-menu.window="
             open = !open;
             if (open) {
                 savedY = window.scrollY;
                 document.body.style.overflow = 'hidden';
             } else {
                 document.body.style.overflow = '';
             }
         "
         x-show="open"
         x-cloak
         style="position:fixed; left:0; right:0; bottom:0; top:var(--header-h,65px); z-index:60; overflow-y:scroll; -webkit-overflow-scrolling:touch; background:#636c65; border-top:1px solid rgba(0,0,0,0.3);">
        <div style="padding:1rem;">
            <div style="display:flex;flex-direction:column;gap:1rem;">

                {{-- Search --}}
                <form method="GET" action="{{ route('search.index') }}" style="display:flex;gap:0.5rem;">
                    <input type="search" name="q" value="{{ request('q') }}"
                        placeholder="Search the collection…"
                        style="flex:1;border-radius:0.375rem;border:1px solid rgba(255,255,255,0.1);background:rgba(0,0,0,0.3);padding:0.5rem 0.75rem;font-size:0.875rem;color:white;">
                    <button type="submit"
                        style="border-radius:0.375rem;background:rgba(255,255,255,0.1);padding:0.5rem 1rem;font-size:0.875rem;color:white;">
                        Go
                    </button>
                </form>

                {{-- MAIN --}}
                <div style="border-radius:0.75rem;background:rgba(0,0,0,0.2);padding:0.75rem;">
                    <div style="font-size:0.75rem;letter-spacing:0.2em;color:rgba(255,255,255,0.7);margin-bottom:0.5rem;">MAIN</div>
                    <div style="display:flex;flex-direction:column;gap:0.25rem;">
                        <a href="/blog" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Blog</a>
                        <a href="{{ route('map.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Map</a>
                        <a href="/for-sale" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">For Sale</a>
                        <a href="/contact" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Contact</a>
                    </div>
                </div>

                {{-- COLLECTION --}}
                <div style="border-radius:0.75rem;background:rgba(0,0,0,0.2);padding:0.75rem;">
                    <div style="font-size:0.75rem;letter-spacing:0.2em;color:rgba(255,255,255,0.7);margin-bottom:0.5rem;">COLLECTION</div>
                    <div style="display:flex;flex-direction:column;gap:0.25rem;">
                        @if(config('collector.enabled_sections.books'))
                        <a href="{{ route('books.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Books</a>
                        @endif
                        @if(config('collector.enabled_sections.items'))
                        <a href="{{ route('items.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Items</a>
                        @endif
                        @if(config('collector.enabled_sections.magazines'))
                        <a href="{{ route('magazines.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Magazines</a>
                        @endif
                        @if(config('collector.enabled_sections.newspapers'))
                        <a href="{{ route('newspapers.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Newspapers</a>
                        @endif
                        @if(config('collector.enabled_sections.banknotes'))
                        <a href="{{ route('banknotes.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Banknotes</a>
                        @endif
                        @if(config('collector.enabled_sections.coins'))
                        <a href="{{ route('coins.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Coins</a>
                        @endif
                        @if(config('collector.enabled_sections.postcards'))
                        <a href="{{ route('postcards.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Postcards</a>
                        @endif
                        @if(config('collector.enabled_sections.stamps'))
                        <a href="{{ route('stamps.index') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Stamps</a>
                        @endif
                    </div>
                </div>

                {{-- ACCOUNT --}}
                <div style="border-radius:0.75rem;background:rgba(0,0,0,0.2);padding:0.75rem;">
                    <div style="font-size:0.75rem;letter-spacing:0.2em;color:rgba(255,255,255,0.7);margin-bottom:0.5rem;">ACCOUNT</div>
                    @guest
                    <a href="{{ route('login') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;">Login</a>
                    @else
                    @can('viewAny', \App\Models\Book::class)
                    <a href="{{ route('admin.dashboard') }}" style="display:block;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;text-decoration:none;" @click="open=false;document.body.style.overflow=''">Dashboard</a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="width:100%;text-align:left;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.875rem;color:#e5e7eb;background:none;border:none;cursor:pointer;">Logout</button>
                    </form>
                    @endguest
                </div>

            </div>
        </div>
    </div>

    <main id="app-main"
        class="flex-1 min-h-0 {{ $mainClass }}"
        style="padding-top: var(--header-h, 0px);">
        @yield('content')
    </main>

    @unless($isHome)
    <footer class="mt-8 border-t border-black/20 bg-black/15 py-5">
        <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 px-4 text-sm text-white/70 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} CollectorWWII</p>
            <p class="font-stencil text-[10px] tracking-[0.25em] text-white/25 uppercase">COLLECTORWWII &middot; 51&deg;N 04&deg;E &middot; EST. MMX</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a>
                <a href="{{ route('for-sale.index') }}" class="hover:text-white transition">For sale</a>
                <a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a>
            </div>
        </div>
    </footer>
    @endunless

    <script>
        // pak het echte element met hoogte
        const navWrap = document.getElementById('main-navbar');

        function getNavEl() {
            if (!navWrap) return null;
            return navWrap.firstElementChild || navWrap; // child heeft meestal de echte hoogte
        }

        function setBodyOffset() {
            const navEl = getNavEl();
            if (!navEl) return;

            const h = navEl.getBoundingClientRect().height;
            document.documentElement.style.setProperty('--header-h', h + 'px');
        }

        const onScroll = () => {
            const navEl = getNavEl();
            if (!navEl) return;
            if (window.scrollY > 10) navEl.classList.add('shadow-lg', 'shadow-black/30');
            else navEl.classList.remove('shadow-lg', 'shadow-black/30');
        };

        window.addEventListener('scroll', onScroll);
        window.addEventListener('load', () => {
            setBodyOffset();
            requestAnimationFrame(setBodyOffset);
            onScroll();
        });
        window.addEventListener('resize', setBodyOffset);

        setBodyOffset();
        onScroll();
    </script>
    @stack('scripts')
</body>

</html>
