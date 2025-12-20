<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\ItemImage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->with(['mainImage'])   // belangrijk voor image_url
            ->paginate(24)
            ->withQueryString();

        return view('items.index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load(['images']);

        $previousItem = Item::where('id', '<', $item->id)->orderBy('id', 'desc')->first();
        $nextItem = Item::where('id', '>', $item->id)->orderBy('id', 'asc')->first();

        return view('items.show', compact('item', 'previousItem', 'nextItem'));
    }

    public function store(Request $request)
    {
        Log::info('ITEM STORE HIT', [
            'user_id' => auth()->id(),
            'payload_keys' => array_keys($request->all()),
        ]);

        $userId = (int) auth()->id();
        $key = "items:create:{$userId}";

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Too many uploads. Please try again later.');
        }
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'category_id' => 'nullable|exists:item_categories,id',
            'origin_id' => 'nullable|exists:item_origins,id',
            'nationality_id' => 'nullable|exists:item_nationalities,id',
            'organization_id' => 'nullable|exists:item_organizations,id',

            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchase_location' => 'nullable|string|max:255',

            'notes' => 'nullable|string',
            'storage_location' => 'nullable|string|max:255',

            'current_price' => 'nullable|numeric|min:0',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',

            // Meerdere uploads:
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        $item = Item::create($validated);

        // Images
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                // store() retourneert bv: items/{id}/randomname.jpg
                $path = $file->store("items/{$item->id}", 'b2');

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $path,        // RELATIEF pad bewaren
                    'is_main' => $index === 0,    // eerste = main
                ]);
            }
        }

        return redirect()->route('items.index')->with('success', 'Item successfully added.');
    }
}
