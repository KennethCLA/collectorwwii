<style>
    body {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        /* optioneel, mooi parallax-achtig effect */
        background-image: url("{{ asset('storage/images/wwii-wallpaper-summer.jpg') }}");
    }
</style>


<x-layout>
    <x-slot:content>
        <div class="h-full max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-center">
                <h1 class="text-5xl font-bold tracking-tight text-[#293134] text-shadow">COLLECTORWWII</h1>
            </div>
            <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8 flex justify-center">
                <h2 class="text-lg font-medium tracking-tight text-[#293134]">Welcome to COLLECTORWWII!</h2>
            </div>
            <!-- Language Switcher -->
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-center">
                <div>
                    <a href="{{ route('changeLanguage', 'en') }}">
                        <img src="{{ asset('images/flag-united-kingdom.png') }}" alt="Switch to English" class="px-1.5" width="64" height="64"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'nl') }}"><img
                            src="{{ asset('images/flag-netherlands.png') }}" alt="Switch to Dutch" class="px-1.5" width="64" height="64"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'de') }}"><img
                            src="{{ asset('images/flag-germany.png') }}" alt="Switch to German" class="px-1.5" width="64" height="64"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'fr') }}"><img
                            src="{{ asset('images/flag-france.png') }}" alt="Switch to French" class="px-1.5" width="64" height="64"></a>
                </div>
            </div>

            <!-- Latest Blog -->
            @if ($latestBlog)
            <div class="mx-auto px-4 py-6 sm:px-6 lg:px-8 text-white text-center rounded-xl bg-[#2c333575]">
                <p class="text-lg font-bold">
                    {{ \Carbon\Carbon::parse($latestBlog['date'])->format('d-m-Y') }}
                </p>

                @foreach (explode("\n\n", $latestBlog['content']) as $paragraph)
                <p class="mt-2">{{ $paragraph }}</p>
                @endforeach

                <p class="mt-2 text-2xl font-bold">COLLECTORWWII</p>
            </div>
            @else
            <p class="text-white text-center">No blog posts available yet.</p>
            @endif
        </div>
    </x-slot:content>
</x-layout>