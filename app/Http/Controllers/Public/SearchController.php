<?php

// app/Http/Controllers/Public/SearchController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Banknote;
use App\Models\Book;
use App\Models\Coin;
use App\Models\Item;
use App\Models\Magazine;
use App\Models\Newspaper;
use App\Models\Postcard;
use App\Models\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    private const LIMIT = 8;

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return view('search.results', [
                'q'      => $q,
                'groups' => [],
                'total'  => 0,
            ]);
        }

        $groups = array_filter(
            $this->buildGroups($q),
            fn($g) => $g['results']->isNotEmpty()
        );

        $total = array_sum(array_map(fn($g) => $g['results']->count(), $groups));

        return view('search.results', compact('q', 'groups', 'total'));
    }

    // -------------------------------------------------------------------------

    private function buildGroups(string $q): array
    {
        return [
            'books'      => $this->books($q),
            'items'      => $this->items($q),
            'magazines'  => $this->magazines($q),
            'newspapers' => $this->newspapers($q),
            'banknotes'  => $this->banknotes($q),
            'coins'      => $this->coins($q),
            'postcards'  => $this->postcards($q),
            'stamps'     => $this->stamps($q),
        ];
    }

    private function books(string $q): array
    {
        $results = config('collector.enabled_sections.books')
            ? Book::with('mainImage', 'authors')
                ->where(fn($w) => $w
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('subtitle', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%")
                    ->orWhere('publisher_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$q}%"))
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($b) => $this->hit(
                    $b->title,
                    $b->authors->pluck('name')->implode(', '),
                    $b->mainImage?->url(),
                    route('books.show', $b)
                ))
            : collect();

        return ['label' => 'Books', 'results' => $results];
    }

    private function items(string $q): array
    {
        $results = config('collector.enabled_sections.items')
            ? Item::with('mainImage', 'category')
                ->where(fn($w) => $w
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($i) => $this->hit(
                    $i->title,
                    $i->category?->name,
                    $i->mainImage?->url(),
                    route('items.show', $i)
                ))
            : collect();

        return ['label' => 'Items', 'results' => $results];
    }

    private function magazines(string $q): array
    {
        $results = config('collector.enabled_sections.magazines')
            ? Magazine::with('mainImage', 'series')
                ->where(fn($w) => $w
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('subtitle', 'like', "%{$q}%")
                    ->orWhere('publisher', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($m) => $this->hit(
                    $m->title,
                    collect([$m->series?->name, $m->issue_year])->filter()->implode(' · '),
                    $m->mainImage?->url(),
                    route('magazines.show', $m)
                ))
            : collect();

        return ['label' => 'Magazines', 'results' => $results];
    }

    private function newspapers(string $q): array
    {
        $results = config('collector.enabled_sections.newspapers')
            ? Newspaper::with('mainImage', 'series')
                ->where(fn($w) => $w
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('publisher', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($n) => $this->hit(
                    $n->title,
                    collect([$n->series?->name, $n->publication_date?->format('Y')])->filter()->implode(' · '),
                    $n->mainImage?->url(),
                    route('newspapers.show', $n)
                ))
            : collect();

        return ['label' => 'Newspapers', 'results' => $results];
    }

    private function banknotes(string $q): array
    {
        $results = config('collector.enabled_sections.banknotes')
            ? Banknote::with('mainImage', 'country')
                ->where(fn($w) => $w
                    ->where('variation', 'like', "%{$q}%")
                    ->orWhere('special_features', 'like', "%{$q}%")
                    ->orWhere('number_jaeger', 'like', "%{$q}%")
                    ->orWhereHas('country', fn($c) => $c->where('name', 'like', "%{$q}%"))
                    ->when(is_numeric($q), fn($w2) => $w2->orWhere('year', (int) $q))
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($b) => $this->hit(
                    collect([$b->country?->name, $b->year])->filter()->implode(' · '),
                    $b->variation,
                    $b->mainImage?->url(),
                    route('banknotes.show', $b)
                ))
            : collect();

        return ['label' => 'Banknotes', 'results' => $results];
    }

    private function coins(string $q): array
    {
        $results = config('collector.enabled_sections.coins')
            ? Coin::with('mainImage', 'country')
                ->where(fn($w) => $w
                    ->whereHas('country', fn($c) => $c->where('name', 'like', "%{$q}%"))
                    ->when(is_numeric($q), fn($w2) => $w2->orWhere('year', (int) $q))
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($c) => $this->hit(
                    collect([$c->country?->name, $c->year])->filter()->implode(' · '),
                    null,
                    $c->mainImage?->url(),
                    route('coins.show', $c)
                ))
            : collect();

        return ['label' => 'Coins', 'results' => $results];
    }

    private function postcards(string $q): array
    {
        $results = config('collector.enabled_sections.postcards')
            ? Postcard::with('mainImage', 'country', 'postcardType')
                ->where(fn($w) => $w
                    ->where('occasion', 'like', "%{$q}%")
                    ->orWhere('special_features', 'like', "%{$q}%")
                    ->orWhere('michel_number', 'like', "%{$q}%")
                    ->orWhereHas('country', fn($c) => $c->where('name', 'like', "%{$q}%"))
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($p) => $this->hit(
                    collect([$p->country?->name, $p->year])->filter()->implode(' · '),
                    collect([$p->postcardType?->name, $p->occasion])->filter()->implode(' · '),
                    $p->mainImage?->url(),
                    route('postcards.show', $p)
                ))
            : collect();

        return ['label' => 'Postcards', 'results' => $results];
    }

    private function stamps(string $q): array
    {
        $results = config('collector.enabled_sections.stamps')
            ? Stamp::with('mainImage', 'country', 'stampType')
                ->where(fn($w) => $w
                    ->where('michel_number', 'like', "%{$q}%")
                    ->orWhere('yvert_tellier_number', 'like', "%{$q}%")
                    ->orWhereHas('country', fn($c) => $c->where('name', 'like', "%{$q}%"))
                    ->when(is_numeric($q), fn($w2) => $w2->orWhere('year', (int) $q))
                )
                ->limit(self::LIMIT)->get()
                ->map(fn($s) => $this->hit(
                    collect([$s->country?->name, $s->year])->filter()->implode(' · '),
                    collect([$s->stampType?->name, $s->michel_number ? 'Michel ' . $s->michel_number : null])->filter()->implode(' · '),
                    $s->mainImage?->url(),
                    route('stamps.show', $s)
                ))
            : collect();

        return ['label' => 'Stamps', 'results' => $results];
    }

    // -------------------------------------------------------------------------

    private function hit(string $title, ?string $subtitle, ?string $thumb, string $url): array
    {
        return [
            'title'    => $title,
            'subtitle' => $subtitle,
            'thumb'    => $thumb ?? asset('images/error-image-not-found.png'),
            'url'      => $url,
        ];
    }
}
