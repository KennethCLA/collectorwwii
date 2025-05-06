<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCover;
use App\Models\BookTopic;
use App\Models\BookSeries;
use App\Models\BookImage;
use App\Models\Location;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('authors');

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
        // Verkrijg ISBN uit request als deze aanwezig is
        $isbn = $request->input('isbn');
        $bookData = null;

        // Als er een ISBN is, probeer dan boekgegevens op te halen via een externe API
        if ($isbn) {
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => "isbn:{$isbn}",
            ]);

            dd($response->json()); // Debugging: kijk wat de API teruggeeft

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['items'][0]['volumeInfo'])) {
                    $bookData = $data['items'][0]['volumeInfo'];
                }
            }
        }

        // Haal andere gegevens op zoals topics, series, covers, etc.
        return view('books.create', [
            'topics' => BookTopic::orderBy('name', 'asc')->get(),
            'series' => BookSeries::orderBy('name', 'asc')->get(),
            'covers' => BookCover::orderBy('name', 'asc')->get(),
            'locations' => Location::orderBy('name', 'asc')->get(),
            'bookData' => $bookData,  // Dit is de informatie die we ophalen op basis van het ISBN
        ]);
    }

    public function store(Request $request)
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
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'authors' => 'required|string',
        ]);

        $book = Book::create($validated);

        $this->syncAuthors($book, $request->input('authors'));

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
            $book->update(['cover' => $coverPath]);
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
        // Controleer of de gebruiker een admin is
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('books.edit', [
            'book' => $book,
            'topics' => BookTopic::orderBy('name', 'asc')->get(),
            'series' => BookSeries::orderBy('name', 'asc')->get(),
            'covers' => BookCover::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

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
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'authors' => 'required|string',
        ]);

        $book->update($validated);

        $this->syncAuthors($book, $request->input('authors'));

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
            $book->update(['cover' => $coverPath]);
        }

        return redirect()->route('books.index')->with('success', 'Book successfully updated.');
    }

    public function destroy(Book $book)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $book->delete();
        return redirect()->route('books.index');
    }

    private function syncAuthors(Book $book, string $authors)
    {
        $authorIds = collect(explode(',', $authors))
            ->map(fn($name) => Author::firstOrCreate(['name' => trim($name)])->id)
            ->toArray();

        $book->authors()->sync($authorIds);
    }
}