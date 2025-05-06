<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Item;
use App\Models\Newspaper;
use App\Models\Magazine;
use App\Models\Banknote;
use App\Models\Coin;
use App\Models\Postcard;
use App\Models\Stamp;

class UserController extends Controller
{
    public function index()
    {
        $book = Book::find(1);  // Voorbeeld om een boek op te halen.
        $item = Item::find(1);  // Voorbeeld om een item op te halen.
        $newspaper = Newspaper::find(1);  // Voorbeeld om een krant op te halen.
        $magazine = Magazine::find(1);  // Voorbeeld om een tijdschrift op te halen.
        $banknote = Banknote::find(1);  // Voorbeeld om een banknot op te halen.
        $coin = Coin::find(1);  // Voorbeeld om een munt op te halen.
        $postcard = Postcard::find(1);  // Voorbeeld om een postkaart op te halen.
        $stamp = Stamp::find(1);  // Voorbeeld om een stempel op te halen.
        $user = auth()->user();
        return view('profile.index', compact('book', 'item', 'newspaper', 'magazine', 'banknote', 'coin', 'postcard', 'stamp', 'user'));
    }
}