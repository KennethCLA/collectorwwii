{{-- resources/views/admin/books/_pdf-card.blade.php --}}
@php
/** @var \App\Models\BookFile $pdf */
$url = $pdf->url();
$title = $pdf->original_name
? pathinfo($pdf->original_name, PATHINFO_FILENAME)
: 'PDF';
@endphp

<div class="max-w-xl rounded-md bg-[#343933] border border-white/10 overflow-hidden">
    {{-- PREVIEW --}}
    <div class="w-full h-48 bg-black/20 overflow-hidden">
        @if($url)
        <iframe
            src="{{ $url }}#page=1&view=FitH"
            class="w-full h-full border-0 pointer-events-none"></iframe>
        @else
        <div class="w-full h-full flex items-center justify-center text-xs text-white/50">
            No preview
        </div>
        @endif
    </div>

    {{-- INFO + ACTIONS --}}
    <div class="p-3 grid grid-cols-[1fr_auto] gap-4 items-start">
        {{-- LEFT: INFO --}}
        <div class="min-w-0">
            <div class="text-white font-semibold truncate">
                {{ $title }}
            </div>
            <div class="text-[11px] text-white/60 break-all mt-1">
                {{ $pdf->path }}
            </div>
        </div>

        {{-- RIGHT: ACTIONS --}}
        <div class="flex flex-col gap-2 shrink-0">
            @if($url)
            <a href="{{ $url }}"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center h-7 px-3 text-[10px] leading-none rounded bg-white/10 text-white hover:bg-white/20 transition">
                Open
            </a>
            @endif

            <form action="{{ route('admin.media.destroy', ['type' => 'books', 'file' => $pdf->id]) }}"
                method="POST"
                onsubmit="return confirm('Delete this file?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center justify-center h-7 px-3 text-[10px] leading-none rounded bg-red-600 text-white hover:bg-red-700 transition w-full">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>