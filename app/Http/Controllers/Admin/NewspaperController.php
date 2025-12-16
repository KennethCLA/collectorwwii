<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newspaper;
use Illuminate\Http\Request;

class NewspaperController extends Controller
{
    public function index()
    {
        $newspapers = Newspaper::all();
        return view('newspapers.index', compact('newspapers'));
    }

    public function create()
    {
        return view('newspapers.create');
    }

    public function store(Request $request)
    {
        Newspaper::create($request->validate([
            'title' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]));

        return redirect()->route('newspapers.index');
    }
}
