<?php
// app/Http/Controllers/Admin/BookController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCover;
use App\Models\BookTopic;
use App\Models\BookSeries;
use App\Models\BookFile;
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

        $bookData = []; // of uit ISBN lookup
        return view('admin.books.create', [
            'bookData' => $bookData,
            'topics' => BookTopic::orderBy('name')->get(),
            'series' => BookSeries::orderBy('name')->get(),
            'covers' => BookCover::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
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
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:51200'], // 50MB, pas aan indien gewenst
            'authors' => 'required|string',
        ]);

        $data = $validated;
        unset($data['attachments']); // attachments niet in books tabel steken
        $book = Book::create($data);
        $this->syncAuthors($book, $request->input('authors'));

        // attachments (images + pdfs) -> BookFile
        if ($request->hasFile('attachments')) {
            $disk = Storage::disk('b2');

            foreach ($request->file('attachments') as $i => $uploaded) {
                $mime = $uploaded->getMimeType() ?? '';
                $ext  = strtolower($uploaded->getClientOriginalExtension() ?? '');

                $isPdf = str_contains($mime, 'pdf') || $ext === 'pdf';
                $type  = $isPdf ? 'pdf' : 'image';

                $prefix = trim(env('B2_PREFIX', ''), '/');
                $base   = $prefix ? "{$prefix}/books/{$book->id}" : "books/{$book->id}";

                $path = $disk->putFile($base, $uploaded);

                // sort_order achteraan
                $maxSort = (int) $book->files()->max('sort_order');
                $sort    = $maxSort + 1;

                $bookFile = BookFile::create([
                    'book_id'    => $book->id,
                    'type'       => $type,
                    'title'      => pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME),
                    'path'       => $path,
                    'is_main'    => false,
                    'sort_order' => $sort,
                ]);

                // eerste IMAGE main zetten (niet eerste attachment als dat een PDF is)
                if ($type === 'image' && $book->images()->count() === 1) {
                    $book->images()->update(['is_main' => false]);
                    $bookFile->update(['is_main' => true]);
                }
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
        $book->load([
            'authors',
            'images',
            'files',
        ]);

        return view('admin.books.edit', [
            'book' => $book,
            'topics' => BookTopic::orderBy('name')->get(),
            'series' => BookSeries::orderBy('name')->get(),
            'covers' => BookCover::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'bookData' => [], // optioneel, maar handig voor create/edit compat
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
        $book->load('files');
        $disk = Storage::disk('b2');

        foreach ($book->files as $file) {
            $p = $file->storagePath();
            if ($p) {
                $disk->delete($p);
            }
            $file->delete();


            $book->delete();
        }

        // Optioneel: cover-file delete als je nog een apart veld gebruikt dat een bestandspad bevat
        // if ($book->cover) {
        //     $coverPath = ltrim($book->cover, '/');
        //     if ($prefix && !str_starts_with($coverPath, $prefix . '/')) {
        //         $coverPath = "{$prefix}/{$coverPath}";
        //     }
        //     $disk->delete($coverPath);
        // }

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
