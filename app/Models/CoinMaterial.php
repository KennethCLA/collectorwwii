<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinMaterial extends Model
{
    protected $table = 'coin_materials';

    protected $fillable = ['name'];
}
