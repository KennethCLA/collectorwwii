{{-- resources/views/coins/show.blade.php --}}

<x-layout :mainClass="'mx-auto w-full max-w-none px-0 py-8'">
    @php
    $images = $coin->images;
    $main = $coin->mainImageFile();
    $mainUrl = $main?->url();
    $files = $coin->files;
    $pdfs = $files->filter(fn($f) => $f->isPdf())->values();
    @endphp

    <style>
        .item-layout { display: grid; grid-template-columns: 560px minmax(0, 1fr); gap: 3.5rem; align-items: start; margin-top: 10px; }
        :root { --media-frame-h: 540px; --media-img-max: 520px; }
        @media (max-width: 1100px) { .item-layout { grid-template-columns: 1fr; } .item-sticky { position: static !important; } }
    </style>

    <div class="w-full max-w-[2200px] mx-auto px-4 sm:px-8 lg:px-16 2xl:px-24">
        <nav class="flex items-center gap-2 text-sm text-white/70 mb-10">
            <a href="{{ route('home') }}" class="hover:text-white hover:underline">Home</a>
            <span class="opacity-50">›</span>
            <a href="{{ route('coins.index') }}" class="hover:text-white hover:underline">Coins</a>
            <span class="opacity-50">›</span>
            <span class="text-white font-medium">{{ $coin->card_title }}</span>
        </nav>

        <div class="item-layout">
            <aside class="item-sticky sticky top-8 space-y-10">
                <section class="rounded-2xl shadow-lg p-7 space-y-6 bg-[#697367] border border-black/20 p-4 mb-4">
                    <header class="space-y-3 mb-4 text-center">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">{{ $coin->card_title }}</h1>
                    </header>
                    <div class="h-px bg-white/15"></div>
                    <div class="rounded-2xl p-4">
                        <div class="flex items-center justify-center" style="height: var(--media-frame-h);">
                            @if ($mainUrl)
                            <a href="{{ $mainUrl }}" data-fancybox="gallery" class="block">
                                <img src="{{ $mainUrl }}" alt="{{ $coin->card_title }}"
                                    class="w-auto max-w-full object-contain rounded-xl"
                                    style="max-height: var(--media-img-max);" loading="lazy">
                            </a>
                            @else
                            <img src="{{ asset('images/error-image-not-found.png') }}" alt="Image not found"
                                class="w-auto max-w-full object-contain rounded-xl"
                                style="max-height: var(--media-img-max);" loading="lazy">
                            @endif
                        </div>
                    </div>
                    @php $mainId = $main?->id; $thumbs = $images->filter(fn($img) => $img->id !== $mainId)->values(); $thumbCount = $thumbs->count(); $enableScroll = $thumbCount >= 7; @endphp
                    @if ($thumbCount > 0)
                    <div class="pt-2">
                        <div class="{{ $enableScroll ? 'overflow-x-auto snap-x snap-mandatory scroll-px-2' : 'overflow-x-hidden' }}">
                            <div class="flex gap-3 pb-1 {{ $enableScroll ? 'justify-start px-1' : 'justify-center' }}">
                                @foreach ($thumbs as $img)
                                @php $url = $img->url(); @endphp
                                @if ($url)
                                <a href="{{ $url }}" data-fancybox="gallery" class="shrink-0 snap-start rounded-lg overflow-hidden bg-[#343933] ring-1 ring-white/10 hover:ring-white/30 transition">
                                    <img src="{{ $url }}" alt="" class="w-16 h-16 sm:w-20 sm:h-20 object-cover" loading="lazy">
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </section>
                @if ($pdfs->count() > 0)
                @foreach ($pdfs as $i => $pdf)
                @php $pdfUrl = $pdf->url(); $pdfTitle = $pdf->original_name ? pathinfo($pdf->original_name, PATHINFO_FILENAME) : ('PDF ' . ($i + 1)); @endphp
                @if ($pdfUrl)
                <section class="rounded-2xl shadow-lg p-7 space-y-5 bg-[#697367] border border-black/20 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0"><h2 class="text-lg sm:text-xl font-bold text-white truncate">{{ $pdfTitle }}</h2></div>
                        <a href="{{ $pdfUrl }}" target="_blank" rel="noopener" class="shrink-0 text-sm px-3 py-2 rounded-md bg-white/10 text-white hover:bg-white/15 transition">Open</a>
                    </div>
                    <div class="rounded-2xl p-4 bg-[#343933] border border-white/10">
                        <div class="overflow-hidden rounded-xl bg-black/20" style="height: var(--media-frame-h);">
                            <iframe src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=1" class="w-full h-full" loading="lazy"></iframe>
                        </div>
                    </div>
                </section>
                @endif
                @endforeach
                @endif
            </aside>

            <div class="space-y-10">
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10 mb-4">
                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Country</dt>
                            <dd class="text-white">{{ $coin->country?->name ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Currency</dt>
                            <dd class="text-white">{{ $coin->currency?->name ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Nominal value</dt>
                            <dd class="text-white">{{ $coin->nominalValue?->name ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Shape</dt>
                            <dd class="text-white">{{ $coin->shape?->name ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Material</dt>
                            <dd class="text-white">{{ $coin->material?->name ?? '—' }}</dd>
                        </div>
                        @if($coin->year)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Year</dt>
                            <dd class="text-white">{{ $coin->year }}</dd>
                        </div>
                        @endif
                        @if($coin->occasion)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Occasion</dt>
                            <dd class="text-white">{{ $coin->occasion->name }}</dd>
                        </div>
                        @endif
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">For sale</dt>
                            <dd class="text-white">{{ $coin->for_sale ? 'Yes' : 'No' }}</dd>
                        </div>
                        @if($coin->selling_price !== null)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Selling price</dt>
                            <dd class="text-white">€ {{ number_format($coin->selling_price, 2, ',', '.') }}</dd>
                        </div>
                        @endif
                    </dl>
                </section>

                @canany(['update','delete'], $coin)
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-extrabold">Admin info</h2>
                    </div>
                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Purchase date</dt>
                            <dd class="text-white">{{ $coin->purchase_date ? $coin->purchase_date->format('d/m/Y') : '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Purchasing price</dt>
                            <dd class="text-white">{{ $coin->purchasing_price !== null ? '€ ' . number_format($coin->purchasing_price, 2, ',', '.') : '—' }}</dd>
                        </div>
                        @if($coin->location)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Location</dt>
                            <dd class="text-white">{{ $coin->location->name }}</dd>
                        </div>
                        @endif
                        @if(!empty($coin->personal_remarks))
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-sm text-white/70 font-semibold">Personal remarks</dt>
                            <dd class="text-white/90 whitespace-pre-line">{{ $coin->personal_remarks }}</dd>
                        </div>
                        @endif
                        <div class="mt-8 pt-6 border-t border-white/15 flex flex-wrap items-center justify-end gap-3">
                            @can('update', $coin)
                            <a href="{{ route('admin.coins.edit', $coin) }}"
                                class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-4 py-2 text-sm text-white hover:bg-white/15 transition">Edit coin</a>
                            @endcan
                            @can('delete', $coin)
                            <form method="POST" action="{{ route('admin.coins.destroy', $coin) }}"
                                onsubmit="return confirm('Delete this coin? This action cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm text-red-100 hover:bg-red-500/20 transition">Delete coin</button>
                            </form>
                            @endcan
                        </div>
                    </dl>
                </section>
                @endcanany
            </div>
        </div>

        @if($previousCoin || $nextCoin)
        <div class="mb-10 mt-10 flex items-center justify-between gap-3">
            <div class="w-1/2">
                @if($previousCoin)
                <a href="{{ route('coins.show', $previousCoin) }}"
                    class="group inline-flex w-full items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="text-white/70 group-hover:text-white">←</span>
                    <span class="min-w-0"><span class="block text-xs text-white/60">Previous</span><span class="block truncate font-semibold">{{ $previousCoin->card_title }}</span></span>
                </a>
                @endif
            </div>
            <div class="w-1/2 text-right">
                @if($nextCoin)
                <a href="{{ route('coins.show', $nextCoin) }}"
                    class="group inline-flex w-full justify-end items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="min-w-0 text-right"><span class="block text-xs text-white/60">Next</span><span class="block truncate font-semibold">{{ $nextCoin->card_title }}</span></span>
                    <span class="text-white/70 group-hover:text-white">→</span>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-layout>
