<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanknoteTimePeriod extends Model
{
    protected $table = 'banknote_time_periods';

    protected $fillable = ['name'];
}
