<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemNationality;
use App\Models\ItemOrganization;
use App\Models\ItemOrigin;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // 1. Base query: haal items op met hun relaties
        $query = Item::query();

        // 2. Filtering (op basis van request-parameters)
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('origin')) {
            $query->where('origin_id', $request->origin);
        }

        if ($request->has('nationality')) {
            $query->where('nationality_id', $request->nationality);
        }

        if ($request->has('organization')) {
            $query->where('organization_id', $request->organization);
        }

        // 3. Zoeken (bijvoorbeeld op de titel)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // 4. Sorteerfunctionaliteit
        //    Voorbeeld van enkele opties: titel, aanmaakdatum, enz.
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            switch ($sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                // Je kunt hier extra cases toevoegen, bijvoorbeeld sorteren op 'category' of 'origin' etc.
                default:
                    // Geen extra sortering
                    break;
            }
        }

        // 5. Paginatie
        //    Pas het getal (10) aan naar wens. Bijvoorbeeld 20, 50, 100...
        $items = $query->paginate(500);

        // 6. Retourneer de view met de resultaten en de benodigde filters
        return view('items.index', [
            'items' => $items,
            'categories' => ItemCategory::all(),
            'origins' => ItemOrigin::all(),
            'nationalities' => ItemNationality::all(),
            'organizations' => ItemOrganization::all(),
        ]);
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'origin_id' => 'nullable|exists:item_origins,id',
            'nationality_id' => 'nullable|exists:item_nationalities,id',
            'organization_id' => 'nullable|exists:item_organizations,id',
            'images.*' => 'image|max:2048',
        ]);

        $item = Item::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $itemFolder = 'items/' . $item->id;
                $filename = uniqid() . '.' . $image->extension();
                $path = $image->storeAs($itemFolder, $filename, 'public');

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $path,
                    'is_main' => $index === 0, // Eerste afbeelding wordt hoofdafbeelding
                ]);
            }
        }

        return redirect()->route('items.index');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'origin_id' => 'nullable|exists:item_origins,id',
            'nationality_id' => 'nullable|exists:item_nationalities,id',
            'organization_id' => 'nullable|exists:item_organizations,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item->update($validated);

        // Check of het item al een hoofdafbeelding heeft
        $hasMainImage = $item->images()->where('is_main', true)->exists();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $itemFolder = 'items/' . $item->id;
                $filename = uniqid() . '.' . $image->extension();
                $path = $image->storeAs($itemFolder, $filename, 'public');

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $path,
                    'is_main' => !$hasMainImage && $index === 0, // Enkel 1x is_main
                ]);

                $hasMainImage = true;
            }
        }

        return redirect()->route('items.index')->with('success', 'Item updated!');
    }

    public function destroy(Item $item)
    {
        // Verwijder alle afbeeldingen uit de storage
        Storage::disk('public')->deleteDirectory('items/' . $item->id);

        // Verwijder item en gerelateerde database records
        $item->images()->delete();
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item verwijderd!');
    }
}
