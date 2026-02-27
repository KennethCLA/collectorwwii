<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanknoteSeries extends Model
{
    protected $table = 'banknote_series';

    protected $fillable = ['name'];
}
