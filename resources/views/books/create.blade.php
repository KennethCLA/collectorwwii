<x-layout>
    <x-slot:content>
        <x-form-layout>
            <form id="book-form" action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data"
                class="w-full mx-auto max-w-7xl">
                @csrf
                <div class="space-y-4">
                    @if (!empty($isbn) && ($isbnLookupFailed ?? false))
                    <div class="mb-4 rounded-md bg-red-600/20 border border-red-600 text-white p-3">
                        No book found for ISBN <strong>{{ $isbn }}</strong>. Please check the number or fill the form manually.
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-600/20 border border-red-600 text-white p-3">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <!-- ISBN -->
                    <div class="flex items-center space-x-4">
                        <label for="isbn" class="w-32 text-sm font-medium text-gray-700">
                            ISBN
                        </label>
                        <input type="text" id="isbn" name="isbn"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('isbn', $isbn ?? '') }}"
                            required />
                        <button type="button" id="search-isbn"
                            class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Search ISBN
                        </button>
                    </div>

                    <!-- Title -->
                    <div class="flex items-center space-x-4">
                        <label for="title" class="w-32 text-sm font-medium text-gray-700">
                            Title
                        </label>
                        <input type="text" id="title" name="title"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('title', $bookData['title'] ?? '') }}"
                            required />
                    </div>

                    <!-- Subtitle -->
                    <div class="flex items-center space-x-4">
                        <label for="subtitle" class="w-32 text-sm font-medium text-gray-700">
                            Subtitle
                        </label>
                        <input type="text" id="subtitle" name="subtitle"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('subtitle', $bookData['subtitle'] ?? '') }}" />
                    </div>

                    <!-- Authors -->
                    <div class="flex items-center space-x-4">
                        <label for="authors" class="w-32 text-sm font-medium text-gray-700">
                            Authors
                        </label>
                        <input type="text" id="authors" name="authors"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('authors', $bookData['authors'] ?? '') }}"
                            required />
                    </div>

                    <!-- Topic -->
                    <div class="flex items-center space-x-4">
                        <label for="topic_id" class="w-32 text-sm font-medium text-gray-700">
                            Topic
                        </label>
                        <select id="topic_id" name="topic_id"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                            <option value="">Select a topic</option>
                            @foreach ($topics as $topic)
                            <option value="{{ $topic->id }}"
                                {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" data-add-option="/topics/ajax/store" data-select-id="topic_id"
                            class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Add Topic
                        </button>
                    </div>

                    <!-- Publisher -->
                    <div class="flex items-center space-x-4">
                        <label for="publisher_name" class="w-32 text-sm font-medium text-gray-700">
                            Publisher
                        </label>
                        <input type="text" id="publisher_name" name="publisher_name"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('publisher_name', $bookData['publisher_name'] ?? '') }}" />
                    </div>

                    <!-- Copyright Year -->
                    <div class="flex items-center space-x-4">
                        <label for="copyright_year" class="w-32 text-sm font-medium text-gray-700">
                            Copyright Year
                        </label>
                        <input type="number" id="copyright_year" name="copyright_year"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('copyright_year', $bookData['copyright_year'] ?? '') }}">
                    </div>

                    <!-- Translator -->
                    <div class="flex items-center space-x-4">
                        <label for="translator" class="w-32 text-sm font-medium text-gray-700">Translator</label>
                        <input type="text" id="translator" name="translator"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('translator') }}">
                    </div>

                    <!-- Issue Number -->
                    <div class="flex items-center space-x-4">
                        <label for="issue_number" class="w-32 text-sm font-medium text-gray-700">Issue Number</label>
                        <input type="text" id="issue_number" name="issue_number"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('issue_number') }}">
                    </div>

                    <!-- Issue Year -->
                    <div class="flex items-center space-x-4">
                        <label for="issue_year" class="w-32 text-sm font-medium text-gray-700">
                            Issue Year
                        </label>
                        <input type="number" id="issue_year" name="issue_year"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('issue_year') }}">
                    </div>

                    <!-- Series -->
                    <div class="flex items-center space-x-4">
                        <label for="series_id" class="w-32 text-sm font-medium text-gray-700">Series</label>
                        <select id="series_id" name="series_id"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                            <option value="">Select a series</option>
                            @foreach ($series as $serie)
                            <option value="{{ $serie->id }}">{{ $serie->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" data-add-option="/series/ajax/store" data-select-id="series_id"
                            class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">Add
                            Series</button>
                    </div>

                    <!-- Series Number -->
                    <div class="flex items-center space-x-4">
                        <label for="series_number" class="w-32 text-sm font-medium text-gray-700">Series
                            Number</label>
                        <input type="text" id="series_number" name="series_number"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                    </div>

                    <!-- Cover -->
                    <div class="flex items-center space-x-4">
                        <label for="cover_id" class="w-32 text-sm font-medium text-gray-700">Cover</label>
                        <select id="cover_id" name="cover_id"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                            <option value="">Select a cover</option>
                            @foreach ($covers as $cover)
                            <option value="{{ $cover->id }}">{{ $cover->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" data-add-option="/covers/ajax/store" data-select-id="cover_id"
                            class="w-32 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Add Cover
                        </button>
                    </div>

                    <!-- Pages -->
                    <div class="flex items-center space-x-4">
                        <label for="pages" class="w-32 text-sm font-medium text-gray-700">Pages</label>
                        <input type="number" id="pages" name="pages"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('pages', $bookData['pages'] ?? '') }}">
                    </div>

                    <!-- Title (First Edition) -->
                    <div class="flex items-center space-x-4">
                        <label for="title_first_edition" class="w-32 text-sm font-medium text-gray-700">Title
                            (First Edition)</label>
                        <input type="text" id="title_first_edition" name="title_first_edition"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('title_first_edition') }}">
                    </div>

                    <!-- Subtitle (First Edition) -->
                    <div class="flex items-center space-x-4">
                        <label for="subtitle_first_edition" class="w-32 text-sm font-medium text-gray-700">Subtitle
                            (First Edition)</label>
                        <input type="text" id="subtitle_first_edition" name="subtitle_first_edition"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('subtitle_first_edition') }}">
                    </div>

                    <!-- Publisher (First Issue) -->
                    <div class="flex items-center space-x-4">
                        <label for="publisher_first_issue" class="w-32 text-sm font-medium text-gray-700">Publisher
                            (First Issue)</label>
                        <input type="text" id="publisher_first_issue" name="publisher_first_issue"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('publisher_first_issue') }}">
                    </div>

                    <!-- Copyright Year (First Edition) -->
                    <div class="flex items-center space-x-4">
                        <label for="copyright_year_first_edition"
                            class="w-32 text-sm font-medium text-gray-700">Copyright Year (First Edition)</label>
                        <input type="number" id="copyright_year_first_edition" name="copyright_year_first_edition"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('copyright_year_first_edition') }}">
                    </div>

                    <!-- Purchase Date -->
                    <div class="flex items-center space-x-4">
                        <label for="purchase-date" class="w-32 text-sm font-medium text-gray-700">Purchase
                            Date</label>
                        <input type="date" id="purchase-date" name="purchase_date"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('purchase_date') }}">
                    </div>

                    <!-- Purchase Price -->
                    <div class="flex items-center space-x-4">
                        <label for="purchase-price" class="w-32 text-sm font-medium text-gray-700">Purchase
                            Price</label>
                        <input type="number" step="0.01" id="purchase-price" name="purchase_price"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('purchase_price') }}">
                    </div>

                    <!-- Description -->
                    <div class="flex items-center space-x-4">
                        <label for="book-description"
                            class="w-32 text-sm font-medium text-gray-700">Description</label>
                        <textarea id="book-description" name="description" rows="4"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim(old('description', $bookData['description'] ?? '')) }}
                        </textarea>
                    </div>

                    <!-- Notes -->
                    <div class="flex items-center space-x-4">
                        <label for="notes" class="w-32 text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" name="notes" rows="4"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim(old('notes')) }}
                        </textarea>
                    </div>

                    <div
                        x-data="{forSale: {{ old('for_sale') ? 'true' : 'false' }}, sellingPrice: '{{ old('selling_price') }}' }"
                        x-init="$watch('forSale', value => { if (!value) sellingPrice = '' })">

                        <!-- For Sale Checkbox -->
                        <div class="flex items-center space-x-4">
                            <label for="for_sale" class="w-32 text-sm font-medium text-gray-700">For Sale</label>
                            <input
                                type="checkbox"
                                id="for_sale"
                                name="for_sale"
                                x-model="forSale"
                                class="p-2 border border-gray-900 rounded-md bg-[#565e55]" />
                        </div>

                        <!-- Selling Price -->
                        <div x-show="forSale" x-cloak class="flex items-center space-x-4 mt-4">
                            <label for="selling-price" class="w-32 text-sm font-medium text-gray-700">Selling Price</label>
                            <input
                                type="number"
                                step="0.01"
                                id="selling-price"
                                name="selling_price"
                                x-model="sellingPrice"
                                class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]" />
                        </div>
                    </div>

                    <!-- Weight -->
                    <div class="flex items-center space-x-4">
                        <label for="weight" class="w-32 text-sm font-medium text-gray-700">Weight</label>
                        <input type="number" id="weight" name="weight"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('weight') }}">
                    </div>

                    <!-- Dimensions -->
                    <div class="flex items-center space-x-4">
                        <label for="dimensions" class="w-32 text-sm font-medium text-gray-700">Dimensions (Width x
                            Height
                            x Thickness)</label>
                        <input type="text" id="dimensions" name="dimensions"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]"
                            value="{{ old('dimensions') }}">
                    </div>

                    <!-- Location -->
                    <div class="flex items-center space-x-4">
                        <label for="location_id" class="w-32 text-sm font-medium text-gray-700">Location</label>
                        <select id="location_id" name="location_id"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="add-location" data-add-option="/add-location"
                            data-select-id="location_id"
                            class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 hover:text-gray-300">
                            Add Location
                        </button>
                    </div>

                    <!-- Location Details -->
                    <div class="flex items-center space-x-4">
                        <label for="location_detail" class="w-32 text-sm font-medium text-gray-700">Location
                            Details</label>
                        <textarea id="location_detail" name="location_detail" rows="4"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">{{ trim(old('location_detail')) }}</textarea>
                    </div>

                    <!-- Attachments -->
                    <div class="flex items-center space-x-4">
                        <label for="attachments" class="w-32 text-sm font-medium text-gray-700">Attachments</label>
                        <input type="file" id="attachments" name="attachments[]" multiple
                            accept="image/*,application/pdf"
                            class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55]">
                    </div>
                    <ul id="file-list" class="mt-2 text-sm text-gray-300"></ul>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500">
                            Save Book
                        </button>
                    </div>
                </div>

                <!-- Modal Template -->
                <div id="modal-container"
                    class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
                    <div class="bg-white p-6 rounded-md shadow-md w-96">
                        <h2 id="modal-title" class="text-lg font-semibold mb-4">Add Item</h2>
                        <label for="modal-name-input" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="modal-name-input" name="name"
                            class="mt-1 p-2 w-full border border-gray-300 rounded-md">
                        <div class="flex justify-end mt-4 space-x-2">
                            <button id="modal-cancel-button"
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                            <button id="modal-save-button"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-form-layout>
    </x-slot:content>
</x-layout>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const isbnInput = document.getElementById("isbn");
        const searchIsbnButton = document.getElementById("search-isbn");
        const bookForm = document.getElementById("book-form");
        const fileInput = document.getElementById("attachments");
        const fileList = document.getElementById("file-list");

        searchIsbnButton.addEventListener("click", searchIsbn);

        function searchIsbn() {
            const isbn = isbnInput.value.trim();

            if (!isbn) {
                alert("Please enter a valid ISBN.");
                return;
            }

            // Redirect naar server-side lookup
            window.location.href = `{{ route('books.create') }}?isbn=${encodeURIComponent(isbn)}`;
        }

        fileInput.addEventListener("change", (event) => {
            fileList.innerHTML = "";
            Array.from(event.target.files).forEach((file, index) => {
                const listItem = document.createElement("li");
                listItem.textContent = file.name;
                const removeBtn = document.createElement("button");
                removeBtn.textContent = "❌";
                removeBtn.classList.add("ml-2", "text-red-500", "cursor-pointer");
                removeBtn.onclick = () => {
                    const dt = new DataTransfer();
                    Array.from(event.target.files)
                        .filter((_, i) => i !== index)
                        .forEach((f) => dt.items.add(f));
                    event.target.files = dt.files;
                    listItem.remove();
                };
                listItem.appendChild(removeBtn);
                fileList.appendChild(listItem);
            });
        });

        const addOptionButtons = document.querySelectorAll("[data-add-option]");

        addOptionButtons.forEach((button) => {
            button.addEventListener("click", async () => {
                const inputValue = prompt("Add a new option:");
                if (!inputValue) return;

                const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenElement) {
                    alert("CSRF token not found.");
                    return;
                }

                button.disabled = true;
                const originalText = button.textContent;
                button.textContent = "Adding...";

                try {
                    const response = await fetch(button.dataset.addOption, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfTokenElement.content
                        },
                        body: JSON.stringify({
                            name: inputValue
                        })
                    });

                    const rawText = await response.text();

                    if (!response.ok) {
                        alert("Server error: " + rawText);
                        return;
                    }

                    let data;
                    try {
                        data = JSON.parse(rawText);
                    } catch (e) {
                        alert("Error processing server response. Please try again.");
                        return;
                    }

                    const selectElement = document.getElementById(button.dataset.selectId);
                    if (selectElement && data && data.id && data.name) {
                        const newOption = document.createElement("option");
                        newOption.value = data.id;
                        newOption.textContent = data.name;
                        selectElement.appendChild(newOption);
                        selectElement.value = data.id;

                        ["change", "input"].forEach(eventName => {
                            const event = new Event(eventName, {
                                bubbles: true,
                                cancelable: true
                            });
                            selectElement.dispatchEvent(event);
                        });

                        alert(`Option added: ${data.name}`);
                    } else {
                        alert("Failed to add option.");
                    }
                } catch (error) {
                    alert("There was an error adding the option: " + (error?.message || error));
                } finally {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });
    });
</script>