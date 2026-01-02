{{-- resources/views/items/edit.blade.php --}}

<x-layout>
    {{-- Breadcrumbs --}}
    <nav class="breadcrumbs flex items-center mb-4 space-x-2 my-auto text-sm text-white">
        <a href="{{ route('home') }}" class="hover:underline">Home</a>
        <div>></div>
        <a href="{{ route('items.index') }}" class="hover:underline">Items</a>
        <div>></div>
        <a href="{{ route('items.show', $item->id) }}" class="hover:underline">{{ $item->title }}</a>
        <div>></div>
        <span class="text-gray-800">Edit {{ $item->title }}</span>
    </nav>

    <x-form-layout>
        <form action="{{ route('admin.items.update', $item) }}" method="POST" class="w-full mx-auto max-w-7xl">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="flex flex-wrap -mx-4">
                    {{-- LEFT --}}
                    <div class="w-full md:w-1/2 px-4">
                        <x-form.input label="Title" name="title" :value="$item->title" required />
                        <x-form.textarea label="Description" name="description" :value="$item->description" />

                        <x-form.select label="Category" name="category_id"
                            :options="$categories->pluck('name', 'id')"
                            :selected="$item->category_id" />

                        <x-form.select label="Origin" name="origin_id"
                            :options="$origins->pluck('name', 'id')"
                            :selected="$item->origin_id" />
                    </div>

                    {{-- RIGHT --}}
                    <div class="w-full md:w-1/2 px-4">
                        <x-form.select label="Nationality" name="nationality_id"
                            :options="$nationalities->pluck('name', 'id')"
                            :selected="$item->nationality_id" />

                        <x-form.select label="Organization" name="organization_id"
                            :options="$organizations->pluck('name', 'id')"
                            :selected="$item->organization_id" />

                        <x-form.input label="Purchase Date" name="purchase_date" type="date" :value="$item->purchase_date" />
                        <x-form.select label="For Sale" name="for_sale" :options="[1 => 'Yes', 0 => 'No']" :selected="$item->for_sale" />
                    </div>
                </div>
            </div>

            <hr class="my-6">
            <h2 class="text-2xl font-bold mb-4">Admin Details</h2>

            <div class="flex flex-wrap -mx-4">
                <div class="w-full md:w-1/2 px-4">
                    <x-form.input label="Purchase Price (€)" name="purchase_price" type="number" step="0.01"
                        :value="$item->purchase_price" />
                    <x-form.input label="Purchase Location" name="purchase_location" :value="$item->purchase_location" />
                </div>
                <div class="w-full md:w-1/2 px-4">
                    <x-form.input label="Storage Location" name="storage_location" :value="$item->storage_location" />
                    <x-form.input label="Selling Price (€)" name="selling_price" type="number" step="0.01"
                        :value="$item->selling_price" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <x-form.button type="submit" color="blue">Save Changes</x-form.button>
                <x-form.button-link :href="route('admin.items.index')" color="gray">Cancel</x-form.button-link>
            </div>
        </form>

        {{-- FILES (Images / PDFs) --}}
        <hr class="my-8">
        <h2 class="text-2xl font-bold mb-4 text-white">Images & PDFs</h2>

        {{-- Upload new files --}}
        <div class="bg-[#697367] rounded-md p-4 mb-6">
            <h3 class="text-lg font-semibold text-white mb-3">Upload files</h3>

            <form action="{{ route('admin.items.media.store', $item) }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                @csrf

                <input type="file" name="files[]" multiple accept="image/*,application/pdf"
                    class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55] text-white">

                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Upload
                </button>
            </form>

            <p class="text-xs text-white/70 mt-2">Supported: images + PDF. Max 50MB per file.</p>
        </div>

        @php
        $images = $item->images()->get();
        $files = $item->files()->get();
        $pdfs = $files->filter(fn ($f) => $f->isPdf())->values();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- IMAGES --}}
            <div class="bg-[#697367] rounded-md p-4">
                <h3 class="text-lg font-semibold text-white mb-3">Images ({{ $images->count() }})</h3>

                @if($images->count() === 0)
                <p class="text-white/80 text-sm">No images uploaded yet.</p>
                @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($images as $img)
                    @php $url = $img->url(); @endphp

                    <div class="rounded-md bg-[#343933] p-2 border border-white/10">
                        @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="block">
                            <img src="{{ $url }}" alt="image" class="w-full h-32 object-contain rounded">
                        </a>
                        @endif

                        <div class="mt-2 flex items-center justify-between gap-2">
                            @if(!$img->is_main)
                            <form action="{{ route('admin.media.makeMain', $img) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-xs px-2 py-1 rounded bg-white/10 text-white hover:bg-white/15">
                                    Make main
                                </button>
                            </form>
                            @else
                            <span class="text-xs px-2 py-1 rounded bg-white/15 text-white font-semibold">
                                Main
                            </span>
                            @endif

                            <form action="{{ route('admin.media.destroy', $img) }}" method="POST"
                                onsubmit="return confirm('Delete this file?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>

                        <div class="mt-2 text-[11px] text-white/60 break-all">
                            {{ $img->path }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- PDFs --}}
            <div class="bg-[#697367] rounded-md p-4">
                <h3 class="text-lg font-semibold text-white mb-3">PDFs ({{ $pdfs->count() }})</h3>

                @if($pdfs->count() === 0)
                <p class="text-white/80 text-sm">No PDFs uploaded yet.</p>
                @else
                <div class="space-y-3">
                    @foreach($pdfs as $pdf)
                    @php $url = $pdf->url(); @endphp

                    <div class="rounded-md bg-[#343933] p-3 border border-white/10 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-white font-semibold truncate">
                                {{ $pdf->original_name ? pathinfo($pdf->original_name, PATHINFO_FILENAME) : 'PDF' }}
                            </div>
                            <div class="text-[11px] text-white/60 break-all">{{ $pdf->path }}</div>

                            @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                class="text-sm underline text-white/90 hover:text-white">
                                Open
                            </a>
                            @endif
                        </div>

                        <form action="{{ route('admin.media.destroy', $pdf) }}" method="POST"
                            onsubmit="return confirm('Delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </x-form-layout>
</x-layout>