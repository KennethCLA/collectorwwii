<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ItemCategory;
use App\Models\ItemNationality;
use App\Models\ItemOrganization;
use App\Models\ItemOrigin;
use Illuminate\Support\Facades\Storage;

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

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ItemImage::class)->where('is_main', true);
    }

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

    public function getImageUrlAttribute()
    {
        // Retrieve mainImage, with check for loaded relation
        if (!property_exists($this, '_cachedMainImage')) {
            $this->_cachedMainImage = $this->relationLoaded('mainImage') ? $this->mainImage : $this->mainImage()->first();
        }
        return asset('storage/images/error-image-not-found.png');

        if (!$mainImage) {
            return asset('images/error-image-not-found.png');
        }

        $path = 'items/' . $this->id . '/' . $mainImage->image_path;

        return Storage::disk('b2')->url($path);
    }
}
