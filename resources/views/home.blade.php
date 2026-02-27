<!-- resources/views/home.blade.php -->

<x-layout :mainClass="'w-full p-0'" :bodyClass="'home home-' . $season . ' overflow-hidden'">
    @php
    $homeText = trim((string) ($latestBlog['content'] ?? ''));
    $excerptParagraphs = array_values(array_filter(array_map('trim', explode("\n\n", $homeText))));
    $excerptParagraphs = array_slice($excerptParagraphs, 0, 2);
    @endphp

    <section class="mx-auto flex h-[calc(100dvh-var(--header-h,0px))] w-full max-w-5xl flex-col justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <div class="rounded-2xl bg-[#2c3335]/65 ring-1 ring-black/30 px-5 py-5 sm:px-8 sm:py-6 text-center">
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-[#6c2114]"
                    style="filter: drop-shadow(0 1px 1px rgba(255, 255, 255, 0.6));">
                    COLLECTORWWII
                </h1>
                <div class="mx-auto mt-3 h-px w-40 bg-[#c2b280]/30"></div>
                <p class="mt-2 text-sm sm:text-lg text-white/80"
                    style="filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.6));">
                    Welcome to COLLECTORWWII!
                </p>
            </div>

            <!-- Language Switcher -->
            <div class="flex justify-center">
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
                            class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg ring-1 ring-black/40 transition hover:-translate-y-0.5"
                            alt="" />
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Latest Blog -->
            @if (!empty($excerptParagraphs))
            <div class="mx-auto w-full max-w-3xl rounded-2xl bg-[#2c3335]/65 ring-1 ring-black/30 px-5 py-4 text-white sm:px-6 sm:py-5">
                <p class="text-xs tracking-[0.2em] text-white/70 text-center">
                    {{ \Carbon\Carbon::parse($latestBlog['date'])->format('d-m-Y') }}
                </p>
                <div class="mt-3 h-px w-32 mx-auto bg-[#c2b280]/35"></div>
                <div class="mt-3 space-y-2 text-sm sm:text-base text-white/90 leading-relaxed">
                    @foreach ($excerptParagraphs as $paragraph)
                    <p>{{ \Illuminate\Support\Str::limit($paragraph, 340) }}</p>
                    @endforeach
                </div>
                <div class="mt-3 h-px w-32 mx-auto bg-[#c2b280]/35"></div>
                <p class="mt-3 text-center text-sm sm:text-base font-semibold tracking-wide">
                    COLLECTORWWII
                </p>
            </div>
            @endif
        </div>

        <div class="flex items-center justify-center gap-4 text-xs text-white/75 sm:text-sm">
            <a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a>
            <a href="{{ route('for-sale.index') }}" class="hover:text-white transition">For sale</a>
            <a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a>
            <span class="text-white/55">|</span>
            <span>&copy; {{ now()->year }} CollectorWWII</span>
        </div>
    </section>
</x-layout>
