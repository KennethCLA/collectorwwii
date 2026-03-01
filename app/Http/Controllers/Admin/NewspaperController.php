<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newspaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewspaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Newspaper::class);

        $query = Newspaper::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $newspapers = $query->orderByDesc('created_at')->paginate(50);

        return view('admin.newspapers.index', compact('newspapers'));
    }

    public function create()
    {
        $this->authorize('create', Newspaper::class);

        return view('admin.newspapers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Newspaper::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'condition' => 'nullable|string|max:50',
            'sold_at' => 'nullable|date',
            'sold_price' => 'nullable|numeric|min:0',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');
        if (! empty($validated['sold_at'])) {
            $validated['for_sale'] = false;
            $validated['selling_price'] = null;
        }

        $newspaper = Newspaper::create($validated);

        return redirect()->route('admin.newspapers.edit', $newspaper)
            ->with('success', 'Newspaper created. Upload images below.');
    }

    public function edit(Newspaper $newspaper)
    {
        $this->authorize('update', $newspaper);

        $newspaper->load(['images', 'files']);

        return view('admin.newspapers.edit', compact('newspaper'));
    }

    public function update(Request $request, Newspaper $newspaper)
    {
        $this->authorize('update', $newspaper);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'condition' => 'nullable|string|max:50',
            'sold_at' => 'nullable|date',
            'sold_price' => 'nullable|numeric|min:0',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');
        if (! empty($validated['sold_at'])) {
            $validated['for_sale'] = false;
            $validated['selling_price'] = null;
        }

        $newspaper->update($validated);

        return redirect()->route('admin.newspapers.edit', $newspaper)
            ->with('success', 'Newspaper updated!');
    }

    public function destroy(Newspaper $newspaper)
    {
        $this->authorize('delete', $newspaper);

        $newspaper->load(['images', 'files']);

        foreach ($newspaper->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('newspapers/'.$newspaper->id);

        $newspaper->media()->delete();
        $newspaper->delete();

        return redirect()->route('admin.newspapers.index')
            ->with('success', 'Newspaper deleted.');
    }
}
