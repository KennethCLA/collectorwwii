<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MagazineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Magazine::class);

        $query = Magazine::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $magazines = $query->orderByDesc('created_at')->paginate(50);

        return view('admin.magazines.index', compact('magazines'));
    }

    public function create()
    {
        $this->authorize('create', Magazine::class);

        return view('admin.magazines.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Magazine::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'issue_number' => 'nullable|integer|min:1',
            'issue_year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $magazine = Magazine::create($validated);

        return redirect()->route('admin.magazines.edit', $magazine)
            ->with('success', 'Magazine created. Upload images below.');
    }

    public function edit(Magazine $magazine)
    {
        $this->authorize('update', $magazine);

        $magazine->load(['images', 'files']);

        return view('admin.magazines.edit', compact('magazine'));
    }

    public function update(Request $request, Magazine $magazine)
    {
        $this->authorize('update', $magazine);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'issue_number' => 'nullable|integer|min:1',
            'issue_year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $magazine->update($validated);

        return redirect()->route('admin.magazines.edit', $magazine)
            ->with('success', 'Magazine updated!');
    }

    public function destroy(Magazine $magazine)
    {
        $this->authorize('delete', $magazine);

        $magazine->load(['images', 'files']);

        foreach ($magazine->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('magazines/'.$magazine->id);

        $magazine->media()->delete();
        $magazine->delete();

        return redirect()->route('admin.magazines.index')
            ->with('success', 'Magazine deleted.');
    }
}
