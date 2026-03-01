<x-layout :mainClass="'flex items-center justify-center'" :bodyClass="'bg-sage-500'">
    <x-form-layout>
        <div class="w-full max-w-md mx-auto">

            {{-- FUNKMELDUNG header --}}
            <div class="text-center mb-5">
                <p class="font-stencil text-xs tracking-[0.4em] text-khaki/60 uppercase mb-1">Fernschreiben Nr. ——</p>
                <h1 class="font-stencil text-3xl font-black tracking-[0.2em] text-white uppercase">FUNKMELDUNG</h1>
                <p class="font-mono text-[10px] tracking-[0.25em] text-white/35 mt-1 uppercase">Feldpost · WK II Sammlung</p>
            </div>

            <div class="h-px bg-khaki/30 mb-5"></div>

            @if (session('success'))
                <div class="mb-4 rounded-xl bg-green-800/50 ring-1 ring-green-500/30 p-3 text-sm text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-800/50 ring-1 ring-red-500/30 p-3 text-sm text-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">ABSENDER / NAME</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('name') ring-red-400/60 @enderror"
                        placeholder="Your name" required>
                </div>

                <div>
                    <label for="email" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">FUNKADRESSE / EMAIL</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('email') ring-red-400/60 @enderror"
                        placeholder="your@email.com" required>
                </div>

                <div>
                    <label for="message" class="font-mono block text-[10px] tracking-[0.25em] text-khaki/70 uppercase mb-1">MELDUNGSTEXT / MESSAGE</label>
                    <textarea id="message" name="message" rows="5"
                        class="w-full rounded-xl bg-black/25 ring-1 ring-black/40 px-3 py-2 text-white placeholder-white/40 font-mono text-sm
                               focus:outline-none focus:ring-2 focus:ring-white/30 @error('message') ring-red-400/60 @enderror"
                        placeholder="Your message..." required>{{ old('message') }}</textarea>
                </div>

                <div class="h-px bg-khaki/20"></div>

                <button type="submit"
                    class="w-full rounded-xl bg-black/30 hover:bg-black/40 ring-1 ring-khaki/30 px-4 py-2.5 font-stencil tracking-[0.2em] text-sm text-white uppercase transition">
                    SENDEN &#8212; Transmit
                </button>
            </form>
        </div>
    </x-form-layout>
</x-layout>
