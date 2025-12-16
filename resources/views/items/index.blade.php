@extends('layouts.app')
<div>
    <label class="block text-sm font-medium text-slate-700">Categorie</label>
    <select name="category" class="mt-1 w-full rounded-xl border-slate-300">
        <option value="">Alle</option>
        @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected((string)request('category')===(string)$c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
</div>


<div>
    <label class="block text-sm font-medium text-slate-700">Origin</label>
    <select name="origin" class="mt-1 w-full rounded-xl border-slate-300">
        <option value="">Alle</option>
        @foreach($origins as $o)
        <option value="{{ $o->id }}" @selected((string)request('origin')===(string)$o->id)>{{ $o->name }}</option>
        @endforeach
    </select>
</div>


<div>
    <label class="block text-sm font-medium text-slate-700">Organization</label>
    <select name="organization" class="mt-1 w-full rounded-xl border-slate-300">
        <option value="">Alle</option>
        @foreach($organizations as $org)
        <option value="{{ $org->id }}" @selected((string)request('organization')===(string)$org->id)>{{ $org->name }}</option>
        @endforeach
    </select>
</div>


<div>
    <label class="block text-sm font-medium text-slate-700">Nationality</label>
    <select name="nationality" class="mt-1 w-full rounded-xl border-slate-300">
        <option value="">Alle</option>
        @foreach($nationalities as $n)
        <option value="{{ $n->id }}" @selected((string)request('nationality')===(string)$n->id)>{{ $n->name }}</option>
        @endforeach
    </select>
</div>


<div>
    <label class="block text-sm font-medium text-slate-700">Sorteer</label>
    <select name="sort" class="mt-1 w-full rounded-xl border-slate-300">
        @php($sort = request('sort','title_asc'))
        <option value="title_asc" @selected($sort==='title_asc' )>Titel A–Z</option>
        <option value="title_desc" @selected($sort==='title_desc' )>Titel Z–A</option>
        <option value="created_at_desc" @selected($sort==='created_at_desc' )>Nieuwste</option>
        <option value="created_at_asc" @selected($sort==='created_at_asc' )>Oudste</option>
    </select>
</div>


<div class="flex gap-3 pt-2">
    <a href="{{ route('items.index') }}" class="flex-1 text-center rounded-xl border px-4 py-2">Reset</a>
    <button class="flex-1 rounded-xl bg-slate-800 text-white px-4 py-2">Toepassen</button>
</div>
</form>
</div>
</aside>


<section class="lg:col-span-9 space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Items</h1>
        <p class="text-sm text-slate-500">{{ $items->total() }} resultaten</p>
    </div>


    @if($items->count() === 0)
    <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
        <p class="text-slate-600">Geen resultaten. Pas filters of zoekterm aan.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($items as $item)
        <a href="{{ route('items.show', $item) }}" class="block rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-md transition overflow-hidden">
            <div class="aspect-[4/3] bg-slate-100">
                @php($main = $item->images()->where('is_main', true)->first())
                @if($main)
                <img src="{{ Storage::disk('public')->url($main->image_path) }}" alt="{{ $item->title }}" class="h-full w-full object-cover" loading="lazy">
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-medium leading-tight line-clamp-2">{{ $item->title }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ optional($item->category)->name ?? '—' }}</p>
            </div>
        </a>
        @endforeach
    </div>
    <div class="pt-4">{{ $items->onEachSide(1)->links('pagination::tailwind') }}</div>
    @endif
</section>
</div>
@endsection