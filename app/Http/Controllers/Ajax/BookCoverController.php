<?php

namespace App\Http\Controllers\Ajax;

use App\Models\BookCover;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookCoverController extends Controller
{
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_covers,name',
        ]);

        $cover = BookCover::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'id' => $cover->id,
            'name' => $cover->name,
        ]);
    }
}
