<?php

// app/Models/Location.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function books()
    {
        return $this->hasMany(\App\Models\Book::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('name');
    }

    public static function flatTree(?int $parentId = null, int $depth = 0): \Illuminate\Support\Collection
    {
        return static::where('parent_id', $parentId)->orderBy('name')->get()
            ->flatMap(fn ($node) => collect([(object)['id' => $node->id, 'name' => str_repeat('— ', $depth).$node->name]])
                ->concat(static::flatTree($node->id, $depth + 1)));
    }
}
