<?php
// app/Http/Controllers/Admin/MediaFileController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Item;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaFileController extends Controller
{
    /**
     * Centrale definitie van wat mag per type.
     */
    private const TYPES = [
        'books' => [
            'model' => Book::class,
            'disk'  => 'b2',
            'folder' => 'books',
            'collections' => [
                'images' => [
                    'mimetypes' => 'image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif',
                    'max' => 51200, // KB
                ],
                'files' => [
                    'mimetypes' => 'application/pdf',
                    'max' => 51200, // KB
                ],
            ],
        ],
        'items' => [
            'model' => Item::class,
            'disk'  => 'b2',
            'folder' => 'items',
            'collections' => [
                'images' => [
                    'mimetypes' => 'image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif',
                    'max' => 51200, // KB
                ],
            ],
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
        $cfg = $this->typeConfigOrFail($type);
        $attachable = $this->resolveAttachableOrFail($type, $id);

        $this->authorize('update', $attachable);

        $allowedCollections = array_keys($cfg['collections']);

        $validated = $request->validate([
            'collection' => ['required', 'string', Rule::in($allowedCollections)],
            'files'      => ['required', 'array', 'min:1'],
            'files.*'    => ['file'], // mimetype/max voegen we hieronder toe per collection
        ]);

        $collection = $validated['collection'];
        $rulesCfg = $cfg['collections'][$collection];

        // Collection-specifieke constraints
        $request->validate([
            'files.*' => [
                'file',
                'max:' . $rulesCfg['max'],
                'mimetypes:' . $rulesCfg['mimetypes'],
            ],
        ]);

        $disk = $cfg['disk'];

        // Main image logic + sort order (alleen voor images)
        $hasMainImage = $collection === 'images'
            ? $attachable->images()->where('is_main', true)->exists()
            : true;

        $nextSort = null;
        if ($collection === 'images') {
            $currentMax = (int) ($attachable->images()->max('sort_order') ?? 0);
            $nextSort = $attachable->images()->exists() ? $currentMax + 1 : 0;
        }

        $uploadedPaths = []; // best-effort cleanup bij fouten

        foreach ($request->file('files', []) as $i => $uploaded) {
            $folder = "{$cfg['folder']}/{$attachable->id}";
            $ext = strtolower($uploaded->extension() ?: 'bin');
            $filename = (string) Str::uuid() . '.' . $ext;

            // 1) eerst uploaden
            $path = $uploaded->storeAs($folder, $filename, $disk);
            $uploadedPaths[] = [$disk, $path];

            $makeMain = ($collection === 'images') && (!$hasMainImage && $i === 0);

            // 2) dan DB record
            try {
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
            } catch (\Throwable $e) {
                // DB faalde → delete net geüploade file (best effort)
                try {
                    Storage::disk($disk)->delete($path);
                } catch (\Throwable $deleteErr) {
                    Log::warning('Media upload cleanup failed', [
                        'disk' => $disk,
                        'path' => $path,
                        'error' => $deleteErr->getMessage(),
                    ]);
                }

                throw $e;
            }

            if ($makeMain) {
                $hasMainImage = true;
            }
        }

        return back()->with('success', 'Upload successful.');
    }

    public function destroy(Request $request, string $type, MediaFile $file)
    {
        $cfg = $this->typeConfigOrFail($type);

        // Guardrails: type match + collection bestaat voor dat type
        abort_unless($file->attachable_type === $cfg['model'], 404);
        abort_unless(isset($cfg['collections'][$file->collection]), 404);

        $attachable = $file->attachable;
        abort_if(!$attachable, 404);

        $this->authorize('update', $attachable);

        $wasMain = (bool) $file->is_main;
        $collection = $file->collection;

        // Delete in storage (best effort, log result)
        if ($file->path) {
            $deleted = Storage::disk($file->disk)->delete($file->path);

            Log::info('Media delete attempt', [
                'disk' => $file->disk,
                'path' => $file->path,
                'deleted_return' => $deleted,
                'media_id' => $file->id,
                'attachable_type' => $file->attachable_type,
                'attachable_id' => $file->attachable_id,
            ]);
        }

        DB::transaction(function () use ($file, $cfg, $attachable, $wasMain, $collection) {
            $file->delete();

            // Als main image weg is: nieuwe main zetten (alleen images)
            if ($wasMain && $collection === 'images') {
                // alles resetten
                MediaFile::where('attachable_type', $cfg['model'])
                    ->where('attachable_id', $attachable->id)
                    ->where('collection', 'images')
                    ->update(['is_main' => 0]);

                // nieuwe main kiezen met duidelijke ordering
                $newMain = $attachable->images()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($newMain) {
                    $newMain->update(['is_main' => 1]);
                }
            }
        });

        return back()->with('success', 'File deleted.');
    }

    public function makeMain(Request $request, string $type, MediaFile $file)
    {
        $cfg = $this->typeConfigOrFail($type);

        abort_unless($file->attachable_type === $cfg['model'], 404);
        abort_unless($file->collection === 'images', 404); // makeMain enkel voor images

        $attachable = $file->attachable;
        abort_if(!$attachable, 404);

        $this->authorize('update', $attachable);

        DB::transaction(function () use ($cfg, $attachable, $file) {
            MediaFile::where('attachable_type', $cfg['model'])
                ->where('attachable_id', $attachable->id)
                ->where('collection', 'images')
                ->update(['is_main' => 0]);

            $file->update(['is_main' => 1]);
        });

        return back()->with('success', 'Main image updated.');
    }
}
