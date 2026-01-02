{{-- resources/views/admin/books/create.blade.php --}}

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

                <div id="attachments-preview"
                    class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                </div>

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