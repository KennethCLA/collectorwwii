<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ForSaleController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all'); // all|books|items
        $q    = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'title_asc');

        $booksQuery = \App\Models\Book::query()
            ->where('for_sale', true)
            ->when($q !== '', fn($qb) => $qb->where('title', 'like', "%{$q}%"))
            ->with(['images', 'authors'])
            ->select(['id', 'title', 'subtitle', 'selling_price', 'created_at']);

        $itemsQuery = \App\Models\Item::query()
            ->where('for_sale', true)
            ->when($q !== '', fn($qi) => $qi->where('title', 'like', "%{$q}%"))
            ->with(['images'])
            ->select(['id', 'title', 'selling_price', 'created_at']);

        $books = collect();
        $items = collect();

        if ($type === 'all' || $type === 'books') {
            $books = $booksQuery->get()->map(function ($b) {
                $img = $b->images->first();

                return [
                    'type'       => 'book',
                    'title'      => $b->title,
                    'subtitle'   => $b->subtitle,
                    'authors'    => $b->authors->pluck('name')->values()->all(),
                    'price'      => $b->selling_price,
                    'created_at' => $b->created_at,
                    'image'      => $img?->url() ?? ($img->url ?? null),
                    'url'        => route('books.show', $b),
                ];
            });
        }

        if ($type === 'all' || $type === 'items') {
            $items = $itemsQuery->get()->map(function ($i) {
                $img = $i->images->first();
                return [
                    'type' => 'item',
                    'title' => $i->title,
                    'price' => $i->selling_price,
                    'created_at' => $i->created_at,
                    'image' => $img?->url() ?? ($img->url ?? null),
                    'url' => route('items.show', $i),
                ];
            });
        }

        $forSale = $books->concat($items);

        // sort
        $forSale = match ($sort) {
            'title_desc' => $forSale->sortByDesc(fn($x) => mb_strtolower($x['title'])),
            'price_asc' => $forSale->sortBy(fn($x) => $x['price'] ?? 999999999),
            'price_desc' => $forSale->sortByDesc(fn($x) => $x['price'] ?? -1),
            'created_at_asc' => $forSale->sortBy(fn($x) => $x['created_at'] ?? now()),
            'created_at_desc' => $forSale->sortByDesc(fn($x) => $x['created_at'] ?? now()),
            default => $forSale->sortBy(fn($x) => mb_strtolower($x['title'])),
        };

        $forSale = $forSale->values();

        // paginate collection (simple manual)
        $perPage = 24;
        $page = (int) ($request->query('page', 1));
        $offset = max(0, ($page - 1) * $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $forSale->slice($offset, $perPage)->values(),
            $forSale->count(),
            $perPage,
            $page,
            [
                'path' => route('for-sale.index'),
                'query' => $request->query(),
            ]
        );

        return view('for-sale.index', [
            'forSale' => $paginator,
        ]);
    }
}
