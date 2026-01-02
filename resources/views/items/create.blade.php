<x-layout>
    <x-form-layout>
        <form id="book-form" action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data"
            class="w-full mx-auto max-w-7xl">
            @csrf

            <div class="space-y-4">
                @include('admin.books._fields', [
                'book' => null,
                'bookData' => $bookData ?? [],
                ])

                <!-- Attachments -->
                <div class="flex items-center space-x-4">
                    <label for="attachments" class="w-32 text-sm font-medium text-gray-700">Attachments</label>
                    <input
                        type="file"
                        id="attachments"
                        name="attachments[]"
                        multiple
                        accept="image/*,application/pdf"
                        class="flex-1 p-2 border border-gray-900 rounded-md bg-[#565e55] text-white">
                </div>

                {{-- Preview grid (thumbnails + PDF tiles) --}}
                <div id="attachments-preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3"></div>

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
</x-layout>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // =========================
        // 1) ISBN SEARCH (create)
        // =========================
        const isbnInput = document.getElementById("isbn");
        const searchIsbnButton = document.getElementById("search-isbn");

        if (isbnInput && searchIsbnButton) {
            searchIsbnButton.addEventListener("click", () => {
                const isbn = isbnInput.value.trim();
                if (!isbn) {
                    alert("Please enter a valid ISBN.");
                    return;
                }
                window.location.href = `{{ route('books.create') }}?isbn=${encodeURIComponent(isbn)}`;
            });
        }

        // =========================
        // 2) ADD OPTION BUTTONS
        // =========================
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
                    if (selectElement && data?.id && data?.name) {
                        const newOption = document.createElement("option");
                        newOption.value = data.id;
                        newOption.textContent = data.name;
                        selectElement.appendChild(newOption);
                        selectElement.value = data.id;

                        ["change", "input"].forEach(eventName => {
                            selectElement.dispatchEvent(new Event(eventName, {
                                bubbles: true,
                                cancelable: true
                            }));
                        });

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

        // =========================
        // 3) ATTACHMENTS PREVIEW
        // =========================
        const input = document.getElementById("attachments");
        const grid = document.getElementById("attachments-preview");
        if (!input || !grid) return;

        let currentFiles = [];
        let objectUrls = [];

        const humanSize = (bytes) => {
            const units = ["B", "KB", "MB", "GB"];
            let i = 0,
                n = bytes;
            while (n >= 1024 && i < units.length - 1) {
                n /= 1024;
                i++;
            }
            return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
        };

        const clearObjectUrls = () => {
            objectUrls.forEach(url => URL.revokeObjectURL(url));
            objectUrls = [];
        };

        const rebuildInputFiles = () => {
            const dt = new DataTransfer();
            currentFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
        };

        const render = () => {
            clearObjectUrls();
            grid.innerHTML = "";

            if (currentFiles.length === 0) return;

            currentFiles.forEach((file, index) => {
                const isImage = file.type?.startsWith("image/");
                const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");

                const card = document.createElement("div");
                card.className = "rounded-md bg-[#343933] border border-white/10 overflow-hidden";

                // Preview
                const preview = document.createElement("div");
                preview.className = "w-full h-28 bg-black/20 flex items-center justify-center overflow-hidden";

                if (isImage) {
                    const url = URL.createObjectURL(file);
                    objectUrls.push(url);

                    const img = document.createElement("img");
                    img.src = url;
                    img.alt = file.name;
                    img.className = "w-full h-full object-cover block";
                    preview.appendChild(img);
                } else if (isPdf) {
                    const tile = document.createElement("div");
                    tile.className = "w-full h-full flex flex-col items-center justify-center text-white/80";
                    tile.innerHTML = `
                    <div class="text-xs font-semibold px-2 py-1 rounded bg-white/10">PDF</div>
                    <div class="mt-2 text-[10px] text-white/50 truncate px-2">${file.name}</div>
                `;
                    preview.appendChild(tile);
                } else {
                    const other = document.createElement("div");
                    other.className = "text-xs text-white/60";
                    other.textContent = "File";
                    preview.appendChild(other);
                }

                // Meta
                const meta = document.createElement("div");
                meta.className = "p-2";

                const name = document.createElement("div");
                name.className = "text-white text-xs font-semibold truncate";
                name.title = file.name;
                name.textContent = file.name;

                const sub = document.createElement("div");
                sub.className = "mt-1 text-[10px] text-white/60 flex items-center justify-between gap-2";
                sub.innerHTML = `
                <span class="truncate">${isImage ? "Image" : (isPdf ? "PDF" : (file.type || "File"))}</span>
                <span class="shrink-0">${humanSize(file.size)}</span>
            `;

                const actions = document.createElement("div");
                actions.className = "mt-2 flex items-center gap-2";

                const openBtn = document.createElement("button");
                openBtn.type = "button";
                openBtn.className = "inline-flex items-center justify-center h-7 px-2 text-[10px] rounded bg-white/10 text-white hover:bg-white/20 transition";
                openBtn.textContent = "Open";
                openBtn.addEventListener("click", () => {
                    const url = URL.createObjectURL(file);
                    window.open(url, "_blank", "noopener");
                    setTimeout(() => URL.revokeObjectURL(url), 30_000);
                });

                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.className = "ml-auto inline-flex items-center justify-center h-7 px-2 text-[10px] rounded bg-red-600 text-white hover:bg-red-700 transition";
                removeBtn.textContent = "Remove";
                removeBtn.addEventListener("click", () => {
                    currentFiles.splice(index, 1);
                    rebuildInputFiles();
                    render();
                });

                actions.appendChild(openBtn);
                actions.appendChild(removeBtn);

                meta.appendChild(name);
                meta.appendChild(sub);
                meta.appendChild(actions);

                card.appendChild(preview);
                card.appendChild(meta);

                grid.appendChild(card);
            });
        };

        input.addEventListener("change", (e) => {
            currentFiles = currentFiles.concat(Array.from(e.target.files || []));
            rebuildInputFiles();
            render();
        });

        // Als je wil: reset previews wanneer form gereset wordt
        const form = document.getElementById("book-form");
        if (form) {
            form.addEventListener("reset", () => {
                currentFiles = [];
                clearObjectUrls();
                grid.innerHTML = "";
            });
        }
    });
</script>
@endpush