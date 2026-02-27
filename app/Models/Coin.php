<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Coin extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'currency_id',
        'nominal_value_id',
        'shape_id',
        'material_id',
        'year',
        'occasion_id',
        'for_sale',
        'selling_price',
        'purchase_date',
        'purchasing_price',
        'location_id',
        'personal_remarks',
    ];

    protected $casts = [
        'for_sale' => 'boolean',
        'purchase_date' => 'date',
        'selling_price' => 'decimal:2',
        'purchasing_price' => 'decimal:2',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function nominalValue()
    {
        return $this->belongsTo(NominalValue::class);
    }

    public function shape()
    {
        return $this->belongsTo(CoinShape::class, 'shape_id');
    }

    public function material()
    {
        return $this->belongsTo(CoinMaterial::class, 'material_id');
    }

    public function occasion()
    {
        return $this->belongsTo(CoinOccasion::class, 'occasion_id');
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
            $this->nominalValue?->name,
            $this->country?->name,
            $this->year,
        ]);
        return implode(' · ', $parts) ?: 'Coin #' . $this->id;
    }
}
