<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BookImage extends Model
{
    protected $fillable = [
        'book_id',
        'image_path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Normalize legacy vs new paths:
     * - legacy: "foo.jpg"
     * - new:    "books/123/foo.jpg"
     */
    public function getNormalizedPathAttribute(): string
    {
        $path = ltrim((string) $this->image_path, '/');

        // If it already contains a folder (e.g. "books/123/foo.jpg"), keep it
        if (str_contains($path, '/')) {
            return $path;
        }

        // Legacy filename-only: prefix it
        return "books/{$this->book_id}/{$path}";
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('b2')->url($this->normalized_path);
    }
}
