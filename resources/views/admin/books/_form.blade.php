{{-- resources/views/admin/books/_form.blade.php --}}

@php
// $book kan null zijn bij create
$isEdit = isset($book) && $book?->exists;
@endphp

<div class="space-y-4">

    {{-- ROW helper pattern: label | input | actions --}}
    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">ISBN</div>

        <div class="min-w-0">
            <input
                name="isbn"
                value="{{ old('isbn', $book->isbn ?? '') }}"
                required
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>

        {{-- Actie rechts (zoals create) --}}
        <div class="flex md:justify-end">
            {{-- voorbeeld: in edit kan je dit weglaten of later toevoegen --}}
            <button type="button"
                class="h-10 px-4 rounded-md bg-gray-900 text-white hover:bg-gray-700">
                Search ISBN
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Title</div>
        <div>
            <input
                name="title"
                value="{{ old('title', $book->title ?? '') }}"
                required
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>
        <div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Subtitle</div>
        <div>
            <input
                name="subtitle"
                value="{{ old('subtitle', $book->subtitle ?? '') }}"
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>
        <div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Author(s)</div>
        <div>
            <input
                name="authors"
                value="{{ old('authors', isset($book) ? $book->authors->implode(', ') : '') }}"
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>
        <div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Topic</div>

        <div>
            <select
                name="topic_id"
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2">
                <option value="">Select a topic</option>
                @foreach($topics as $t)
                <option value="{{ $t->id }}"
                    @selected(old('topic_id', $book->topic_id ?? null) == $t->id)>
                    {{ $t->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex md:justify-end">
            <button type="button"
                class="h-10 px-4 rounded-md bg-gray-900 text-white hover:bg-gray-700">
                Add Topic
            </button>
        </div>
    </div>

    {{-- ... herhaal dit patroon voor Series/Cover/Pages/etc ... --}}

    <hr class="my-6 border-white/10">

    <h2 class="text-xl font-bold text-white">Admin Details</h2>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Purchase Price (€)</div>
        <div>
            <input
                name="purchase_price"
                type="number"
                step="0.01"
                value="{{ old('purchase_price', $book->purchase_price ?? '') }}"
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>
        <div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-2 md:gap-4 items-center">
        <div class="text-white/80 text-sm">Storage Location</div>
        <div>
            <input
                name="storage_location"
                value="{{ old('storage_location', $book->storage_location ?? '') }}"
                class="w-full rounded-md bg-[#565e55] text-white border border-black/40 px-3 py-2" />
        </div>
        <div></div>
    </div>

</div>