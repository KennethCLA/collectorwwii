<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Banknote;
use App\Models\Book;
use App\Models\Coin;
use App\Models\Item;
use App\Models\Magazine;
use App\Models\Newspaper;
use App\Models\Postcard;
use App\Models\Stamp;

use App\Policies\BanknotePolicy;
use App\Policies\BookPolicy;
use App\Policies\CoinPolicy;
use App\Policies\ItemPolicy;
use App\Policies\MagazinePolicy;
use App\Policies\NewspaperPolicy;
use App\Policies\PostcardPolicy;
use App\Policies\StampPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Banknote::class, BanknotePolicy::class);
        Gate::policy(Coin::class, CoinPolicy::class);
        Gate::policy(Magazine::class, MagazinePolicy::class);
        Gate::policy(Newspaper::class, NewspaperPolicy::class);
        Gate::policy(Postcard::class, PostcardPolicy::class);
        Gate::policy(Stamp::class, StampPolicy::class);
    }
}
