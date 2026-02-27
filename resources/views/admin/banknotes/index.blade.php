{{-- resources/views/admin/banknotes/index.blade.php --}}

<x-layout>
    @section('admin-content')
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-white">Banknotes</h1>
            <a href="{{ route('admin.banknotes.create') }}"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                New banknote
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
        @endif

        <form method="GET" action="{{ route('admin.banknotes.index') }}"
            class="mb-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-white/60 mb-1">Country</label>
                <select name="country_id"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}" @selected(request('country_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Currency</label>
                <select name="currency_id"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    @foreach($currencies as $c)
                    <option value="{{ $c->id }}" @selected(request('currency_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">For Sale</label>
                <select name="for_sale"
                    class="rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                    <option value="">All</option>
                    <option value="1" @selected(request('for_sale') === '1')>For sale</option>
                    <option value="0" @selected(request('for_sale') === '0')>Not for sale</option>
                </select>
            </div>
            <button type="submit"
                class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Filter</button>
            @if(request()->hasAny(['country_id','currency_id','for_sale']))
            <a href="{{ route('admin.banknotes.index') }}"
                class="rounded-md bg-white/5 px-4 py-2 text-sm text-white/60 hover:text-white">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto rounded-xl border border-black/20 bg-black/10">
            <table class="w-full text-sm text-white">
                <thead class="border-b border-white/10 text-white/60 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Country</th>
                        <th class="px-4 py-3 text-left">Currency</th>
                        <th class="px-4 py-3 text-left">Value</th>
                        <th class="px-4 py-3 text-left">Year</th>
                        <th class="px-4 py-3 text-left">For Sale</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($banknotes as $banknote)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 font-medium">{{ $banknote->country?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-white/70">{{ $banknote->currency?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-white/70">{{ $banknote->nominalValue?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-white/70">{{ $banknote->year ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($banknote->for_sale)
                            <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs text-emerald-200">Yes</span>
                            @else
                            <span class="text-white/40">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-white/50 text-xs">{{ $banknote->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.banknotes.edit', $banknote) }}"
                                    class="rounded-md bg-white/10 px-3 py-1 text-xs hover:bg-white/20">Edit</a>
                                <form method="POST" action="{{ route('admin.banknotes.destroy', $banknote) }}"
                                    onsubmit="return confirm('Delete this banknote?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-md bg-red-500/20 px-3 py-1 text-xs text-red-200 hover:bg-red-500/30">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-white/40">No banknotes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-white">
            {{ $banknotes->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
    @endsection
</x-layout>
