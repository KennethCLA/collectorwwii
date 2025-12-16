<?php

namespace App\Http\Controllers\Admin\Ajax;

use App\Models\BookSeries;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookSeriesController extends Controller
{
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_series,name',
        ]);

        $series = BookSeries::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'id' => $series->id,
            'name' => $series->name,
        ]);
    }
}
