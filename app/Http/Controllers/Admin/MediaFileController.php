<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Item;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaFileController extends Controller
{
    /**
     * Centrale definitie van wat mag per type.
     */
    private const TYPES = [
        'books' => [
            'model' => Book::class,
            'disk'  => 'b2',
            'collections' => [
                'images' => [
                    'mimetypes' => 'image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif',
                    'max' => 51200,
                ],
                'files' => [
                    'mimetypes' => 'application/pdf',
                    'max' => 51200,
                ],
            ],
            'folder' => 'books', // opslagfolder prefix
        ],
        'items' => [
            'model' => Item::class,
            'disk'  => 'b2',
            'collections' => [
                'images' => [
                    'mimetypes' => 'image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif',
                    'max' => 51200,
                ],
            ],
            'folder' => 'items',
        ],
    ];

    private function typeConfigOrFail(string $type): array
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        return self::TYPES[$type];
    }

    private function resolveAttachableOrFail(string $type, int|string $id)
    {
        $cfg = $this->typeConfigOrFail($type);
        $modelClass = $cfg['model'];

        return $modelClass::query()->findOrFail($id);
    }

    public function store(Request $request, string $type, int $id)
    {
        $attachable = $this->resolveAttachableOrFail($type, $id);
        $this->authorize('update', $attachable);

        $cfg = $this->typeConfigOrFail($type);

        $collection = $request->input('collection', 'images');
        abort_unless(isset($cfg['collections'][$collection]), 404);

        $rulesCfg = $cfg['collections'][$collection];

        $validated = $request->validate([
            'collection' => ['required', 'string'],
            'files'      => ['required', 'array'],
            'files.*'    => ['file', 'max:' . $rulesCfg['max'], 'mimetypes:' . $rulesCfg['mimetypes']],
        ]);

        $disk = $cfg['disk'];

        $hasMainImage = $collection === 'images'
            ? $attachable->images()->where('is_main', true)->exists()
            : true; // voor pdf's irrelevant

        $nextSort = 0;
        if ($collection === 'images') {
            $nextSort = (int) ($attachable->images()->max('sort_order') ?? 0);
            if ($attachable->images()->exists()) {
                $nextSort++;
            }
        }

        foreach ($request->file('files') as $i => $uploaded) {
            $folder = "{$cfg['folder']}/{$attachable->id}";
            $filename = uniqid('', true) . '.' . $uploaded->extension();
            $path = $uploaded->storeAs($folder, $filename, $disk);

            $makeMain = ($collection === 'images') && (!$hasMainImage && $i === 0);

            $attachable->media()->create([
                'disk'          => $disk,
                'path'          => $path,
                'mime_type'     => $uploaded->getMimeType(),
                'size'          => $uploaded->getSize(),
                'original_name' => $uploaded->getClientOriginalName(),
                'collection'    => $collection,
                'is_main'       => $makeMain,
                'sort_order'    => $collection === 'images' ? $nextSort++ : null,
            ]);

            if ($makeMain) {
                $hasMainImage = true;
            }
        }

        return back()->with('success', 'Upload successful.');
    }

    public function destroy(Request $request, string $type, MediaFile $file)
    {
        $cfg = $this->typeConfigOrFail($type);

        // Guardrails: type + attachable_type matchen, en collection mag bestaan
        abort_unless($file->attachable_type === $cfg['model'], 404);
        abort_unless(isset($cfg['collections'][$file->collection]), 404);

        $attachable = $file->attachable;
        $this->authorize('update', $attachable);

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
        $collection = $file->collection;

        $file->delete();

        // Als main image weg is: nieuwe main zetten (alleen images)
        if ($wasMain && $collection === 'images') {
            $newMain = $attachable->images()->first();
            if ($newMain) {
                MediaFile::where('attachable_type', $cfg['model'])
                    ->where('attachable_id', $attachable->id)
                    ->where('collection', 'images')
                    ->update(['is_main' => 0]);

                $newMain->update(['is_main' => 1]);
            }
        }

        return back()->with('success', 'File deleted.');
    }

    public function makeMain(Request $request, string $type, MediaFile $file)
    {
        $cfg = $this->typeConfigOrFail($type);

        abort_unless($file->attachable_type === $cfg['model'], 404);
        abort_unless($file->collection === 'images', 404); // makeMain enkel voor images

        $attachable = $file->attachable;
        $this->authorize('update', $attachable);

        MediaFile::where('attachable_type', $cfg['model'])
            ->where('attachable_id', $attachable->id)
            ->where('collection', 'images')
            ->update(['is_main' => 0]);

        $file->update(['is_main' => 1]);

        return back()->with('success', 'Main image updated.');
    }
}
