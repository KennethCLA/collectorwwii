{{-- resources/views/admin/items/index.blade.php --}}

<x-layout>
    @section('admin-content')
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-white">Items</h1>
            <a href="{{ route('admin.items.create') }}"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                New item
            </a>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.items.index') }}"
            class="mb-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-white/60 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search title..."
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20 w-56" />
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Category</label>
                <select name="category"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Nationality</label>
                <select name="nationality"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($nationalities as $n)
                    <option value="{{ $n->id }}" @selected(request('nationality') == $n->id)>{{ $n->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Origin</label>
                <select name="origin"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($origins as $o)
                    <option value="{{ $o->id }}" @selected(request('origin') == $o->id)>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Organization</label>
                <select name="organization"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}" @selected(request('organization') == $org->id)>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                Filter
            </button>
            @if(request()->hasAny(['search','category','nationality','origin','organization']))
            <a href="{{ route('admin.items.index') }}"
                class="rounded-md bg-white/5 px-4 py-2 text-sm text-white/60 hover:text-white">
                Clear
            </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-xl border border-black/20 bg-black/10">
            <table class="w-full text-sm text-white">
                <thead class="border-b border-white/10 text-white/60 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Nationality</th>
                        <th class="px-4 py-3 text-left">For Sale</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($items as $item)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 font-medium">{{ $item->title }}</td>
                        <td class="px-4 py-3 text-white/70">{{ $item->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-white/70">{{ $item->nationality?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($item->for_sale)
                            <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs text-emerald-200">Yes</span>
                            @else
                            <span class="text-white/40">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-white/50 text-xs">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.items.edit', $item) }}"
                                    class="rounded-md bg-white/10 px-3 py-1 text-xs hover:bg-white/20">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.items.destroy', $item) }}"
                                    onsubmit="return confirm('Delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-md bg-red-500/20 px-3 py-1 text-xs text-red-200 hover:bg-red-500/30">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-white/40">No items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 text-white">
            {{ $items->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
    @endsection
</x-layout>
