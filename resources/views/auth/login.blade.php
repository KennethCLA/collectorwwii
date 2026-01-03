<x-layout :mainClass="'flex items-center justify-center'" :bodyClass="'bg-[#565e55]'">
    <div class="w-full max-w-md rounded-2xl bg-[#2c3335]/75 ring-1 ring-black/40 p-6 text-white">
        <h1 class="text-2xl font-semibold tracking-wide">Admin login</h1>
        <p class="mt-1 text-sm text-white/70">Toegang voor beheer.</p>

        <div class="mt-4 h-px bg-[#c2b280]/30"></div>

        <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm text-white/80 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40
                                   focus:outline-none focus:ring-2 focus:ring-white/30 @error('email') ring-red-400/60 @enderror" />
                @error('email')
                <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm text-white/80 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40
                                   focus:outline-none focus:ring-2 focus:ring-white/30 @error('password') ring-red-400/60 @enderror" />
                @error('password')
                <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-white/80">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                        class="rounded border-black/40 bg-black/25 text-white focus:ring-white/30" />
                    Remember me
                </label>

                @if (Route::has('password.request'))
                <a class="text-sm text-white/70 hover:text-white underline-offset-4 hover:underline"
                    href="{{ route('password.request') }}">
                    Forgot?
                </a>
                @endif
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-black/30 hover:bg-black/40 ring-1 ring-black/40 px-4 py-2 font-medium transition">
                Login
            </button>
        </form>
    </div>
</x-layout>