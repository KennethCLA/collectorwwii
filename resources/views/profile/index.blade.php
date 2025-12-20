<!-- resources/views/profile/index.blade.php -->
<x-layout>
    <div class="container">
        <div class="container flex justify-between">
            <div class="breadcrumbs flex items-center pl-4 space-x-2 my-auto">
                <a href="{{ route('home') }}" class="pr-2">Home</a> /
                <a href="{{ route('profile.index') }}">Profile</a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="flex gap-4">
            <!-- aside met flex-shrink-0 voor automatische hoogte -->
            <aside class="flex-shrink-0 bg-[#697367] text-white p-4 rounded-md w-full md:w-auto h-full">
                <ul>
                    <h3 class="text-lg font-bold mb-2">Creation</h3>
                    <ul class="pl-4 pb-4">
                        <li>
                            <a href="{{ route('books.create') }}" class="text-white hover:text-gray-300">Create
                                Book</a>
                        </li>

                        <li>
                            <a href="{{ route('items.create') }}" class="text-white hover:text-gray-300">Create
                                Item</a>
                        </li>

                        <li>
                            <a href="{{ route('newspapers.create') }}" class="text-white hover:text-gray-300">Create
                                Newspaper</a>
                        </li>

                        <li>
                            <a href="{{ route('magazines.create') }}" class="text-white hover:text-gray-300">Create
                                Magazine</a>
                        </li>

                        <li>
                            <a href="{{ route('banknotes.create') }}" class="text-white hover:text-gray-300">Create
                                Banknote</a>
                        </li>

                        <li>
                            <a href="{{ route('coins.create') }}" class="text-white hover:text-gray-300">Create
                                Coin</a>
                        </li>

                        <li>
                            <a href="{{ route('postcards.create') }}" class="text-white hover:text-gray-300">Create
                                Postcard</a>
                        </li>

                        <li>
                            <a href="{{ route('stamps.create') }}" class="text-white hover:text-gray-300">Create
                                Stamp</a>
                        </li>
                    </ul>

                    <h3 class="text-lg font-bold mb-2">Editing</h3>
                    <ul class="pl-4">
                        <li>
                            <a href="{{ route('books.edit', $book->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Book</a>
                        </li>

                        <li>
                            <a href="{{ route('items.edit', $item->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Item</a>
                        </li>

                        <li>
                            <a href="{{ route('newspapers.edit', $newspaper->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Newspaper</a>
                        </li>

                        <li>
                            <a href="{{ route('magazines.edit', $magazine->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Magazine</a>
                        </li>

                        <li>
                            <a href="{{ route('banknotes.edit', $banknote->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Banknote</a>
                        </li>

                        <li>
                            <a href="{{ route('coins.edit', $coin->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Coin</a>
                        </li>

                        <li>
                            <a href="{{ route('postcards.edit', $postcard->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Postcard</a>
                        </li>

                        <li>
                            <a href="{{ route('stamps.edit', $stamp->id) }}"
                                class="text-white hover:text-gray-300">Edit
                                Stamp</a>
                        </li>
                    </ul>
                </ul>
            </aside>

            <main class="flex-grow mx-auto">
                <h1 class="text-xl w-full mx-auto text-center font-bold mb-4 text-white">Hello {{ $user->name }}
                </h1>

                <div class="mb-4">
                    <img src="{{ asset('storage/images/hitlers-gustav-gun.jpg') }}" alt="Hitler's Gustav Gun"
                        class="w-full h-[460px] object-cover rounded-md">
                </div>
        </div>
    </div>
</x-layout>