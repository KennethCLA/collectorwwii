<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Collectorwwii is a website where you can find all kinds of items from the second world war.">
    <title>COLLECTORWWII</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <link rel="shortcut icon" href="../storage/images/wwii-tank-icon.ico" type="image/x-icon">

    <style>
        .breadcrumbs a {
            color: #a0aec0;
            /* Grijze kleur voor links */
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: #4a5568;
            /* Donkerder grijs bij hover */
        }

        .breadcrumbs li {
            display: inline-block;
            /* Zorgt ervoor dat de items naast elkaar komen */
        }

        .breadcrumbs .separator {
            margin: 0 8px;
            /* Voeg ruimte toe tussen items, indien nodig */
        }
    </style>

</head>

<body class="bg-[#565e55] min-h-screen pt-24">
    <div class="min-h-full">
        <header class="fixed top-0 left-0 w-full h-24 bg-gray-800 z-50">
            <nav class="bg-[#565e55] sticky top-0">
                <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-14 items-center justify-between">

                        <!-- Logo -->
                        <div class="flex-1 flex justify-start">
                            <a href="{{ url('/') }}">
                                <img class="h-5 w-auto" src="{{ asset('storage/images/wwii-collector-logo.png') }}"
                                    alt="Collectorwwii-logo">
                            </a>
                        </div>

                        <!-- Gebruikersopties -->
                        <div class="flex-1 flex justify-end space-x-4">
                            <!-- Navigatie -->
                            <div class="flex-1 flex justify-end">
                                <div class="hidden md:block">
                                    <div class="ml-10 flex items-baseline space-x-4">
                                        <x-nav-link href="/blog" :active="request()->is('blog')">Blog</x-nav-link>
                                        <x-nav-link href="/for-sale" :active="request()->is('for-sale')">For Sale</x-nav-link>
                                        <x-nav-link href="/map" :active="request()->is('map')">Map</x-nav-link>
                                        <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-4">

                                <!-- Music button -->
                                @if (auth()->check() && auth()->user()->is_admin)
                                    <button type="button" x-data="{ muted: true }" @click="muted = !muted"
                                        class="relative rounded-md p-1 text-gray-400 hover:text-white">
                                        <span class="sr-only">Toggle volume</span>
                                        <template x-if="muted">
                                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11.25 5.25 6 9H3.75v6H6l5.25 3.75v-13.5zM16.5 9l3 3m0 0-3 3m3-3h.01" />
                                            </svg>
                                        </template>
                                        <template x-if="!muted">
                                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11.25 5.25 6 9H3.75v6H6l5.25 3.75v-13.5zM16.5 8.25a4.5 4.5 0 0 1 0 7.5" />
                                            </svg>
                                        </template>
                                    </button>
                                @endif

                                <div x-data="{ open: false }" class="relative">
                                    <!-- Link naar profiel of login -->
                                    @auth
                                        <a href="{{ route('profile.index') }}" class="flex items-center">
                                            <img class="size-4 rounded-sm"
                                                src="{{ asset('storage/images/icon-hitler.png') }}" alt="Profiel">
                                        </a>
                                    @else
                                        <!-- Button die de popup opent -->
                                        <button type="button"
                                            class="relative flex max-w-xs items-center rounded-md text-sm text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 font-medium"
                                            @click="open = !open" aria-expanded="false" aria-haspopup="true">
                                            <span class="absolute -inset-1.5"></span>

                                            <a href="{{ route('login') }}" class="flex items-center">
                                                <img class="size-4 rounded-sm"
                                                    src="{{ asset('storage/images/icon-user-regular.svg') }}"
                                                    alt="Login">
                                            </a>
                                        </button>
                                    @endauth

                                    <!-- Dropdown -->
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute right-0 mt-14 w-64 bg-[#697367] rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-4">
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <label class="block text-sm font-medium text-gray-700">Username</label>
                                            <input type="name" name="name" required
                                                class="w-full px-3 py-2 mt-1 border rounded-md focus:ring bg-[#565e55] border-gray-900">

                                            <label class="block text-sm font-medium text-gray-700 mt-2">Password</label>
                                            <input type="password" name="password" required
                                                class="w-full px-3 py-2 mt-1 border rounded-md focus:ring bg-[#565e55] border-gray-900">

                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="remember" class="mr-2 rounded-md">
                                                <span class="text-sm text-gray-700">Remember me</span>
                                            </label>

                                            <button type="submit"
                                                class="w-full bg-gray-900 text-white py-2 rounded-md mt-3 hover:bg-gray-700">
                                                Login
                                            </button>
                                        </form>

                                        <p class="mt-2 text-sm text-gray-600">
                                            No account yet?
                                            <a href="{{ route('register') }}"
                                                class="text-blue-600 hover:underline">Register
                                                here</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="-mr-2 flex md:hidden">
                            <!-- Mobile menu button -->
                            <button type="button"
                                class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden"
                                aria-controls="mobile-menu" aria-expanded="false">
                                <span class="absolute -inset-0.5"></span>
                                <span class="sr-only">Open main menu</span>
                                <!-- Menu open: "hidden", Menu closed: "block" -->
                                <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true" data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                                <!-- Menu open: "block", Menu closed: "hidden" -->
                                <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true" data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu, show/hide based on menu state. -->
                <div class="md:hidden" id="mobile-menu">
                    <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="/"
                            class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white"
                            aria-current="page">Home</a>
                        <a href="/about"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">About</a>
                        <a href="/contact"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Contact</a>
                    </div>
                    <div class="border-t border-gray-700 pt-4 pb-3">
                        <div class="flex items-center px-5">
                            <div class="shrink-0">
                                <img class="size-10 rounded-full" src="https://laracasts.com/images/lary-ai-face.svg"
                                    alt="">
                            </div>
                            <div class="ml-3">
                                <div class="text-base/5 font-medium text-white">Lary Robot</div>
                                <div class="text-sm font-medium text-gray-400">kenneth@example.com</div>
                            </div>
                            <button type="button"
                                class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">View notifications</span>
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true" data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
            <nav class="bg-[#697367] sticky top-14">
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
            </nav>
        </header>

        <main class="h-full max-w-[80%] mx-auto px-4 pt-6 pb-8 sm:px-6 lg:px-8">
            {{ $content }}
        </main>
    </div>

    <script>
        document.querySelector('[aria-controls="mobile-menu"]').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>

</html>
