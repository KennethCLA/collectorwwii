<x-layout :mainClass="'flex items-center justify-center'" :bodyClass="'bg-[#565e55]'">
    <x-form-layout>
        <form action="{{ route('contact') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name"
                    class="mt-1 p-2 w-full border border-gray-900 rounded-md bg-[#565e55]" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email"
                    class="mt-1 p-2 w-full border border-gray-900 rounded-md bg-[#565e55]" required>
            </div>

            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" name="message" rows="4"
                    class="mt-1 p-2 w-full border border-gray-900 rounded-md bg-[#565e55]" required></textarea>
            </div>

            <button type="submit"
                class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 hover:text-gray-300">
                Send Message
            </button>
        </form>
    </x-form-layout>
</x-layout>