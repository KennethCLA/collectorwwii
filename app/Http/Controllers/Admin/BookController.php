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
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

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
        // Only allow authenticated users
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        // Apply rate limiting (e.g., max 10 uploads per minute per user)
        if (app('cache')->has('book_upload_' . auth()->id())) {
            $uploads = app('cache')->increment('book_upload_' . auth()->id());
            if ($uploads > 10) {
                abort(429, 'Too many uploads. Please try again later.');
            }
        } else {
            app('cache')->put('book_upload_' . auth()->id(), 1, 60);
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
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif|max:8192',
            'authors' => 'required|string',
        ]);

        $book = Book::create($validated);
        $this->syncAuthors($book, $request->input('authors'));

        // extra afbeeldingen
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("books/{$book->id}", 'b2'); // bv. books/123/xxx.jpg
                BookImage::create([
                    'book_id'   => $book->id,
                    'image_path' => $path,         // bewaar RELATIEF pad
                    'is_main'   => false,
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
        if (!auth()->user()->isAdmin()) abort(403);

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
        if (!auth()->user()->isAdmin()) abort(403);

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
