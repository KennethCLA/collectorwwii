<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookSeries extends Model
{
    use HasFactory;

    protected $table = 'book_series'; // ✅ Zorg ervoor dat dit overeenkomt met je database
    protected $fillable = ['name'];
}

