<?php
// app/Models/BookFile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookFile extends Model
{
    protected $fillable = [
        'book_id',
        'type',
        'title',
        'path',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Helpers (optioneel)
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    public function storagePath(): ?string
    {
        $p = ltrim((string) $this->path, '/');
        if ($p === '') return null;

        // Legacy: sommige records/paths zaten onder "local/"
        if (str_starts_with($p, 'local/')) {
            $p = substr($p, 6); // remove "local/"
        }

        // Legacy: als path enkel filename is
        if (!str_contains($p, '/')) {
            $p = "books/{$this->book_id}/{$p}";
        }

        return $p;
    }
}
