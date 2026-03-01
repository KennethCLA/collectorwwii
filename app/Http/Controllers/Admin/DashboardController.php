<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banknote;
use App\Models\Book;
use App\Models\Coin;
use App\Models\Item;
use App\Models\Magazine;
use App\Models\Newspaper;
use App\Models\Postcard;
use App\Models\Stamp;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfWeek = now()->startOfWeek();

        $sections = [
            [
                'name' => 'Books',
                'total' => Book::count(),
                'for_sale' => Book::where('for_sale', true)->count(),
                'created_this_week' => Book::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Items',
                'total' => Item::count(),
                'for_sale' => Item::where('for_sale', true)->count(),
                'created_this_week' => Item::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Banknotes',
                'total' => Banknote::count(),
                'for_sale' => Banknote::where('for_sale', true)->count(),
                'created_this_week' => Banknote::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Coins',
                'total' => Coin::count(),
                'for_sale' => Coin::where('for_sale', true)->count(),
                'created_this_week' => Coin::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Magazines',
                'total' => Magazine::count(),
                'for_sale' => Magazine::where('for_sale', true)->count(),
                'created_this_week' => Magazine::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Newspapers',
                'total' => Newspaper::count(),
                'for_sale' => Newspaper::where('for_sale', true)->count(),
                'created_this_week' => Newspaper::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Postcards',
                'total' => Postcard::count(),
                'for_sale' => Postcard::where('for_sale', true)->count(),
                'created_this_week' => Postcard::where('created_at', '>=', $startOfWeek)->count(),
            ],
            [
                'name' => 'Stamps',
                'total' => Stamp::count(),
                'for_sale' => Stamp::where('for_sale', true)->count(),
                'created_this_week' => Stamp::where('created_at', '>=', $startOfWeek)->count(),
            ],
        ];

        // purchase_price column: Book, Item, Magazine, Newspaper
        // purchasing_price column: Banknote, Coin, Postcard, Stamp
        $totalInvested =
            Book::sum('purchase_price') +
            Item::sum('purchase_price') +
            Magazine::sum('purchase_price') +
            Newspaper::sum('purchase_price') +
            Banknote::sum('purchasing_price') +
            Coin::sum('purchasing_price') +
            Postcard::sum('purchasing_price') +
            Stamp::sum('purchasing_price');

        $totalForSaleValue =
            Book::where('for_sale', true)->sum('selling_price') +
            Item::where('for_sale', true)->sum('selling_price') +
            Magazine::where('for_sale', true)->sum('selling_price') +
            Newspaper::where('for_sale', true)->sum('selling_price') +
            Banknote::where('for_sale', true)->sum('selling_price') +
            Coin::where('for_sale', true)->sum('selling_price') +
            Postcard::where('for_sale', true)->sum('selling_price') +
            Stamp::where('for_sale', true)->sum('selling_price');

        $totalSoldValue =
            Book::whereNotNull('sold_at')->sum('sold_price') +
            Item::whereNotNull('sold_at')->sum('sold_price') +
            Magazine::whereNotNull('sold_at')->sum('sold_price') +
            Newspaper::whereNotNull('sold_at')->sum('sold_price') +
            Banknote::whereNotNull('sold_at')->sum('sold_price') +
            Coin::whereNotNull('sold_at')->sum('sold_price') +
            Postcard::whereNotNull('sold_at')->sum('sold_price') +
            Stamp::whereNotNull('sold_at')->sum('sold_price');

        $totalSoldCount =
            Book::whereNotNull('sold_at')->count() +
            Item::whereNotNull('sold_at')->count() +
            Magazine::whereNotNull('sold_at')->count() +
            Newspaper::whereNotNull('sold_at')->count() +
            Banknote::whereNotNull('sold_at')->count() +
            Coin::whereNotNull('sold_at')->count() +
            Postcard::whereNotNull('sold_at')->count() +
            Stamp::whereNotNull('sold_at')->count();

        return view('admin.dashboard', [
            'sections' => $sections,
            'startOfWeek' => $startOfWeek,
            'totalInvested' => $totalInvested,
            'totalForSaleValue' => $totalForSaleValue,
            'totalSoldValue' => $totalSoldValue,
            'totalSoldCount' => $totalSoldCount,
        ]);
    }
}
