<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BookSeries;
use App\Models\BookCover;
use App\Models\BookTopic;
use Illuminate\Support\Facades\Storage;


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

    public function mainImage()
    {
        return $this->hasOne(BookImage::class)->where('is_main', true);
    }

    public function images()
    {
        return $this->hasMany(BookImage::class);
    }

    public function getImageUrlAttribute(): string
    {
        $b2 = rtrim((string) env('B2_BUCKET_URL'), '/');

        $image = $this->relationLoaded('images')
            ? ($this->images->firstWhere('is_main', true) ?? $this->images->first())
            : $this->images()->orderByDesc('is_main')->orderBy('id')->first();

        $path = $image?->image_path;
        if (!$path) {
            return asset('images/error-image-not-found.png');
        }

        $path = ltrim((string) $path, '/');

        // Nieuw formaat: "books/123/file.jpg" (of "covers/..")
        if (str_contains($path, '/')) {
            return $b2 . '/' . $path;
        }

        // Oud formaat: enkel filename "file.jpg" -> ga uit van books/{id}/file.jpg
        return $b2 . "/books/{$this->id}/{$path}";
    }
}
