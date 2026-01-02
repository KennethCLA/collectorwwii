<?php
// app/Models/Item.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemCategory;
use App\Models\ItemNationality;
use App\Models\ItemOrganization;
use App\Models\ItemOrigin;
use App\Models\MediaFile;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'origin_id',
        'nationality_id',
        'organization_id',
        'purchase_price',
        'purchase_date',
        'purchase_location',
        'notes',
        'storage_location',
        'current_price',
        'for_sale',
        'selling_price'
    ];

    protected $casts = [
        'for_sale' => 'boolean',
        'purchase_date' => 'date',
        'selling_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function origin()
    {
        return $this->belongsTo(ItemOrigin::class, 'origin_id');
    }

    public function nationality()
    {
        return $this->belongsTo(
            ItemNationality::class,
            'nationality_id'
        );
    }

    public function organization()
    {
        return $this->belongsTo(ItemOrganization::class, 'organization_id');
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
        // Gebruik loaded relations als ze al geladen zijn (show pagina)
        if ($this->relationLoaded('mainImage') || $this->relationLoaded('images')) {
            return $this->getRelation('mainImage')
                ?? ($this->getRelation('images')?->first());
        }

        // Fallback wanneer niet eager loaded (bvb. ergens anders)
        return $this->mainImage()->first() ?? $this->images()->first();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->mainImageFile()?->url()
            ?? asset('images/error-image-not-found.png');
    }
}
