{{-- resources/views/books/show.blade.php --}}

<x-layout :mainClass="'mx-auto w-full max-w-none px-0 py-8'">
    @php
    $images = $book->images()->get();
    $main = $images->first();
    $mainUrl = $main?->url();

    $files = $book->files()->get();
    $pdfs = $files->filter(fn($f) => $f->isPdf())->values();
    @endphp

    <style>
        .book-layout {
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
            .book-layout {
                grid-template-columns: 1fr;
            }

            .book-sticky {
                position: static !important;
            }
        }
    </style>

    {{-- PAGE WRAPPER (écht breed) --}}
    <div class="w-full max-w-[2200px] mx-auto px-8 sm:px-12 lg:px-16 2xl:px-24">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-sm text-white/70 mb-10">
            <a href="{{ route('home') }}" class="hover:text-white hover:underline">Home</a>
            <span class="opacity-50">›</span>
            <a href="{{ route('books.index') }}" class="hover:text-white hover:underline">Books</a>
            <span class="opacity-50">›</span>
            <span class="text-white font-medium">{{ $book->title }}</span>
        </nav>

        @if($previousBook || $nextBook)
        <div class="mb-10 flex items-center justify-between gap-3">
            {{-- Previous --}}
            <div class="w-1/2">
                @if($previousBook)
                <a href="{{ route('books.show', $previousBook) }}"
                    class="group inline-flex w-full items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="text-white/70 group-hover:text-white">←</span>
                    <span class="min-w-0">
                        <span class="block text-xs text-white/60">Previous</span>
                        <span class="block truncate font-semibold">{{ $previousBook->title }}</span>
                    </span>
                </a>
                @endif
            </div>

            {{-- Next --}}
            <div class="w-1/2 text-right">
                @if($nextBook)
                <a href="{{ route('books.show', $nextBook) }}"
                    class="group inline-flex w-full justify-end items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white hover:bg-white/10 transition">
                    <span class="min-w-0 text-right">
                        <span class="block text-xs text-white/60">Next</span>
                        <span class="block truncate font-semibold">{{ $nextBook->title }}</span>
                    </span>
                    <span class="text-white/70 group-hover:text-white">→</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- CONTENT --}}
        <div class="book-layout">
            {{-- LEFT: MEDIA --}}
            <aside class="book-sticky sticky top-8 space-y-10">
                {{-- COVER + THUMBS --}}
                <section class="rounded-2xl shadow-lg p-7 space-y-6 bg-[#697367] border border-black/20 p-4 mb-4">
                    <header class="space-y-3 mb-4 text-center">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
                            {{ $book->title }}
                        </h1>

                        @if (!empty($book->subtitle))
                        {{-- divider tussen titel en subtitel --}}
                        <div class="h-px bg-white/20"></div>
                        <p class="text-white/80 italic">
                            {{ $book->subtitle }}
                        </p>
                        @endif
                    </header>

                    {{-- divider tussen titelblok en cover --}}
                    <div class="h-px bg-white/15"></div>

                    {{-- COVER FRAME (achtergrond duidelijk) --}}
                    <div class="rounded-2xl p-4">
                        <div class="flex items-center justify-center" style="height: var(--media-frame-h);">
                            @if ($mainUrl)
                            <a href="{{ $mainUrl }}" data-fancybox="gallery" class="block">
                                <img
                                    src="{{ $mainUrl }}"
                                    alt="{{ $book->title }}"
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

                    {{-- Thumbnails --}}
                    @php
                    $mainId = $main?->id;
                    $thumbs = $images->filter(fn ($img) => $img->id !== $mainId)->values();

                    $thumbCount = $thumbs->count();
                    $enableScroll = $thumbCount >= 7; // tweak: vanaf 7 ga je scrollen
                    @endphp

                    @if ($thumbCount > 0)
                    <div class="pt-2">
                        <div class="{{ $enableScroll ? 'overflow-x-auto snap-x snap-mandatory scroll-px-2' : 'overflow-x-hidden' }}">
                            <div class="flex gap-3 pb-1 {{ $enableScroll ? 'justify-start px-1' : 'justify-center' }}"> @foreach ($thumbs as $img)
                                @php $url = $img->url(); @endphp
                                @if ($url)
                                <a href="{{ $url }}"
                                    data-fancybox="gallery"
                                    class="shrink-0 snap-start rounded-lg overflow-hidden bg-[#343933] ring-1 ring-white/10 hover:ring-white/30 transition">
                                    <img src="{{ $url }}"
                                        alt=""
                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover">
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </section>

                {{-- PDF PREVIEW (altijd zichtbaar, cover-size feeling) --}}
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

                    {{-- PDF FRAME (zelfde “blok” als cover) --}}
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

            {{-- RIGHT: INFO (2 aparte containers) --}}
            <div class="space-y-10">
                {{-- Publieke INFO card --}}
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10 mb-4">

                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Author(s)</dt>
                            <dd class="text-white text-lg">
                                {{ $book->authors->pluck('name')->implode(', ') ?: '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">ISBN</dt>
                            <dd class="text-white text-lg">{{ $book->isbn ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Topic</dt>
                            <dd class="text-white text-lg">{{ $book->topic?->name ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Publisher</dt>
                            <dd class="text-white text-lg">{{ $book->publisher_name ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Year</dt>
                            <dd class="text-white text-lg">{{ $book->copyright_year ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Translator</dt>
                            <dd class="text-white text-lg">{{ $book->translator ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Issue Number</dt>
                            <dd class="text-white text-lg">{{ $book->issue_number ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Issue Year</dt>
                            <dd class="text-white text-lg">{{ $book->issue_year ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Series</dt>
                            <dd class="text-white text-lg">
                                {{ $book->series?->name ?: '—' }}
                                @if($book->series_number)
                                <span class="text-white/70">#{{ $book->series_number }}</span>
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Cover</dt>
                            <dd class="text-white text-lg">{{ $book->cover?->name ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Pages</dt>
                            <dd class="text-white text-lg">{{ $book->pages ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Title (First Edition)</dt>
                            <dd class="text-white text-lg">{{ $book->title_first_edition ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Subtitle (First Edition)</dt>
                            <dd class="text-white text-lg">{{ $book->subtitle_first_edition ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Publisher (First Edition)</dt>
                            <dd class="text-white text-lg">{{ $book->publisher_first_issue ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Copyright Year (First Edition)</dt>
                            <dd class="text-white text-lg">{{ $book->copyright_year_first_edition ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">For sale</dt>
                            <dd class="text-white text-lg">{{ $book->for_sale ? 'Yes' : 'No' }}</dd>
                        </div>

                        @if(!empty($book->description))
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Description</dt>
                            <dd class="text-white/90 whitespace-pre-line">{{ $book->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </section>

                {{-- Admin INFO card (aparte container) --}}
                @canany(['update','delete'], $book)
                <section class="bg-[#697367] text-white rounded-2xl shadow-lg border border-black/20 p-8 lg:p-10">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-extrabold">Admin info</h2>
                    </div>

                    <dl class="space-y-6">
                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Purchase date</dt>
                            <dd class="text-white text-lg">
                                {{ $book->purchase_date ? $book->purchase_date->format('d/m/Y') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Purchase price</dt>
                            <dd class="text-white text-lg">
                                {{ $book->purchase_price !== null ? number_format($book->purchase_price, 2, ',', '.') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Location</dt>
                            <dd class="text-white text-lg">
                                {{ $book->purchase_location ?: '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Weight</dt>
                            <dd class="text-white text-lg">
                                {{ $book->weight ?: '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Dimensions (W x H x T)</dt>
                            <dd class="text-white text-lg">
                                {{ $book->dimensions ?: '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Location</dt>
                            <dd class="text-white text-lg">
                                {{ $book->location_id?->name ?: '—' }}
                                @if($book->location_detail)
                                <span class="text-white/70">#{{ $book->location_detail }}</span>
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/20 px-6 py-2 mb-4">
                            <dt class="text-xl text-white/70 font-semibold">Notes</dt>
                            <dd class="text-white/90 whitespace-pre-line">
                                {{ $book->notes ?: '—' }}
                            </dd>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/15 flex flex-wrap items-center justify-end gap-3">
                            @can('update', $book)
                            <a href="{{ route('admin.books.edit', $book) }}"
                                class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-4 py-2 text-sm text-white hover:bg-white/15 transition">
                                Edit book
                            </a>
                            @endcan

                            @can('delete', $book)
                            <form method="POST"
                                action="{{ route('admin.books.destroy', $book) }}"
                                onsubmit="return confirm('Delete this book? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm text-red-100 hover:bg-red-500/20 transition">
                                    Delete book
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