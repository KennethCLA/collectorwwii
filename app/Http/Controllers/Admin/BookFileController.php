<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookFileController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'files' => ['required', 'array'],
            'files.*' => [
                'file',
                'max:51200', // 50MB
                // images + pdf
                'mimetypes:application/pdf,image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif',
            ],
        ]);

        $disk = 'b2';

        // Bepaal of er al een main image is
        $hasMainImage = $book->images()->where('is_main', true)->exists();

        // sort_order: neem max, als alles 0 is blijft dit goed oplopen
        $nextImageSort = (int) ($book->images()->max('sort_order') ?? 0);
        if ($book->images()->exists()) {
            $nextImageSort++;
        }

        $nextFileSort = (int) ($book->files()->max('sort_order') ?? 0);
        if ($book->files()->exists()) {
            $nextFileSort++;
        }

        foreach ($request->file('files') as $i => $uploaded) {
            $mime = $uploaded->getMimeType() ?? '';
            $original = $uploaded->getClientOriginalName();

            $isPdf = str_starts_with($mime, 'application/pdf')
                || str_ends_with(strtolower($original), '.pdf');

            // collection bepalen
            $collection = $isPdf ? 'files' : 'images';

            // Folderstructuur: books/{book_id}/...
            $folder = "books/{$book->id}";
            $filename = uniqid('', true) . '.' . $uploaded->extension();
            $path = $uploaded->storeAs($folder, $filename, $disk);

            // main image enkel voor images, en enkel als er nog geen main is
            $makeMain = (!$hasMainImage && !$isPdf && $i === 0);

            $sortOrder = $collection === 'images'
                ? $nextImageSort++
                : $nextFileSort++;

            $book->media()->create([
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $mime ?: null,
                'size' => $uploaded->getSize(),
                'original_name' => $original,
                'collection' => $collection,
                'is_main' => $makeMain,
                'sort_order' => $sortOrder,
            ]);

            if ($makeMain) {
                $hasMainImage = true;
            }
        }
        return back()->with('success', 'Files uploaded.');
    }


    public function destroy(MediaFile $file)
    {
        Log::info('HIT destroy()', [
            'file_id' => $file->id,
            'attachable_type' => $file->attachable_type,
            'attachable_id' => $file->attachable_id,
            'collection' => $file->collection,
            'disk' => $file->disk,
            'path' => $file->path,
        ]);

        if ($file->attachable_type !== \App\Models\Book::class) {
            abort(404);
        }

        $book = $file->attachable;
        $this->authorize('update', $book);

        // 1) Log disk config (zonder secrets)
        $cfg = config("filesystems.disks.{$file->disk}", []);
        Log::info('Disk config snapshot', [
            'disk' => $file->disk,
            'driver' => $cfg['driver'] ?? null,
            'bucket' => $cfg['bucket'] ?? null,
            'region' => $cfg['region'] ?? null,
            'endpoint' => $cfg['endpoint'] ?? null,
            'url' => $cfg['url'] ?? null,
            'root' => $cfg['root'] ?? null,
        ]);

        // 2) Delete met result + exists check
        if ($file->path) {
            $existsBefore = Storage::disk($file->disk)->exists($file->path);
            $deleted = Storage::disk($file->disk)->delete($file->path);
            $existsAfter = Storage::disk($file->disk)->exists($file->path);

            Log::info('B2 delete result', [
                'path' => $file->path,
                'exists_before' => $existsBefore,
                'deleted_return' => $deleted,
                'exists_after' => $existsAfter,
            ]);
        }

        $wasMain = (bool) $file->is_main;
        $collection = $file->collection;

        $file->delete();

        if ($collection === 'images' && $wasMain) {
            $newMain = $book->images()->first();
            if ($newMain) $newMain->update(['is_main' => 1]);
        }

        return back()->with('success', 'File deleted.');
    }

    public function makeMain(MediaFile $file)
    {
        if ($file->attachable_type !== \App\Models\Book::class || $file->collection !== 'images') {
            abort(404);
        }

        $book = $file->attachable;
        $this->authorize('update', $book);

        // Zet alle images op 0, daarna deze op 1
        MediaFile::where('attachable_type', \App\Models\Book::class)
            ->where('attachable_id', $book->id)
            ->where('collection', 'images')
            ->update(['is_main' => 0]);

        $file->update(['is_main' => 1]);

        return back()->with('success', 'Main image updated.');
    }
}
