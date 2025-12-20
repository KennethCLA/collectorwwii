<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $latestBlog = Blog::query()
            ->latest('date')   // of latest('created_at')
            ->first();

        return view('home', [
            'latestBlog' => $latestBlog ? [
                'date' => $latestBlog->date ?? $latestBlog->created_at,
                'content' => $latestBlog->content,
            ] : null
        ]);
    }
}
