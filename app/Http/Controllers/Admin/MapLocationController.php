<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MapLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', MapLocation::class);

        $query = MapLocation::query()->with('mainImage');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $locations = $query->orderBy('name')->paginate(50);

        return view('admin/map-locations/index', [
            'locations' => $locations,
        ]);
    }

    public function create()
    {
        $this->authorize('create', MapLocation::class);

        return view('admin/map-locations/create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', MapLocation::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'max:51200', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif'],
        ]);

        $location = MapLocation::create([
            'name' => $validated['name'],
            'coordinates' => $this->coordinates((float) $validated['latitude'], (float) $validated['longitude']),
            'description' => $validated['description'] ?? null,
        ]);

        $hasMain = false;
        $sortOrder = 0;
        foreach ($request->file('photos', []) as $idx => $uploaded) {
            $folder = "map-locations/{$location->id}";
            $ext = strtolower($uploaded->extension() ?: 'bin');
            $filename = (string) Str::uuid().'.'.$ext;
            $path = $uploaded->storeAs($folder, $filename, 'b2');

            $isMain = ! $hasMain && $idx === 0;
            $location->media()->create([
                'disk' => 'b2',
                'path' => $path,
                'mime_type' => $uploaded->getMimeType(),
                'size' => $uploaded->getSize(),
                'original_name' => $uploaded->getClientOriginalName(),
                'collection' => 'images',
                'is_main' => $isMain,
                'sort_order' => $sortOrder++,
            ]);

            if ($isMain) {
                $hasMain = true;
            }
        }

        return redirect()->route('admin.map-locations.edit', $location)
            ->with('success', 'Map location created.');
    }

    public function edit(MapLocation $mapLocation)
    {
        $this->authorize('update', $mapLocation);

        $mapLocation->load(['images', 'files']);

        return view('admin/map-locations/edit', [
            'location' => $mapLocation,
            'latitude' => $mapLocation->latitude(),
            'longitude' => $mapLocation->longitude(),
        ]);
    }

    public function update(Request $request, MapLocation $mapLocation)
    {
        $this->authorize('update', $mapLocation);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string'],
        ]);

        $mapLocation->update([
            'name' => $validated['name'],
            'coordinates' => $this->coordinates((float) $validated['latitude'], (float) $validated['longitude']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.map-locations.edit', $mapLocation)
            ->with('success', 'Map location updated.');
    }

    public function destroy(MapLocation $mapLocation)
    {
        $this->authorize('delete', $mapLocation);

        $mapLocation->load('media');

        foreach ($mapLocation->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('map-locations/'.$mapLocation->id);
        $mapLocation->media()->delete();
        $mapLocation->delete();

        return redirect()->route('admin.map-locations.index')
            ->with('success', 'Map location deleted.');
    }

    private function coordinates(float $lat, float $lng): string
    {
        return number_format($lat, 6, '.', '').','.number_format($lng, 6, '.', '');
    }
}
