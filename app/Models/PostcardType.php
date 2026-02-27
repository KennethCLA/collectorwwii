<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostcardType extends Model
{
    protected $table = 'postcard_types';

    protected $fillable = ['name'];
}
