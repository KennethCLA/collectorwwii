<?php

namespace App\Http\Controllers\Admin\Ajax;

use App\Models\BookTopic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookTopicController extends Controller
{
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_topics,name',
        ]);

        $topic = BookTopic::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'id' => $topic->id,
            'name' => $topic->name,
        ]);
    }
}
