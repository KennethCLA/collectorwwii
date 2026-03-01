{{-- resources/views/errors/500.blade.php --}}
<x-layout :mainClass="'w-full px-0 py-0'">
    <div class="flex min-h-[calc(100dvh-var(--header-h,0px))] items-center justify-center px-4">
        <div class="w-full max-w-lg rounded-2xl bg-sage border border-black/20 px-8 py-12 text-center shadow-lg noise-texture">
            <svg class="mx-auto h-8 w-8 text-khaki/50 mb-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M7 0h6v7h7v6h-7v7H7v-7H0V7h7V0z"/>
            </svg>
            <p class="font-stencil text-xs tracking-[0.4em] text-khaki/70 uppercase mb-2">Seite beschädigt</p>
            <p class="font-stencil text-7xl font-black text-white/15 tracking-widest">500</p>
            <h1 class="font-stencil mt-2 text-3xl font-black tracking-[0.2em] text-khaki uppercase">BESCHÄDIGT</h1>
            <div class="mx-auto mt-4 h-px w-24 bg-khaki/30"></div>
            <p class="mt-4 text-sm text-white/60 tracking-wide">An unexpected error has occurred.</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 rounded-md bg-white/10 px-5 py-2.5 text-sm font-medium text-white hover:bg-white/15 transition">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Back to home
                </a>
                <button onclick="location.reload()"
                    class="inline-flex items-center gap-2 rounded-md border border-white/20 px-5 py-2.5 text-sm font-medium text-white/80 hover:text-white hover:bg-white/5 transition">
                    Try again
                </button>
            </div>
        </div>
    </div>
</x-layout>
