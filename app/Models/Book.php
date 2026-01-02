<?php
// app/Models/Book.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use App\Models\BookSeries;
use App\Models\BookCover;
use App\Models\BookTopic;
use App\Models\MediaFile;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'title',
        'subtitle',
        'title_first_edition',
        'subtitle_first_edition',
        'description',
        'translator',
        'copyright_year',
        'issue_number',
        'issue_year',
        'series_id',
        'series_number',
        'pages',
        'cover_id',
        'topic_id',
        'copyright_year_first_issue',
        'publisher_name',
        'publisher_first_issue',
        'purchase_price',
        'purchase_date',
        'notes',
        'storage_location',
        'for_sale',
        'selling_price',
        'weight',
        'width',
        'height',
        'thickness'
    ];

    protected $casts = [
        'for_sale' => 'boolean',
        'purchase_date' => 'date',
        'copyright_year' => 'integer',
        'issue_year' => 'integer',
        'selling_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function series()
    {
        return $this->belongsTo(BookSeries::class, 'series_id');
    }

    public function cover()
    {
        return $this->belongsTo(BookCover::class, 'cover_id');
    }

    public function topic()
    {
        return $this->belongsTo(BookTopic::class, 'topic_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_authors');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'attachable');
    }

    public function images(): MorphMany
    {
        return $this->media()
            ->where('collection', 'images')
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function files(): MorphMany
    {
        return $this->media()
            ->where('collection', 'files')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(MediaFile::class, 'attachable')
            ->where('collection', 'images')
            ->where('is_main', 1);
    }

    /**
     * Fallback: als er geen is_main is, pak dan eerste image volgens sortering.
     */
    public function mainImageFile(): ?MediaFile
    {
        return $this->mainImage()->first() ?? $this->images()->first();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->mainImageFile()?->url()
            ?? asset('images/error-image-not-found.png');
    }
}
