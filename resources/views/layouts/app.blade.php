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

$useAdminHeader = $useAdminHeader ?? $autoAdmin;
@endphp

<!doctype html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Collectorwwii is a website where you can find all kinds of items from the second world war.">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('images/wwii-tank-icon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
</head>

<body id="app-body" class="min-h-screen flex flex-col bg-[#565e55] {{ $bodyClass }}">
    {{-- Fixed header wrapper (1 plek die de hoogte bepaalt) --}}
    <header id="site-header" class="fixed top-0 left-0 w-full z-50 transition-shadow">
        @if($useAdminHeader)
        <div id="main-navbar" class="fixed top-0 left-0 w-full z-50 transition-shadow">
            <x-admin-header />
        </div>
        @else
        <div id="main-navbar" class="fixed top-0 left-0 w-full z-50 transition-shadow">
            <x-nav-bar />
        </div>
        @endif
    </header>

    <main class="flex-1 {{ $mainClass }}">
        @yield('content')
    </main>

    <footer class="border-t border-black/20 py-6 text-center text-sm text-white/70">
        &copy; {{ now()->year }} CollectorWWII
    </footer>

    <script>
        const nav = document.getElementById('main-navbar');
        const body = document.getElementById('app-body');

        function setBodyOffset() {
            if (!nav || !body) return;
            body.style.paddingTop = nav.getBoundingClientRect().height + 'px';
        }

        // Mobile menu toggle
        const btn = document.querySelector('[aria-controls="mobile-menu"]');
        if (btn) {
            btn.addEventListener('click', () => {
                const mobileMenu = document.getElementById('mobile-menu');
                if (!mobileMenu) return;
                mobileMenu.classList.toggle('hidden');
                btn.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
                requestAnimationFrame(setBodyOffset);
            });
        }

        // Shadow on scroll
        const onScroll = () => {
            if (!nav) return;
            if (window.scrollY > 10) nav.classList.add('shadow-lg', 'shadow-black/30');
            else nav.classList.remove('shadow-lg', 'shadow-black/30');
        };

        window.addEventListener('scroll', onScroll);
        window.addEventListener('load', () => {
            setBodyOffset();
            onScroll();
        });
        window.addEventListener('resize', setBodyOffset);

        setBodyOffset();
        onScroll();
    </script>
    @stack('scripts')
</body>

</html>