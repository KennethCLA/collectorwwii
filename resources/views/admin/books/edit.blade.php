{{-- resources/views/admin/books/edit.blade.php --}}

<x-layout>
    <x-form-layout>
        @php
        $val = function (string $key, $fallback = '') use ($book) {
            return old($key, data_get($book, $key, $fallback));
        };

        $forSaleOld = old('for_sale', $book->for_sale ?? false);
        $forSaleJs = filter_var($forSaleOld, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        @endphp

        <form id="book-form"
            action="{{ route('admin.books.update', $book) }}"
            method="POST"
            class="w-full mx-auto max-w-7xl">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Edit book</h1>
                    <p class="mt-1 text-sm text-white/60">Update the book details.</p>
                </div>

                <a href="{{ route('admin.books.index') }}"
                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                    Back
                </a>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
                <div class="font-semibold mb-2">Please fix the following:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- MAIN --}}
            <div class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-8">

                {{-- PUBLIC FIELDS --}}
                <section class="rounded-xl border border-black/20 bg-black/10 p-6">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <h2 class="text-base font-semibold text-white">Public details</h2>
                        <span class="text-xs text-white/50">Visible on the public book page</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- ISBN --}}
                        <div class="lg:col-span-2 space-y-2">
                            <label for="isbn" class="text-sm font-medium text-white/80">ISBN</label>
                            <input
                                type="text"
                                id="isbn"
                                name="isbn"
                                value="{{ $val('isbn') }}"
                                placeholder="978..."
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                       focus:outline-none focus:ring-2 focus:ring-white/20" />
                        </div>

                        {{-- Authors --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">
                                Author(s) * <span class="text-white/50">(comma separated)</span>
                            </label>
                            <input type="text"
                                name="authors"
                                value="{{ old('authors', $book->authors?->pluck('name')->implode(', ') ?? '') }}"
                                required
                                placeholder="e.g. John Doe, Jane Doe"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Title --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Title *</label>
                            <input type="text"
                                name="title"
                                value="{{ $val('title') }}"
                                required
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Subtitle --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Subtitle</label>
                            <input type="text"
                                name="subtitle"
                                value="{{ $val('subtitle') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Publisher --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Publisher</label>
                            <input type="text"
                                name="publisher_name"
                                value="{{ $val('publisher_name') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Year --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Copyright year</label>
                            <input type="number"
                                name="copyright_year"
                                value="{{ $val('copyright_year') }}"
                                min="1000"
                                max="{{ date('Y') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Pages --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Pages</label>
                            <input type="number"
                                name="pages"
                                value="{{ $val('pages') }}"
                                min="1"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Topic --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Topic</label>

                            <div class="flex items-center gap-2">
                                <select id="topic_id" name="topic_id"
                                    class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($topics as $t)
                                    <option value="{{ $t->id }}" @selected((string)$val('topic_id')===(string)$t->id)>{{ $t->name }}</option>
                                    @endforeach
                                </select>

                                <button type="button"
                                    class="h-10 w-10 mb-6 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add
                                    data-type="topic"
                                    data-select="#topic_id"
                                    title="Add topic">+</button>
                            </div>
                        </div>

                        {{-- Series --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Series</label>

                            <div class="flex items-center gap-2">
                                <select id="series_id" name="series_id"
                                    class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($series as $s)
                                    <option value="{{ $s->id }}" @selected((string)$val('series_id')===(string)$s->id)>{{ $s->name }}</option>
                                    @endforeach
                                </select>

                                <button type="button"
                                    class="h-10 w-10 mb-6 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add
                                    data-type="series"
                                    data-select="#series_id"
                                    title="Add series">+</button>
                            </div>
                        </div>

                        {{-- Series number --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Series #</label>
                            <input type="text"
                                name="series_number"
                                value="{{ $val('series_number') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Cover type --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Cover</label>

                            <div class="flex items-center gap-2">
                                <select id="cover_id" name="cover_id"
                                    class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($covers as $c)
                                    <option value="{{ $c->id }}" @selected((string)$val('cover_id')===(string)$c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>

                                <button type="button"
                                    class="h-10 w-10 mb-6 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add
                                    data-type="cover"
                                    data-select="#cover_id"
                                    title="Add cover">+</button>
                            </div>
                        </div>

                        {{-- Translator --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Translator</label>
                            <input type="text"
                                name="translator"
                                value="{{ $val('translator') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Issue number --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Issue #</label>
                            <input type="text"
                                name="issue_number"
                                value="{{ $val('issue_number') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Issue year --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Issue year</label>
                            <input type="number"
                                name="issue_year"
                                value="{{ $val('issue_year') }}"
                                min="1000"
                                max="{{ date('Y') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- First edition title --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Title (1st ed.)</label>
                            <input type="text"
                                name="title_first_edition"
                                value="{{ $val('title_first_edition') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- First edition subtitle --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Subtitle (1st ed.)</label>
                            <input type="text"
                                name="subtitle_first_edition"
                                value="{{ $val('subtitle_first_edition') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Publisher first issue --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Publisher (1st)</label>
                            <input type="text"
                                name="publisher_first_issue"
                                value="{{ $val('publisher_first_issue') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Copyright year first issue --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Copyright (1st)</label>
                            <input type="number"
                                name="copyright_year_first_issue"
                                value="{{ $val('copyright_year_first_issue') }}"
                                min="1000"
                                max="{{ date('Y') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-white/80">Description</label>
                        <textarea name="description"
                            rows="6"
                            class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                         focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('description', $book->description) }}</textarea>
                    </div>
                </section>

                {{-- ADMIN-ONLY FIELDS --}}
                <section class="rounded-xl border border-white/10 bg-black/20 p-6">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3">
                            <h2 class="text-base font-semibold text-white">Admin-only</h2>
                            <span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">
                                Not visible publicly
                            </span>
                        </div>
                        <span class="text-xs text-white/50">Pricing, storage, internal notes</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Purchase date --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Purchase date</label>
                            <input type="date"
                                name="purchase_date"
                                value="{{ old('purchase_date', $book->purchase_date?->format('Y-m-d') ?? '') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Purchase price --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Purchase €</label>
                            <input type="number"
                                step="0.01"
                                name="purchase_price"
                                value="{{ $val('purchase_price') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                          focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Purchase origin --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Purchase origin</label>

                            <div class="flex items-center gap-2">
                                <select id="origin_id" name="origin_id"
                                    class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($origins as $o)
                                    <option value="{{ $o->id }}" @selected((string)$val('origin_id')===(string)$o->id)>{{ $o->name }}</option>
                                    @endforeach
                                </select>

                                <button type="button"
                                    class="h-10 w-10 mb-6 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add
                                    data-type="origin"
                                    data-select="#origin_id"
                                    title="Add origin">+</button>
                            </div>
                        </div>

                        {{-- Storage location --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Storage location</label>

                            <div class="flex items-center gap-2">
                                <select id="location_id" name="location_id"
                                    class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected((string)$val('location_id')===(string)$loc->id)>{{ $loc->name }}</option>
                                    @endforeach
                                </select>

                                <button type="button"
                                    class="h-10 w-10 mb-6 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add
                                    data-type="location"
                                    data-select="#location_id"
                                    title="Add location">+</button>
                            </div>
                        </div>

                        {{-- For sale --}}
                        <div x-data="{ forSale: {{ $forSaleJs }} }" class="space-y-2">
                            <label class="text-sm font-medium text-white/80">For sale</label>

                            <div class="flex items-center gap-3">
                                <input type="hidden" name="for_sale" value="0">
                                <input type="checkbox"
                                    name="for_sale"
                                    value="1"
                                    x-model="forSale"
                                    class="h-5 w-5 rounded border-white/20 bg-white/10">
                                <span class="text-sm text-white/70">Mark as for sale</span>
                            </div>

                            <div x-show="forSale" x-cloak class="pt-2">
                                <label class="text-sm font-medium text-white/80">Selling price €</label>
                                <input type="number"
                                    step="0.01"
                                    name="selling_price"
                                    value="{{ $val('selling_price') }}"
                                    class="mt-2 w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                              focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-white/80">Notes</label>
                        <textarea name="notes"
                            rows="4"
                            class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40
                                         focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('notes', $book->notes) }}</textarea>
                    </div>

                    {{-- Weight + dimensions --}}
                    <div class="mt-6">
                        <div class="text-sm font-medium text-white/80 mb-2">Physical (optional)</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm text-white/70">Weight (grams)</label>
                                <input type="number" name="weight" value="{{ $val('weight') }}"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm text-white/70">Width</label>
                                <input type="number" name="width" value="{{ $val('width') }}"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm text-white/70">Height</label>
                                <input type="number" name="height" value="{{ $val('height') }}"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm text-white/70">Thickness</label>
                                <input type="number" name="thickness" value="{{ $val('thickness') }}"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- MEDIA (EDIT) --}}
                <section class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-6">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-base font-semibold text-white">Media</h2>
                        <span class="text-xs text-white/50">Upload new files or manage existing ones</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Upload images --}}
                        <div class="rounded-md bg-[#343933] p-4 border border-white/10">
                            <div class="text-white font-semibold mb-2">Upload images</div>

                            <form action="{{ route('admin.media.store', ['type' => 'books', 'id' => $book->id]) }}"
                                method="POST" enctype="multipart/form-data"
                                class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="collection" value="images">

                                <input type="file" name="files[]" multiple accept="image/*"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-white hover:file:bg-white/15">

                                <button type="submit"
                                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                                    Upload images
                                </button>
                            </form>
                        </div>

                        {{-- Upload PDFs --}}
                        <div class="rounded-md bg-[#343933] p-4 border border-white/10">
                            <div class="text-white font-semibold mb-2">Upload PDFs</div>

                            <form action="{{ route('admin.media.store', ['type' => 'books', 'id' => $book->id]) }}"
                                method="POST" enctype="multipart/form-data"
                                class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="collection" value="files">

                                <input type="file" name="files[]" multiple accept="application/pdf"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-white hover:file:bg-white/15">

                                <button type="submit"
                                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                                    Upload PDFs
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-xs text-white/50">Max 50MB per file.</p>

                    @php
                    $mediaImages = $book->images()->get();
                    $mediaPdfs = $book->files()->get()->filter(fn ($f) => $f->isPdf())->values();
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Existing images --}}
                        <div class="bg-[#697367] rounded-md p-4 border border-black/20">
                            <h3 class="text-lg font-semibold text-white mb-3">Images ({{ $mediaImages->count() }})</h3>

                            @if($mediaImages->isEmpty())
                            <p class="text-white/80 text-sm">No images uploaded yet.</p>
                            @else
                            <div class="flex flex-wrap gap-2 items-start">
                                @foreach($mediaImages as $img)
                                @include('admin.books._image-card', ['img' => $img])
                                @endforeach
                            </div>
                            @endif
                        </div>

                        {{-- Existing PDFs --}}
                        <div class="bg-[#697367] rounded-md p-4 border border-black/20">
                            <h3 class="text-lg font-semibold text-white mb-3">PDFs ({{ $mediaPdfs->count() }})</h3>

                            @if($mediaPdfs->isEmpty())
                            <p class="text-white/80 text-sm">No PDFs uploaded yet.</p>
                            @else
                            <div class="space-y-4">
                                @foreach($mediaPdfs as $pdf)
                                @include('admin.books._pdf-card', ['pdf' => $pdf])
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.books.index') }}"
                        class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                        Cancel
                    </a>
                    <button type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        Save changes
                    </button>
                </div>
            </div>
        </form>
    </x-form-layout>

    <dialog id="lookupModal" class="rounded-xl p-0 backdrop:bg-black/60">
        <form method="dialog" class="w-[min(520px,92vw)] bg-[#2b322a] text-white">
            <div class="p-4 border-b border-white/10 flex items-center justify-between">
                <h3 id="lookupModalTitle" class="text-lg font-semibold">Add</h3>
                <button class="px-3 py-1 rounded-md bg-white/10">✕</button>
            </div>
            <div class="p-4 space-y-2">
                <label class="text-sm text-white/80">Name</label>
                <input id="lookupName" type="text"
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white"
                    placeholder="Name..." />
                <p id="lookupError" class="text-sm text-red-300 hidden"></p>
            </div>
            <div class="p-4 border-t border-white/10 flex justify-end gap-2">
                <button value="cancel" class="px-4 py-2 rounded-md bg-white/10">Cancel</button>
                <button id="lookupSaveBtn" type="button" class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700">Add</button>
            </div>
        </form>
    </dialog>

    {{-- Lookup modal logic --}}
    <script>
        (() => {
            const modal = document.getElementById('lookupModal');
            const titleEl = document.getElementById('lookupModalTitle');
            const nameEl = document.getElementById('lookupName');
            const errEl = document.getElementById('lookupError');
            const saveBtn = document.getElementById('lookupSaveBtn');

            let current = {
                type: null,
                select: null
            };

            function openModal(type, selectSelector) {
                current.type = type;
                current.select = document.querySelector(selectSelector);
                titleEl.textContent = `Add ${type}`;
                errEl.classList.add('hidden');
                errEl.textContent = '';
                nameEl.value = '';
                modal.showModal();
                setTimeout(() => nameEl.focus(), 50);
            }

            function upsertNativeOption(selectEl, value, label) {
                let opt = selectEl.querySelector(`option[value="${CSS.escape(value)}"]`);
                if (!opt) {
                    opt = new Option(label, value, true, true);
                    selectEl.add(opt);
                } else {
                    opt.text = label;
                    opt.selected = true;
                }
                selectEl.dispatchEvent(new Event('input', { bubbles: true }));
                selectEl.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function syncChoices(selectEl, value, label) {
                if (!window.__choicesInstances) return false;
                const instance = window.__choicesInstances.find(c => {
                    try { return c.passedElement.element === selectEl; } catch { return false; }
                });
                if (!instance) return false;
                instance.setChoices(
                    [{ value: String(value), label: label, selected: true }],
                    'value', 'label', false
                );
                instance.setChoiceByValue(String(value));
                return true;
            }

            async function saveLookup() {
                const name = (nameEl.value || '').trim();
                if (!name) {
                    errEl.textContent = 'Name is required.';
                    errEl.classList.remove('hidden');
                    return;
                }
                if (!current.select) {
                    errEl.textContent = 'Select not found on page.';
                    errEl.classList.remove('hidden');
                    return;
                }

                saveBtn.disabled = true;

                try {
                    const url = `{{ route('admin.lookups.ajax.store', ['type' => '___']) }}`.replace('___', current.type);
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ name }),
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        errEl.textContent = data?.message || data?.errors?.name?.[0] || 'Failed to add.';
                        errEl.classList.remove('hidden');
                        return;
                    }

                    const value = String(data.id);
                    const label = data.name;

                    const handledByChoices = syncChoices(current.select, value, label);
                    if (!handledByChoices) {
                        upsertNativeOption(current.select, value, label);
                    }

                    modal.close();
                    current.select.focus();

                } catch (e) {
                    errEl.textContent = 'Network error.';
                    errEl.classList.remove('hidden');
                } finally {
                    saveBtn.disabled = false;
                }
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-lookup-add]');
                if (!btn) return;
                openModal(btn.dataset.type, btn.dataset.select);
            });

            saveBtn.addEventListener('click', saveLookup);

            nameEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveLookup();
                }
            });
        })();
    </script>
</x-layout>
