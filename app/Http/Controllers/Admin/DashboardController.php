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

        return view('admin.dashboard', [
            'sections' => $sections,
            'startOfWeek' => $startOfWeek,
        ]);
    }
}
