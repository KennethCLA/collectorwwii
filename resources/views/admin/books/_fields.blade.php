{{-- resources/views/admin/books/_fields.blade.php --}}

@php
// Always defined
$book = $book ?? null;
$bookData = $bookData ?? []; // ISBN lookup / prefill on create

$isEdit = (bool) ($book?->exists);

/**
* Central value resolver:
* old() > book (edit) > bookData (create)
*/
$val = function (string $key, $fallback = '') use ($book, $bookData) {
return old($key, data_get($book, $key, data_get($bookData, $key, $fallback)));
};

/**
* Date resolver for <input type="date">:
* expects Y-m-d
*/
$dateVal = function (string $key) use ($val) {
$v = $val($key);

if (empty($v)) return '';

// If already Y-m-d, keep it
if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;

try {
return \Illuminate\Support\Carbon::parse($v)->format('Y-m-d');
} catch (\Throwable $e) {
return '';
}
};

/**
* Authors field:
* - Edit: use relation names
* - Create: use $bookData['authors'] if present
*/
$authorsVal = function () use ($book, $bookData) {
$old = old('authors');
if ($old !== null) return $old;

if ($book?->exists) {
return $book->authors?->pluck('name')->implode(', ') ?? '';
}

return data_get($bookData, 'authors', '');
};

// For sale checkbox + selling price
$forSaleChecked = (bool) old('for_sale', (bool) ($book->for_sale ?? false));
$sellingPriceVal = old('selling_price', $book->selling_price ?? '');
@endphp

{{-- ISBN --}}
<div class="flex items-center space-x-4">
    <label for="isbn" class="w-32 text-sm font-medium text-gray-300">ISBN</label>

    <input
        type="text"
        id="isbn"
        name="isbn"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('isbn') }}"
        {{ $isEdit ? 'readonly' : '' }}
        required />

    @unless($isEdit)
    <button type="button" id="search-isbn"
        class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
        Search ISBN
    </button>
    @endunless
</div>

{{-- Title --}}
<div class="flex items-center space-x-4">
    <label for="title" class="w-32 text-sm font-medium text-gray-700">Title</label>
    <input
        type="text"
        id="title"
        name="title"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('title') }}"
        required />
</div>

{{-- Subtitle --}}
<div class="flex items-center space-x-4">
    <label for="subtitle" class="w-32 text-sm font-medium text-gray-700">Subtitle</label>
    <input
        type="text"
        id="subtitle"
        name="subtitle"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('subtitle') }}" />
</div>

{{-- Authors --}}
<div class="flex items-center space-x-4">
    <label for="authors" class="w-32 text-sm font-medium text-gray-700">Author(s)</label>
    <input
        type="text"
        id="authors"
        name="authors"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $authorsVal() }}"
        required />
</div>

{{-- Topic --}}
<div class="flex items-center space-x-4">
    <label for="topic_id" class="w-32 text-sm font-medium text-gray-700">Topic</label>

    <select id="topic_id" name="topic_id"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
        <option value="">Select a topic</option>
        @foreach ($topics as $topic)
        <option value="{{ $topic->id }}"
            {{ (string)$val('topic_id') === (string)$topic->id ? 'selected' : '' }}>
            {{ $topic->name }}
        </option>
        @endforeach
    </select>

    <button type="button" data-add-option="/topics/ajax/store" data-select-id="topic_id"
        class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
        Add Topic
    </button>
</div>

{{-- Publisher --}}
<div class="flex items-center space-x-4">
    <label for="publisher_name" class="w-32 text-sm font-medium text-gray-700">Publisher</label>
    <input
        type="text"
        id="publisher_name"
        name="publisher_name"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('publisher_name') }}" />
</div>

{{-- Copyright Year --}}
<div class="flex items-center space-x-4">
    <label for="copyright_year" class="w-32 text-sm font-medium text-gray-700">Copyright Year</label>
    <input
        type="number"
        id="copyright_year"
        name="copyright_year"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('copyright_year') }}" />
</div>

{{-- Translator --}}
<div class="flex items-center space-x-4">
    <label for="translator" class="w-32 text-sm font-medium text-gray-700">Translator</label>
    <input
        type="text"
        id="translator"
        name="translator"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('translator') }}" />
</div>

{{-- Issue Number --}}
<div class="flex items-center space-x-4">
    <label for="issue_number" class="w-32 text-sm font-medium text-gray-700">Issue Number</label>
    <input
        type="text"
        id="issue_number"
        name="issue_number"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('issue_number') }}" />
</div>

{{-- Issue Year --}}
<div class="flex items-center space-x-4">
    <label for="issue_year" class="w-32 text-sm font-medium text-gray-700">Issue Year</label>
    <input
        type="number"
        id="issue_year"
        name="issue_year"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('issue_year') }}" />
</div>

{{-- Series --}}
<div class="flex items-center space-x-4">
    <label for="series_id" class="w-32 text-sm font-medium text-gray-700">Series</label>

    <select id="series_id" name="series_id"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
        <option value="">Select a series</option>
        @foreach ($series as $serie)
        <option value="{{ $serie->id }}"
            {{ (string)$val('series_id') === (string)$serie->id ? 'selected' : '' }}>
            {{ $serie->name }}
        </option>
        @endforeach
    </select>

    <button type="button" data-add-option="/series/ajax/store" data-select-id="series_id"
        class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
        Add Series
    </button>
</div>

{{-- Series Number --}}
<div class="flex items-center space-x-4">
    <label for="series_number" class="w-32 text-sm font-medium text-gray-700">Series Number</label>
    <input
        type="text"
        id="series_number"
        name="series_number"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('series_number') }}" />
</div>

{{-- Cover --}}
<div class="flex items-center space-x-4">
    <label for="cover_id" class="w-32 text-sm font-medium text-gray-700">Cover</label>

    <select id="cover_id" name="cover_id"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
        <option value="">Select a cover</option>
        @foreach ($covers as $cover)
        <option value="{{ $cover->id }}"
            {{ (string)$val('cover_id') === (string)$cover->id ? 'selected' : '' }}>
            {{ $cover->name }}
        </option>
        @endforeach
    </select>

    <button type="button" data-add-option="/covers/ajax/store" data-select-id="cover_id"
        class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
        Add Cover
    </button>
</div>

{{-- Pages --}}
<div class="flex items-center space-x-4">
    <label for="pages" class="w-32 text-sm font-medium text-gray-700">Pages</label>
    <input
        type="number"
        id="pages"
        name="pages"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('pages') }}" />
</div>

{{-- Title (First Edition) --}}
<div class="flex items-center space-x-4">
    <label for="title_first_edition" class="w-32 text-sm font-medium text-gray-700">Title (First Edition)</label>
    <input
        type="text"
        id="title_first_edition"
        name="title_first_edition"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('title_first_edition') }}" />
</div>

{{-- Subtitle (First Edition) --}}
<div class="flex items-center space-x-4">
    <label for="subtitle_first_edition" class="w-32 text-sm font-medium text-gray-700">Subtitle (First Edition)</label>
    <input
        type="text"
        id="subtitle_first_edition"
        name="subtitle_first_edition"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('subtitle_first_edition') }}" />
</div>

{{-- Publisher (First Issue) --}}
<div class="flex items-center space-x-4">
    <label for="publisher_first_issue" class="w-32 text-sm font-medium text-gray-700">Publisher (First Issue)</label>
    <input
        type="text"
        id="publisher_first_issue"
        name="publisher_first_issue"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('publisher_first_issue') }}" />
</div>

{{-- Copyright Year (First Edition) --}}
<div class="flex items-center space-x-4">
    <label for="copyright_year_first_edition" class="w-32 text-sm font-medium text-gray-700">
        Copyright Year (First Edition)
    </label>
    <input
        type="number"
        id="copyright_year_first_edition"
        name="copyright_year_first_edition"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('copyright_year_first_edition') }}" />
</div>

{{-- Purchase Date --}}
<div class="flex items-center space-x-4">
    <label for="purchase_date" class="w-32 text-sm font-medium text-gray-700">Purchase Date</label>
    <input
        type="date"
        id="purchase_date"
        name="purchase_date"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $dateVal('purchase_date') }}" />
</div>

{{-- Purchase Price --}}
<div class="flex items-center space-x-4">
    <label for="purchase_price" class="w-32 text-sm font-medium text-gray-700">Purchase Price</label>
    <input
        type="number"
        step="0.01"
        id="purchase_price"
        name="purchase_price"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('purchase_price') }}" />
</div>

{{-- Description --}}
<div class="flex items-center space-x-4">
    <label for="description" class="w-32 text-sm font-medium text-gray-700">Description</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim($val('description')) }}</textarea>
</div>

{{-- Notes --}}
<div class="flex items-center space-x-4">
    <label for="notes" class="w-32 text-sm font-medium text-gray-700">Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="4"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim($val('notes')) }}</textarea>
</div>

{{-- For Sale + Selling Price --}}
<div
    x-data="{ forSale: {{ $forSaleChecked ? 'true' : 'false' }}, sellingPrice: '{{ $sellingPriceVal }}' }"
    x-init="$watch('forSale', v => { if (!v) sellingPrice = '' })">
    <div class="flex items-center space-x-4">
        <label for="for_sale" class="w-32 text-sm font-medium text-gray-700">For Sale</label>
        <input
            type="checkbox"
            id="for_sale"
            name="for_sale"
            x-model="forSale"
            class="p-2 border border-gray-900 rounded-md bg-[#565e55]"
            {{ $forSaleChecked ? 'checked' : '' }} />
    </div>

    <div x-show="forSale" x-cloak class="flex items-center space-x-4 mt-4">
        <label for="selling_price" class="w-32 text-sm font-medium text-gray-700">Selling Price</label>
        <input
            type="number"
            step="0.01"
            id="selling_price"
            name="selling_price"
            x-model="sellingPrice"
            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]" />
    </div>
</div>

{{-- Weight --}}
<div class="flex items-center space-x-4">
    <label for="weight" class="w-32 text-sm font-medium text-gray-700">Weight</label>
    <input
        type="number"
        id="weight"
        name="weight"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('weight') }}" />
</div>

{{-- Dimensions --}}
<div class="flex items-center space-x-4">
    <label for="dimensions" class="w-32 text-sm font-medium text-gray-700">Dimensions (W x H x T)</label>
    <input
        type="text"
        id="dimensions"
        name="dimensions"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
        value="{{ $val('dimensions') }}" />
</div>

{{-- Location --}}
<div class="flex items-center space-x-4">
    <label for="location_id" class="w-32 text-sm font-medium text-gray-700">Location</label>

    <select id="location_id" name="location_id"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
        <option value="">Select a location</option>
        @foreach ($locations as $location)
        <option value="{{ $location->id }}"
            {{ (string)$val('location_id') === (string)$location->id ? 'selected' : '' }}>
            {{ $location->name }}
        </option>
        @endforeach
    </select>

    <button type="button" id="add-location" data-add-option="/add-location" data-select-id="location_id"
        class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 hover:text-gray-300">
        Add Location
    </button>
</div>

{{-- Location Details --}}
<div class="flex items-center space-x-4">
    <label for="location_detail" class="w-32 text-sm font-medium text-gray-700">Location Details</label>
    <textarea
        id="location_detail"
        name="location_detail"
        rows="4"
        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim($val('location_detail')) }}</textarea>
</div>