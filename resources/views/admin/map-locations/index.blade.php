@extends('layouts.admin')

@section('admin-content')
<div class="w-full">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-white">Map locations</h1>
        <a href="{{ route('admin.map-locations.create') }}"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
            New location
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('success') }}
    </div>
    @endif

    <form method="GET" action="{{ route('admin.map-locations.index') }}" class="mb-4 flex items-end gap-3">
        <div>
            <label class="mb-1 block text-xs text-white/60">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search location..."
                class="w-64 rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
        </div>
        <button type="submit"
            class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Filter</button>
        @if(request()->filled('search'))
        <a href="{{ route('admin.map-locations.index') }}" class="rounded-md bg-white/5 px-4 py-2 text-sm text-white/70">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-xl border border-black/20 bg-black/10">
        <table class="w-full text-sm text-white">
            <thead class="border-b border-white/10 text-white/60 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Coordinates</th>
                    <th class="px-4 py-3 text-left">Image</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($locations as $location)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-4 py-3 font-medium">{{ $location->name }}</td>
                    <td class="px-4 py-3 text-white/70">{{ $location->coordinates }}</td>
                    <td class="px-4 py-3">
                        <img src="{{ $location->image_url }}" alt="{{ $location->name }}" class="h-10 w-16 rounded object-cover">
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.map-locations.edit', $location) }}"
                                class="rounded-md bg-white/10 px-3 py-1 text-xs hover:bg-white/20">Edit</a>
                            <form method="POST" action="{{ route('admin.map-locations.destroy', $location) }}"
                                onsubmit="return confirm('Delete this location?');">
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
                    <td colspan="4" class="px-4 py-8 text-center text-white/40">No map locations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-white">
        {{ $locations->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>
@endsection
