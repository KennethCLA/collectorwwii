{{-- resources/views/profile/index.blade.php --}}
<x-layout :mainClass="'w-full px-4 py-6 sm:px-6 lg:px-8'">
    <div class="mx-auto w-full max-w-5xl space-y-6 pt-6">

        {{-- Soldbuch header --}}
        <div class="rounded-2xl bg-black/20 p-5 ring-1 ring-black/30 noise-texture">
            <p class="font-stencil text-xs tracking-[0.4em] text-khaki/60 uppercase mb-1">Personalakte</p>
            <h1 class="font-stencil text-3xl font-black tracking-[0.2em] text-white uppercase">SOLDBUCH</h1>
            <p class="font-mono text-[10px] tracking-[0.25em] text-white/40 mt-1 uppercase">
                Inhaber: {{ $user->name }} &nbsp;·&nbsp; WK II Sammlung
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[220px_1fr]">

            {{-- Sidebar: service record sections --}}
            <aside class="rounded-2xl bg-black/20 ring-1 ring-black/30 p-5 text-white noise-texture lg:sticky lg:top-20 lg:self-start">
                <p class="font-stencil text-[10px] uppercase tracking-[0.2em] text-white/40 mb-3">Dienstakte</p>

                <div class="space-y-4">
                    <div>
                        <p class="font-stencil text-[10px] uppercase tracking-[0.18em] text-khaki/60 mb-1.5 border-b border-khaki/20 pb-1">Erfassung</p>
                        <ul class="space-y-1">
                            @foreach([
                                ['label' => 'Buch', 'route' => 'books.create'],
                                ['label' => 'Gegenstand', 'route' => 'items.create'],
                                ['label' => 'Zeitung', 'route' => 'newspapers.create'],
                                ['label' => 'Zeitschrift', 'route' => 'magazines.create'],
                                ['label' => 'Banknote', 'route' => 'banknotes.create'],
                                ['label' => 'Münze', 'route' => 'coins.create'],
                                ['label' => 'Postkarte', 'route' => 'postcards.create'],
                                ['label' => 'Briefmarke', 'route' => 'stamps.create'],
                            ] as $link)
                            <li>
                                <a href="{{ route($link['route']) }}"
                                   class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition">
                                    <span class="font-mono text-[9px] text-khaki/50">+</span>
                                    {{ $link['label'] }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <p class="font-stencil text-[10px] uppercase tracking-[0.18em] text-khaki/60 mb-1.5 border-b border-khaki/20 pb-1">Bearbeitung</p>
                        <ul class="space-y-1">
                            @foreach([
                                ['label' => 'Buch', 'route' => 'books.edit', 'id' => $book->id],
                                ['label' => 'Gegenstand', 'route' => 'items.edit', 'id' => $item->id],
                                ['label' => 'Zeitung', 'route' => 'newspapers.edit', 'id' => $newspaper->id],
                                ['label' => 'Zeitschrift', 'route' => 'magazines.edit', 'id' => $magazine->id],
                                ['label' => 'Banknote', 'route' => 'banknotes.edit', 'id' => $banknote->id],
                                ['label' => 'Münze', 'route' => 'coins.edit', 'id' => $coin->id],
                                ['label' => 'Postkarte', 'route' => 'postcards.edit', 'id' => $postcard->id],
                                ['label' => 'Briefmarke', 'route' => 'stamps.edit', 'id' => $stamp->id],
                            ] as $link)
                            <li>
                                <a href="{{ route($link['route'], $link['id']) }}"
                                   class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition">
                                    <span class="font-mono text-[9px] text-khaki/50">›</span>
                                    {{ $link['label'] }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>

            {{-- Main content --}}
            <main class="space-y-6">
                <div class="rounded-2xl bg-black/20 ring-1 ring-black/30 overflow-hidden">
                    <img src="{{ asset('storage/images/hitlers-gustav-gun.jpg') }}" alt="Gustav Kanone"
                        class="w-full h-[460px] object-cover">
                    <div class="px-5 py-3 border-t border-black/30">
                        <p class="font-mono text-[10px] tracking-[0.2em] text-white/35 uppercase">
                            Dok. Nr. WKII-PF-001 &nbsp;·&nbsp; Schwere Artillerie &nbsp;·&nbsp; Dora / Gustav
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-layout>
