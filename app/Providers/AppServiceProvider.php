<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Book;
use App\Models\Item;
use App\Policies\BookPolicy;
use App\Policies\ItemPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
    }
}
