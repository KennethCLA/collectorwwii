<x-layout :mainClass="'flex items-center justify-center'" :bodyClass="'bg-sage-500'">
    <x-slot:title>Registrierung</x-slot:title>

    <div class="w-full max-w-md">

        {{-- REGISTRIERUNG header --}}
        <div class="text-center mb-5">
            <p class="font-stencil text-xs tracking-[0.4em] text-khaki/60 uppercase mb-1">Neue Zugangsberechtigung</p>
            <h1 class="font-stencil text-3xl font-black tracking-[0.2em] text-white uppercase">REGISTRIERUNG</h1>
            <p class="font-mono text-[10px] tracking-[0.25em] text-white/35 mt-1 uppercase">Personalstammbuch · WK II Sammlung</p>
        </div>

        <div class="rounded-2xl bg-[#2c3335]/75 ring-1 ring-black/40 p-6 text-white noise-texture">
            <div class="h-px bg-khaki/30 mb-5"></div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">DIENSTGRAD / NAME</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('name') ring-red-400/60 @enderror">
                    @error('name')
                    <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">FERNSCHREIBADRESSE / EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('email') ring-red-400/60 @enderror">
                    @error('email')
                    <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">LOSUNGSWORT / PASSWORT</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('password') ring-red-400/60 @enderror">
                    @error('password')
                    <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">BESTÄTIGUNG / WIEDERHOLEN</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30">
                </div>

                <div class="h-px bg-khaki/20"></div>

                <button type="submit"
                    class="w-full rounded-xl bg-black/30 hover:bg-black/40 ring-1 ring-khaki/30 px-4 py-2.5 font-stencil tracking-[0.2em] text-sm text-white uppercase transition">
                    EINTRAGEN &#8212; Register
                </button>
            </form>
        </div>
    </div>
</x-layout>
