<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postcard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'year',
        'postcard_type_id',
        'occasion',
        'unstamped',
        'stamped',
        'special_stamp',
        'for_sale',
        'selling_price',
        'purchase_date',
        'purchasing_price',
        'current_value',
        'location_id',
        'personal_remarks',
    ];

    protected $casts = [
        'for_sale' => 'boolean',
        'unstamped' => 'boolean',
        'stamped' => 'boolean',
        'special_stamp' => 'boolean',
        'purchase_date' => 'date',
        'selling_price' => 'decimal:2',
        'purchasing_price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function postcardType()
    {
        return $this->belongsTo(PostcardType::class, 'postcard_type_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
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

    public function mainImageFile(): ?MediaFile
    {
        if ($this->relationLoaded('mainImage') || $this->relationLoaded('images')) {
            return $this->getRelation('mainImage')
                ?? ($this->getRelation('images')?->first());
        }

        return $this->mainImage()->first() ?? $this->images()->first();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->mainImageFile()?->url()
            ?? asset('images/error-image-not-found.png');
    }

    public function getCardTitleAttribute(): string
    {
        $parts = array_filter([
            $this->country?->name,
            $this->year,
        ]);

        return implode(' · ', $parts) ?: 'Postcard #'.$this->id;
    }
}
