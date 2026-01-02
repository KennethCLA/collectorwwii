<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemFileController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'files' => ['required', 'array'],
            // Items: enkel images (geen pdf)
            'files.*' => ['file', 'max:51200', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif'],
        ]);

        $disk = 'b2';

        $hasMainImage = $item->images()->where('is_main', true)->exists();

        $nextImageSort = (int) ($item->images()->max('sort_order') ?? 0);
        if ($item->images()->exists()) {
            $nextImageSort++;
        }

        foreach ($request->file('files') as $i => $uploaded) {
            $folder = "items/{$item->id}";
            $filename = uniqid('', true) . '.' . $uploaded->extension();
            $path = $uploaded->storeAs($folder, $filename, $disk);

            $makeMain = (!$hasMainImage && $i === 0);

            $item->media()->create([
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $uploaded->getMimeType(),
                'size' => $uploaded->getSize(),
                'original_name' => $uploaded->getClientOriginalName(),
                'collection' => 'images',
                'is_main' => $makeMain,
                'sort_order' => $nextImageSort++,
            ]);

            if ($makeMain) {
                $hasMainImage = true;
            }
        }

        return back()->with('success', 'Images uploaded.');
    }

    public function destroy(MediaFile $file)
    {
        if ($file->attachable_type !== \App\Models\Item::class || $file->collection !== 'images') {
            abort(404);
        }

        $item = $file->attachable;
        $this->authorize('update', $item);

        $deleted = null;

        if ($file->path) {
            $deleted = Storage::disk($file->disk)->delete($file->path);
            Log::info('B2 delete attempt', [
                'disk' => $file->disk,
                'path' => $file->path,
                'deleted_return' => $deleted,
            ]);
        }

        $wasMain = (bool) $file->is_main;
        $file->delete();

        if ($wasMain) {
            $newMain = $item->images()->first();
            if ($newMain) {
                MediaFile::where('attachable_type', \App\Models\Item::class)
                    ->where('attachable_id', $item->id)
                    ->where('collection', 'images')
                    ->update(['is_main' => 0]);

                $newMain->update(['is_main' => 1]);
            }
        }

        return back()->with('success', 'Image deleted.');
    }

    public function makeMain(MediaFile $file)
    {
        if ($file->attachable_type !== \App\Models\Item::class || $file->collection !== 'images') {
            abort(404);
        }

        $item = $file->attachable;
        $this->authorize('update', $item);

        MediaFile::where('attachable_type', \App\Models\Item::class)
            ->where('attachable_id', $item->id)
            ->where('collection', 'images')
            ->update(['is_main' => 0]);

        $file->update(['is_main' => 1]);

        return back()->with('success', 'Main image updated.');
    }
}
