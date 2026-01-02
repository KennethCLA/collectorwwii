{{-- resources/views/admin/books/_media.blade.php --}}

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
                class="p-2 border border-gray-900 rounded-md bg-[#565e55] text-white">

            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
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
                class="p-2 border border-gray-900 rounded-md bg-[#565e55] text-white">

            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Upload PDFs
            </button>
        </form>
    </div>
</div>

<p class="text-xs text-white/70 mt-2">
    Max 50MB per file. Supported image formats + PDF.
</p>

@php
$images = $book->images()->get();
$pdfs = $book->files()->get()->filter(fn ($f) => $f->isPdf())->values();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- IMAGES --}}
    <div class="bg-[#697367] rounded-md p-4">
        <h3 class="text-lg font-semibold text-white mb-3">Images ({{ $images->count() }})</h3>

        @if($images->isEmpty())
        <p class="text-white/80 text-sm">No images uploaded yet.</p>
        @else
        <div class="flex flex-wrap gap-2 items-start">
            @foreach($images as $img)
            @include('admin.books._image-card', ['img' => $img])
            @endforeach
        </div>
        @endif
    </div>

    {{-- PDFs --}}
    <div class="bg-[#697367] rounded-md p-4">
        <h3 class="text-lg font-semibold text-white mb-3">PDFs ({{ $pdfs->count() }})</h3>

        @if($pdfs->isEmpty())
        <p class="text-white/80 text-sm">No PDFs uploaded yet.</p>
        @else
        <div class="space-y-4">
            @foreach($pdfs as $pdf)
            @include('admin.books._pdf-card', ['pdf' => $pdf])
            @endforeach
        </div>
        @endif
    </div>
</div>