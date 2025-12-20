<x-layout>
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs flex items-center mb-4 space-x-2 my-auto text-sm text-white">
        <a href="{{ route('home') }}" class="hover:underline">Home</a>
        <div>></div>
        <a href="{{ route('books.index') }}" class="hover:underline">Books</a>
        <div>></div>
        <a href="{{ route('books.show', $book->id) }}" class="hover:underline">{{ $book->title }}</a>
        <div>></div>
        <span class="text-gray-800">Edit {{ $book->title }}</span>
    </nav>
    <x-form-layout>
        <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data"
            class="w-full mx-auto max-w-7xl">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="flex flex-wrap -mx-4">
                    <div class="w-full md:w-1/2 px-4">
                        <x-form.input label="ISBN" name="isbn" :value="$book->isbn" required />
                        <x-form.input label="Title" name="title" :value="$book->title" required />
                        <x-form.input label="Subtitle" name="subtitle" :value="$book->subtitle" required />
                        <x-form.input label="Author(s)" name="authors" :value="$book->authors->implode(', ')" />
                        <x-form.input label="Translator" name="translator" :value="$book->translator" />
                        <x-form.input label="Publisher" name="publisher_name" :value="$book->publisher_name" />
                        <x-form.select label="Topic" name="topic_id" :options="$topics->pluck('name', 'id')" :selected="$book->topic_id" />
                        <x-form.input label="Copyright Year" name="copyright_year" type="number"
                            :value="$book->copyright_year" />
                        <x-form.input label="Purchase Date" name="purchase_date" type="date" :value="$book->purchase_date" />
                    </div>
                    <div class="w-full md:w-1/2 px-4">
                        <x-form.select label="Cover" name="cover_id" :options="$covers->pluck('name', 'id')" :selected="$book->cover_id" />
                        <x-form.select label="Series" name="series_id" :options="$series->pluck('name', 'id')" :selected="$book->series_id" />
                        <x-form.input label="Pages" name="pages" type="number" :value="$book->pages" />
                        <x-form.input label="Edition Number" name="issue_number" type="number"
                            :value="$book->issue_number" />
                        <x-form.input label="Edition Year" name="issue_year" type="number" :value="$book->issue_year" />
                        <x-form.input label="Series Number" name="series_number" type="number"
                            :value="$book->series_number" />
                        <x-form.textarea label="Description" name="description" :value="$book->description" />
                        <x-form.select label="For Sale" name="for_sale" :options="[1 => 'Yes', 0 => 'No']" :selected="$book->for_sale" />
                    </div>
                </div>
            </div>

            <hr class="my-6">
            <h2 class="text-2xl font-bold mb-4">Admin Details</h2>
            <div class="flex flex-wrap -mx-4">
                <div class="w-full md:w-1/2 px-4">
                    <x-form.input label="Purchase Price (€)" name="purchase_price" type="number" step="0.01"
                        :value="$book->purchase_price" />
                    <x-form.input label="Storage Location" name="storage_location" :value="$book->storage_location" />
                </div>
                <div class="w-full md:w-1/2 px-4">
                    <x-form.input label="Weight" name="weight" type="text" :value="$book->weight" />
                    <x-form.input label="Dimensions" name="dimensions" type="text" :value="$book->dimensions" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <x-form.button type="submit" color="blue">Save Changes</x-form.button>
                <x-form.button-link :href="route('books.index')" color="gray">Cancel</x-form.button-link>
            </div>
        </form>
    </x-form-layout>
</x-layout>