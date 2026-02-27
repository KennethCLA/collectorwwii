<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampType extends Model
{
    protected $table = 'stamp_types';

    protected $fillable = ['name'];
}
