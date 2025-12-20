<!-- resources/views/home.blade.php -->

<x-layout :bodyClass="'home home-' . $season">
    <div class="h-full max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-[#2c3335]/65 ring-1 ring-black/30 px-8 py-8 text-center">
            <h1 class="text-5xl sm:text-6xl font-black tracking-tight text-[#6c2114]"
                style="filter: drop-shadow(0 1px 1px rgba(255, 255, 255, 0.6));">
                COLLECTORWWII
            </h1>
            <div class="mx-auto mt-4 h-px w-40 bg-[#c2b280]/30"></div>
            <p class="mt-3 text-bold sm:text-xl text-white/70"
                style="filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.6));">
                Welcome to COLLECTORWWII!
            </p>
        </div>

        <!-- Language Switcher -->
        <div class="mt-6 flex justify-center">
            <div class="inline-flex gap-2 rounded-2xl bg-[#2c3335]/65 ring-1 ring-black/30 px-3 py-2">
                @foreach ([
                ['en','flag-united-kingdom.png','English'],
                ['nl','flag-netherlands.png','Nederlands'],
                ['de','flag-germany.png','Deutsch'],
                ['fr','flag-france.png','Français'],
                ] as [$code,$img,$label])
                <a href="{{ route('changeLanguage', $code) }}"
                    class="rounded-xl p-1 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
                    aria-label="Switch to {{ $label }}">
                    <img src="{{ asset('images/'.$img) }}"
                        class="h-10 w-10 rounded-lg ring-1 ring-black/40 transition hover:-translate-y-0.5"
                        alt="" />
                </a>
                @endforeach
            </div>
        </div>


        <!-- Latest Blog -->
        @if ($latestBlog)
        <div class="mt-10 mx-auto max-w-3xl rounded-2xl bg-[#2c3335]/65 ring-1 ring-black/30 px-6 py-6 text-white">
            <p class="text-xs tracking-[0.2em] text-white/70 text-center">
                {{ \Carbon\Carbon::parse($latestBlog['date'])->format('d-m-Y') }}
            </p>
            <div class="mt-4 h-px w-32 mx-auto bg-[#c2b280]/35"></div>
            <div class="mt-5 space-y-4 text-white/90 leading-relaxed">
                @foreach (explode("\n\n", $latestBlog['content']) as $paragraph)
                <p>{{ $paragraph }}</p>
                @endforeach
            </div>
            <div class="mt-4 h-px w-32 mx-auto bg-[#c2b280]/35"></div>
            <p class="mt-6 text-center text-lg font-semibold tracking-wide">
                COLLECTORWWII
            </p>
        </div>
        @else
        <p class="mt-10 text-white/80 text-center">No blog posts available yet.</p>
        @endif
    </div>
</x-layout>