<x-layout>
    <x-slot:content>
        <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-center">
                <h1 class="text-5xl font-bold tracking-tight text-[#293134] text-shadow">COLLECTORWWII blog</h1>
            </div>
            <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8 flex justify-center">
                <h2 class="text-lg font-medium tracking-tight text-[#293134]">Welcome to the COLLECTORWWII blog!</h2>
            </div>
            <!-- Language Switcher -->
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-center">
                <div>
                    <a href="{{ route('changeLanguage', 'en') }}"><img
                            src="{{ asset('storage/images/flag-united-kingdom.png') }}" alt="" class="px-1.5"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'nl') }}"><img
                            src="{{ asset('storage/images/flag-netherlands.png') }}" alt="" class="px-1.5"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'de') }}"><img
                            src="{{ asset('storage/images/flag-germany.png') }} " alt="" class="px-1.5"></a>
                </div>
                <div>
                    <a href="{{ route('changeLanguage', 'fr') }}"><img
                            src="{{ asset('storage/images/flag-france.png') }}" alt="" class="px-1.5"></a>
                </div>
            </div>
            @if ($blogs && count($blogs) > 0)
                @foreach ($blogs as $blog)
                    <div class="mx-auto px-4 py-6 sm:px-6 lg:px-8 text-white text-center rounded-xl bg-[#2c333575]">
                        <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($blog['date'])->format('d-m-Y') }}</p>
                        <p class="mt-2">
                            @foreach (explode("\n\n", $blog['content'][$language] ?? $blog['content']['en']) as $paragraph)
                                <p class="mt-2">{{ $paragraph }}</p>
                            @endforeach
                        </p>
                        <p class="mt-2 text-2xl font-bold">COLLECTORWWII</p>
                    </div>
                    <hr class="my-4">
                @endforeach
            @else
                <p class="text-white text-center">No blog posts available yet.</p>
            @endif
        </div>
    </x-slot:content>
</x-layout>
