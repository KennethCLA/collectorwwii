{{-- resources/views/items/show.blade.php --}}

<x-layout :mainClass="'mx-auto w-full max-w-none px-0 py-8'">
    @php
    $images = $item->images; // collection (al geladen)
    $main = $item->mainImageFile(); // gebruikt loaded relations (na stap 2A)
    $mainUrl = $main?->url();

    $files = $item->files; // collection (al geladen)
    $pdfs = $files->filter(fn($f) => $f->isPdf())->values();
    @endphp


    <style>
        .item-layout {
            display: grid;
            grid-template-columns: 560px minmax(0, 1fr);
            gap: 3.5rem;
            align-items: start;
            margin-top: 10px;
        }

        /* 1 bron van waarheid voor cover + pdf hoogte */
        :root {
            --media-frame-h: 540px;
            --media-img-max: 520px;
        }

        @media (max-width: 1100px) {
            .item-layout {
                grid-template-columns: 1fr;
            }

            .item-sticky {
                position: static !important;
            }
        }
    </style>

    <div class="w-full max-w-[2200px] mx-auto px-8 sm:px-12 lg:px-16 2xl:px-24">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-sm text-white/70 mb-10">
            <a href="{{ route('home') }}" class="hover:text-white hover:underline">Home</a>
            <span class="opacity-50">›</span>
            <a href="{{ route('items.index') }}" class="hover:text-white hover:underline">Items</a>
            <span class="opacity-50">›</span>
            <span class="text-white font-medium">{{ $item->title }}</span>
        </nav>

        @if($previousItem || $nextItem)
        <div class="mb-10 flex items-center justify-between gap-3">
            {{-- Previous --}}
            <div class="w-1/2">
                @if($previousItem)
                <a href="{{ route('items.show', $previousItem) }}"
                    class="group inline-flex w-full items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="text-white/70 group-hover:text-white">←</span>
                    <span class="min-w-0">
                        <span class="block text-xs text-white/60">Previous</span>
                        <span class="block truncate font-semibold">{{ $previousItem->title }}</span>
                    </span>
                </a>
                @endif
            </div>

            {{-- Next --}}
            <div class="w-1/2 text-right">
                @if($nextItem)
                <a href="{{ route('items.show', $nextItem) }}"
                    class="group inline-flex w-full justify-end items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="min-w-0 text-right">
                        <span class="block text-xs text-white/60">Next</span>
                        <span class="block truncate font-semibold">{{ $nextItem->title }}</span>
                    </span>
                    <span class="text-white/70 group-hover:text-white">→</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <div class="item-layout">
            {{-- LEFT: MEDIA --}}
            <aside class="item-sticky sticky top-8 space-y-10">
                {{-- MAIN MEDIA CARD --}}
                <section class="rounded-2xl shadow-lg p-7 space-y-6 bg-[#697367] border border-black/20 p-4 mb-4">
                    <header class="space-y-3 mb-4 text-center">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
                            {{ $item->title }}
                        </h1>

                        @if (!empty($item->description))
                        <div class="h-px bg-white/20"></div>
                        <p class="text-white/80 italic">
                            {{ $item->description }}
                        </p>
                        @endif
                    </header>

                    <div class="h-px bg-white/15"></div>

                    {{-- MAIN IMAGE --}}
                    <div class="rounded-2xl p-4">
                        <div class="flex items-center justify-center" style="height: var(--media-frame-h);">
                            @if ($mainUrl)
                            <a href="{{ $mainUrl }}" data-fancybox="gallery" class="block">
                                <img
                                    src="{{ $mainUrl }}"
                                    alt="{{ $item->title }}"
                                    class="w-auto max-w-full object-contain rounded-xl"
                                    style="max-height: var(--media-img-max);"
                                    loading="lazy">
                            </a>
                            @else
                            <img
                                src="{{ asset('images/error-image-not-found.png') }}"
                                alt="Image not found"
                                class="w-auto max-w-full object-contain rounded-xl"
                                style="max-height: var(--media-img-max);"
                                loading="lazy">
                            @endif
                        </div>
                    </div>

                    {{-- THUMBNAILS (center als weinig, scroll + snap als veel) --}}
                    @php
                    $mainId = $main?->id;
                    $thumbs = $images->filter(fn ($img) => $img->id !== $mainId)->values();

                    $thumbCount = $thumbs->count();
                    $enableScroll = $thumbCount >= 7; // vanaf 7: scroll + snap
                    @endphp

                    @if ($thumbCount > 0)
                    <div class="pt-2">
                        <div class="{{ $enableScroll ? 'overflow-x-auto snap-x snap-mandatory scroll-px-2' : 'overflow-x-hidden' }}">
                            <div class="flex gap-3 pb-1 {{ $enableScroll ? 'justify-start px-1' : 'justify-center' }}">
                                @foreach ($thumbs as $img)
                                @php $url = $img->url(); @endphp
                                @if ($url)
                                <a
                                    href="{{ $url }}"
                                    data-fancybox="gallery"
                                    class="shrink-0 snap-start rounded-lg overflow-hidden bg-[#343933] ring-1 ring-white/10 hover:ring-white/30 transition">
                                    <img
                                        src="{{ $url }}"
                                        alt=""
                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover"
                                        loading="lazy">
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </section>

                {{-- PDF PREVIEW(S) --}}
                @if ($pdfs->count() > 0)
                @foreach ($pdfs as $i => $pdf)
                @php
                $pdfUrl = $pdf->url();
                $pdfTitle = $pdf->original_name
                ? pathinfo($pdf->original_name, PATHINFO_FILENAME)
                : ('PDF ' . ($i + 1));
                @endphp

                @if ($pdfUrl)
                <section class="rounded-2xl shadow-lg p-7 space-y-5 bg-[#697367] border border-black/20 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-white truncate">
                                {{ $pdfTitle }}
                            </h2>
                        </div>
                        <a
                            href="{{ $pdfUrl }}"
                            target="_blank"
                            rel="noopener"
                            class="shrink-0 text-sm px-3 py-2 rounded-md bg-white/10 text-white hover:bg-white/15 transition">
                            Open
                        </a>
                    </div>

                    <div class="rounded-2xl p-4 bg-[#343933] border border-white/10">
                        <div class="overflow-hidden rounded-xl bg-black/20" style="height: var(--media-frame-h);">
                            <iframe
                                src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=1"
                                class="w-full h-full"
                                loading="lazy"></iframe>
                        </div>
                    </div>
                </section>
                @endif
                @endforeach
                @endif
            </aside>

            {{-- RIGHT: INFO (2 cards) --}}
            <div class="space-y-10">
                {{-- PUBLIC INFO --}}
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10 mb-4">
                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Category</dt>
                            <dd class="text-white text-lg">{{ $item->category?->name ?? '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Nationality</dt>
                            <dd class="text-white text-lg">{{ $item->nationality?->name ?? '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Origin</dt>
                            <dd class="text-white text-lg">{{ $item->origin?->name ?? '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Organization</dt>
                            <dd class="text-white text-lg">{{ $item->organization?->name ?? '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">For sale</dt>
                            <dd class="text-white text-lg">{{ $item->for_sale ? 'Yes' : 'No' }}</dd>
                        </div>

                        @if($item->selling_price !== null)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Selling price</dt>
                            <dd class="text-white text-lg">{{ number_format($item->selling_price, 2, ',', '.') }}</dd>
                        </div>
                        @endif

                        @if($item->current_price !== null)
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Current price</dt>
                            <dd class="text-white text-lg">{{ number_format($item->current_price, 2, ',', '.') }}</dd>
                        </div>
                        @endif
                    </dl>
                </section>

                {{-- ADMIN INFO --}}
                @canany(['update','delete'], $item)
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-extrabold">Admin info</h2>
                    </div>

                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Purchase date</dt>
                            <dd class="text-white text-lg">
                                {{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Purchase price</dt>
                            <dd class="text-white text-lg">
                                {{ $item->purchase_price !== null ? number_format($item->purchase_price, 2, ',', '.') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Purchase location</dt>
                            <dd class="text-white text-lg">{{ $item->purchase_location ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Storage location</dt>
                            <dd class="text-white text-lg">{{ $item->storage_location ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Notes</dt>
                            <dd class="text-white/90 whitespace-pre-line">{{ $item->notes ?: '—' }}</dd>
                        </div>

                        {{-- ADMIN ACTIONS --}}
                        <div class="mt-8 pt-6 border-t border-white/15 flex flex-wrap items-center justify-end gap-3">
                            @can('update', $item)
                            <a href="{{ route('admin.items.edit', $item) }}"
                                class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-4 py-2 text-sm text-white hover:bg-white/15 transition">
                                Edit item
                            </a>
                            @endcan

                            @can('delete', $item)
                            <form method="POST"
                                action="{{ route('admin.items.destroy', $item) }}"
                                onsubmit="return confirm('Delete this item? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm text-red-100 hover:bg-red-500/20 transition">
                                    Delete item
                                </button>
                            </form>
                            @endcan
                        </div>
                    </dl>
                </section>
                @endcanany
            </div>
        </div>
    </div>
</x-layout>