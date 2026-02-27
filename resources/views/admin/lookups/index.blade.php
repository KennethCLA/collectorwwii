@extends('layouts.admin')

@section('admin-content')
<div class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-white">{{ $label }}</h1>
            <p class="mt-1 text-sm text-white/70">{{ $description }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_320px]">
        <div class="rounded-xl border border-black/20 bg-black/15 p-4">
            <form method="GET" action="{{ route('admin.lookups.index', ['type' => $type]) }}"
                class="mb-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="mb-1 block text-xs text-white/60">Search</label>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search name..."
                        class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-white/20">
                </div>
                <button type="submit"
                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">
                    Filter
                </button>
                @if($search !== '')
                <a href="{{ route('admin.lookups.index', ['type' => $type]) }}"
                    class="rounded-md bg-white/5 px-4 py-2 text-sm text-white/70 hover:text-white">
                    Clear
                </a>
                @endif
            </form>

            <div class="overflow-x-auto rounded-xl border border-black/25 bg-black/10">
                <table class="w-full text-sm text-white">
                    <thead class="border-b border-white/10 text-xs uppercase text-white/60">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">In use</th>
                            <th class="px-4 py-3 text-left">Created</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($rows as $row)
                        @php
                            $createdAt = '—';
                            if (!empty($row->created_at)) {
                                try {
                                    $createdAt = \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                                } catch (\Throwable $e) {
                                    $createdAt = (string) $row->created_at;
                                }
                            }
                        @endphp
                        <tr class="transition hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if(($row->usage_total ?? 0) > 0)
                                <span class="inline-flex items-center rounded-full bg-amber-500/20 px-2 py-0.5 text-xs text-amber-200">
                                    {{ $row->usage_total }}
                                </span>
                                @else
                                <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs text-emerald-200">
                                    0
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-white/60">{{ $createdAt }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.lookups.destroy', ['type' => $type, 'id' => $row->id]) }}"
                                    onsubmit="return confirm('Delete this option?');"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-md px-2.5 py-1 text-xs {{ ($row->usage_total ?? 0) > 0 ? 'cursor-not-allowed bg-white/10 text-white/40' : 'bg-red-500/20 text-red-200 hover:bg-red-500/30' }}"
                                        @disabled(($row->usage_total ?? 0) > 0)>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-7 text-center text-white/45">No options found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-white">
                {{ $rows->links('pagination::tailwind') }}
            </div>
        </div>

        <aside class="rounded-xl border border-black/20 bg-black/15 p-4">
            <h2 class="text-base font-semibold text-white">Add option</h2>
            <p class="mt-1 text-sm text-white/65">
                Add a new value that appears in selection fields.
            </p>

            <form method="POST" action="{{ route('admin.lookups.store', ['type' => $type]) }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="mb-1 block text-xs text-white/60">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-white/20">
                    @error('name')
                    <p class="mt-1 text-xs text-red-200">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    Save
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection
