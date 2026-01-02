<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemNationality;
use App\Models\ItemOrigin;
use App\Models\ItemOrganization;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = ItemCategory::orderBy('name')->get();
        $nationalities = ItemNationality::orderBy('name')->get();
        $origins = ItemOrigin::orderBy('name')->get();
        $organizations = ItemOrganization::orderBy('name')->get();

        $itemsQuery = Item::query()
            ->with(['mainImage', 'category', 'nationality', 'origin', 'organization']);

        // SEARCH (title + description)
        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $itemsQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        // FILTERS
        if ($request->filled('category_id')) {
            $itemsQuery->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('nationality_id')) {
            $itemsQuery->where('nationality_id', $request->integer('nationality_id'));
        }
        if ($request->filled('origin_id')) {
            $itemsQuery->where('origin_id', $request->integer('origin_id'));
        }
        if ($request->filled('organization_id')) {
            $itemsQuery->where('organization_id', $request->integer('organization_id'));
        }

        // STATUS (for_sale)
        if ($request->filled('for_sale')) {
            // for_sale=1 => true, for_sale=0 => false
            $itemsQuery->where('for_sale', (bool) ((int) $request->input('for_sale')));
        }

        // SORT
        $sort = $request->input('sort', 'created_at_asc');

        switch ($sort) {
            case 'title_asc':
                $itemsQuery->orderBy('title', 'asc')->orderBy('id', 'desc');
                break;

            case 'title_desc':
                $itemsQuery->orderBy('title', 'desc')->orderBy('id', 'desc');
                break;

            case 'created_at_asc': // Newest first
                $itemsQuery->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;

            case 'created_at_desc': // Oldest first
                $itemsQuery->orderBy('created_at', 'asc')->orderBy('id', 'asc');
                break;

            default:
                // veilige fallback
                $itemsQuery->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                $sort = 'created_at_asc';
                break;
        }

        $items = $itemsQuery->paginate(504)->withQueryString();

        return view('items.index', compact(
            'items',
            'categories',
            'nationalities',
            'origins',
            'organizations',
            'sort'
        ));
    }

    public function show(Item $item)
    {
        $item->load([
            'images',
            'mainImage',
            'files',
            'category',
            'nationality',
            'origin',
            'organization',
        ]);

        $previousItem = Item::where('id', '<', $item->id)->orderByDesc('id')->first();
        $nextItem     = Item::where('id', '>', $item->id)->orderBy('id')->first();

        return view('items.show', compact('item', 'previousItem', 'nextItem'));
    }
}
