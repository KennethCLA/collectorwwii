@extends('layouts.admin')

@section('admin-content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<form action="{{ route('admin.map-locations.store') }}" method="POST" enctype="multipart/form-data" class="w-full">
    @csrf

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Create map location</h1>
            <p class="mt-1 text-sm text-white/60">Add a visited location with exact coordinates.</p>
            <p class="mt-1 text-xs text-white/55">You can upload photos now or add more after saving.</p>
        </div>
        <a href="{{ route('admin.map-locations.index') }}"
            class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Back</a>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
        <div class="font-semibold mb-2">Please fix the following:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-medium text-white/80">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">Latitude *</label>
                <input id="latitude" type="number" step="0.000001" min="-90" max="90" name="latitude" value="{{ old('latitude') }}" required
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
            </div>
            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">Longitude *</label>
                <input id="longitude" type="number" step="0.000001" min="-180" max="180" name="longitude" value="{{ old('longitude') }}" required
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-white/80">Pick location on map</label>
            <div class="space-y-2">
                <div class="flex gap-2">
                    <input id="map-search" type="text" placeholder="Search location (city, address, place...)"
                        class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                    <button id="map-search-btn" type="button"
                        class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Search</button>
                </div>
                <div id="map-search-results" class="hidden rounded-md border border-white/10 bg-black/25 p-2"></div>
            </div>
            <div id="map-picker" class="h-[28rem] w-full rounded-xl ring-1 ring-black/30"></div>
            <p class="text-xs text-white/60">Click on the map to place the pin. Coordinates update automatically.</p>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-white/80">Description</label>
            <textarea name="description" rows="6"
                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('description') }}</textarea>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-white/80">Photos (optional)</label>
            <input type="file" name="photos[]" multiple accept="image/*"
                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-white hover:file:bg-white/15">
            <p class="text-xs text-white/60">Select one or more images. The first becomes the main photo.</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.map-locations.index') }}"
                class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Cancel</a>
            <button type="submit"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Create location</button>
        </div>
    </div>
</form>

@include('admin.map-locations._map-search-script')
@endsection
