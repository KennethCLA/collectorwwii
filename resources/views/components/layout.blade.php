<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Collectorwwii is a website where you can find all kinds of items from the second world war.">
    <title>COLLECTORWWII</title>

    {{-- Tailwind/Alpine/Fancybox from CDN (prima voor dev) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />

    {{-- Favicon vanuit public/images (géén ../) --}}
    <link rel="shortcut icon" href="{{ asset('images/wwii-tank-icon.ico') }}" type="image/x-icon">

    <style>
        .breadcrumbs a {
            color: #a0aec0;
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: #4a5568;
        }

        .breadcrumbs li {
            display: inline-block;
        }

        .breadcrumbs .separator {
            margin: 0 8px;
        }
    </style>
</head>

<body class="bg-[#565e55] min-h-screen pt-24">
    <div class="min-h-full">
        {{-- Gebruik de navbar-component --}}
        <header class="fixed top-0 left-0 w-full h-24 bg-gray-800 z-50">
            <x-nav-bar />
        </header>

        <main class="h-full w-full pt-6 pb-8">
            {{ $content }}
        </main>
    </div>

    <script>
        const btn = document.querySelector('[aria-controls="mobile-menu"]');
        if (btn) {
            btn.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>

</html>