<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'image_path', 'is_main'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getB2UrlAttribute(): string
    {
        $path = ltrim((string) $this->image_path, '/');

        if (!str_contains($path, '/')) {
            $path = "items/{$this->item_id}/{$path}";
        }

        return Storage::disk('b2')->url($path);
    }
}
