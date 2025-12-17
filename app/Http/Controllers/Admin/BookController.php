<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCover;
use App\Models\BookTopic;
use App\Models\BookSeries;
use App\Models\BookImage;
use App\Models\Location;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(Request $request)
    {
        $query = Book::with(['authors', 'images']);

        if ($request->has('topic')) {
            $query->where('topic_id', $request->topic);
        }

        if ($request->has('series')) {
            $query->where('series_id', $request->series);
        }

        if ($request->has('cover')) {
            $query->where('cover_id', $request->cover);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('authors', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorteren op titel, auteur, of datum
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            switch ($sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'author_asc':
                    $query->orderBy(
                        Author::select('name')->whereColumn('authors.id', 'author_book.author_id'),
                        'asc'
                    );
                    break;
                case 'author_desc':
                    $query->orderBy(
                        Author::select('name')->whereColumn('authors.id', 'author_book.author_id'),
                        'desc'
                    );
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        $books = $query->paginate(500);

        return view('books.index', [
            'books' => $books,
            'topics' => BookTopic::orderBy('name', 'asc')->get(),
            'series' => BookSeries::orderBy('name', 'asc')->get(),
            'covers' => BookCover::orderBy('name', 'asc')->get(),
            'locations' => Location::orderBy('name', 'asc')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $isbn = trim((string) $request->input('isbn', ''));
        $isbnLookupFailed = false;
        $bookData = null;

        if ($isbn !== '') {
            $response = Http::timeout(5)->get('https://www.googleapis.com/books/v1/volumes', [
                'q' => "isbn:{$isbn}",
            ]);

            Log::info('Google Books API response', [
                'isbn' => $isbn,
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $info = $data['items'][0]['volumeInfo'] ?? null;

                if (is_array($info)) {
                    $publishedDate = $info['publishedDate'] ?? null;

                    $bookData = [
                        'isbn' => $isbn,
                        'title' => $info['title'] ?? null,
                        'subtitle' => $info['subtitle'] ?? null,
                        'authors' => !empty($info['authors']) ? implode(', ', $info['authors']) : null,
                        'publisher_name' => $info['publisher'] ?? null,
                        'copyright_year' => $publishedDate ? (int) substr((string) $publishedDate, 0, 4) : null,
                        'pages' => $info['pageCount'] ?? null,
                        'description' => $info['description'] ?? null,
                    ];
                } else {
                    $isbnLookupFailed = true;
                }
            } else {
                $isbnLookupFailed = true;
            }
        }

        return view('books.create', [
            'topics' => BookTopic::orderBy('name', 'asc')->get(),
            'series' => BookSeries::orderBy('name', 'asc')->get(),
            'covers' => BookCover::orderBy('name', 'asc')->get(),
            'locations' => Location::orderBy('name', 'asc')->get(),
            'isbn' => $isbn,
            'bookData' => $bookData,
            'isbnLookupFailed' => $isbnLookupFailed,
        ]);
    }

    public function store(Request $request)
    {
        $userId = (int) auth()->id();
        $key = "books:create:{$userId}";

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Too many uploads. Please try again later.');
        }

        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'isbn' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'topic_id' => 'nullable|exists:book_topics,id',
            'series_id' => 'nullable|exists:book_series,id',
            'cover_id' => 'nullable|exists:book_covers,id',
            'publisher_name' => 'nullable|string|max:255',
            'copyright_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'translator' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'pages' => 'nullable|integer|min:1',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif|max:8192',
            'authors' => 'required|string',
        ]);

        $book = Book::create($validated);
        $this->syncAuthors($book, $request->input('authors'));

        // extra afbeeldingen
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $path = $file->store("books/{$book->id}", 'b2');

                BookImage::create([
                    'book_id' => $book->id,
                    'image_path' => $path,
                    'is_main' => $index === 0, // eerste = main
                ]);
            }
        }

        // cover
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'b2');
            $book->update(['cover' => $coverPath]); // cover bevat enkel pad
        }

        return redirect()->route('books.index')->with('success', 'Book successfully added.');
    }

    public function show(Book $book)
    {
        // Laad de gerelateerde afbeeldingen
        $book->load('images');

        // Haal het vorige boek op
        $previousBook = Book::where('id', '<', $book->id)->orderBy('id', 'desc')->first();

        // Haal het volgende boek op
        $nextBook = Book::where('id', '>', $book->id)->orderBy('id', 'asc')->first();

        // Geef de variabelen door naar de view
        return view('books.show', compact('book', 'previousBook', 'nextBook'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', [
            'book' => $book,
            'topics' => BookTopic::orderBy('name', 'asc')->get(),
            'series' => BookSeries::orderBy('name', 'asc')->get(),
            'covers' => BookCover::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'topic_id' => 'nullable|exists:book_topics,id',
            'series_id' => 'nullable|exists:book_series,id',
            'cover_id' => 'nullable|exists:book_covers,id',
            'publisher_name' => 'nullable|string|max:255',
            'copyright_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'translator' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'pages' => 'nullable|integer|min:1',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'authors' => 'required|string',
        ]);

        $book->update($validated);
        $this->syncAuthors($book, $request->input('authors'));

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('b2')->delete($book->cover);
            }
            $coverPath = $request->file('cover')->store('covers', 'b2');
            $book->update(['cover' => $coverPath]);
        }

        return redirect()->route('books.index')->with('success', 'Book successfully updated.');
    }

    public function destroy(Book $book)
    {
        // verwijder alle images uit B2
        foreach ($book->images as $image) {
            Storage::disk('b2')->delete($image->image_path);
            $image->delete();
        }

        // verwijder cover uit B2
        if ($book->cover) {
            Storage::disk('b2')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted.');
    }

    private function syncAuthors(Book $book, string $authors)
    {
        $authorIds = collect(explode(',', $authors))
            ->map(fn($name) => Author::firstOrCreate(['name' => trim($name)])->id)
            ->toArray();

        $book->authors()->sync($authorIds);
    }
}
