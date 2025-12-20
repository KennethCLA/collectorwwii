<x-layout>
    <x-slot:title>Register</x-slot:title>
    <x-form-layout>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" type="text"
                    class="mt-1 p-2 w-full border rounded-md bg-[#565e55] 
                        @error('name') border-red-500 @else border-gray-900 @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email"
                    class="mt-1 p-2 w-full border rounded-md bg-[#565e55] 
                        @error('email') border-red-500 @else border-gray-900 @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password"
                    class="mt-1 p-2 w-full border rounded-md bg-[#565e55] 
                        @error('password') border-red-500 @else border-gray-900 @enderror"
                    name="password" required autocomplete="new-password">
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password-confirm" class="block text-sm font-medium text-gray-700">Confirm
                    Password</label>
                <input id="password-confirm" type="password" class="mt-1 p-2 w-full border rounded-md bg-[#565e55]"
                    name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit"
                class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 hover:text-gray-300">
                Register
            </button>
        </form>
    </x-form-layout>
</x-layout>