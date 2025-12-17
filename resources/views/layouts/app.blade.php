<!doctype html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name','CollectorWWII') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        html {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial
        }
    </style>
</head>

<body class="min-h-full">
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="currentColor">
                    <path d="M3 6a1 1 0 011-1h16a1 1 0 011 1v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6z" />
                    <path d="M3 6l9 6 9-6" />
                </svg>
                <span class="hidden sm:block">CollectorWWII</span>
            </a>


            <form action="{{ route('items.index') }}" method="get" class="hidden md:block flex-1 max-w-xl ml-4">
                {{-- preserve filters while searching --}}
                @foreach (['category','origin','organization','nationality','sort'] as $keep)
                @if (request()->filled($keep))
                <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}" />
                @endif
                @endforeach
                <div class="relative">
                    <input name="search" value="{{ request('search') }}" placeholder="Zoeken naar items…"
                        class="w-full rounded-xl border-slate-300 pl-10 focus:ring-slate-400 focus:border-slate-400" />
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105 5a7.5 7.5 0 0011.65 11.65z" />
                        </svg>
                    </div>
                </div>
            </form>


            <nav class="ml-auto flex items-center gap-2 text-sm">
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="{{ route('items.index') }}">Items</a>
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="{{ route('books.index') }}">Books</a>
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="{{ route('blog') }}">Blog</a>
                @guest
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="{{ route('login') }}">Login</a>
                @else
                @if(auth()->user()->role_id === 1)
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100 font-semibold text-slate-700"
                    href="{{ route('admin.dashboard') }}">
                    Admin
                </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg hover:bg-slate-100">
                        Logout
                    </button>
                </form>
                @endguest
            </nav>
        </div>
    </header>


    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>


    <footer class="border-t border-slate-200 py-6 text-center text-sm text-slate-500">&copy; {{ now()->year }} CollectorWWII</footer>
</body>

</html>