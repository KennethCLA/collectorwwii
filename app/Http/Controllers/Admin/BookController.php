<?php
// app/Http/Controllers/Admin/BookController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCover;
use App\Models\BookTopic;
use App\Models\BookSeries;
use App\Models\MediaFile;
use App\Models\Location;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $bookData = [];

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

                if ((int) data_get($data, 'totalItems', 0) < 1) {
                    $isbnLookupFailed = true;
                } else {
                    $info = data_get($data, 'items.0.volumeInfo');

                    if (is_array($info)) {
                        $publishedDate = data_get($info, 'publishedDate');

                        $bookData = [
                            'isbn' => $isbn,
                            'title' => data_get($info, 'title'),
                            'subtitle' => data_get($info, 'subtitle'),
                            'authors' => ($a = data_get($info, 'authors')) ? implode(', ', (array)$a) : null,
                            'publisher_name' => data_get($info, 'publisher'),
                            'copyright_year' => $publishedDate ? (int) substr((string) $publishedDate, 0, 4) : null,
                            'pages' => data_get($info, 'pageCount'),
                            'description' => data_get($info, 'description'),
                        ];
                    } else {
                        $isbnLookupFailed = true;
                    }
                }
            } else {
                $isbnLookupFailed = true;
            }
        }

        return view('admin.books.create', [
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

        // 1) Validate eerst (zodat invalid submits geen rate limiter verbranden)
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',

            'title_first_edition' => 'nullable|string|max:255',
            'subtitle_first_edition' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'translator' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',

            'copyright_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'issue_number' => 'nullable|string|max:255',
            'issue_year' => 'nullable|integer|min:1000|max:' . date('Y'),

            'topic_id' => 'nullable|exists:book_topics,id',
            'series_id' => 'nullable|exists:book_series,id',
            'series_number' => 'nullable|string|max:255',
            'cover_id' => 'nullable|exists:book_covers,id',

            'copyright_year_first_issue' => 'nullable|integer|min:1000|max:' . date('Y'),
            'publisher_name' => 'nullable|string|max:255',
            'publisher_first_issue' => 'nullable|string|max:255',

            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'storage_location' => 'nullable|string|max:255',

            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',

            'weight' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'thickness' => 'nullable|integer|min:0',

            'authors' => 'required|string',

            // Uploads (optioneel)
            'images'   => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp', 'max:51200'],
            'pdfs'     => ['nullable', 'array'],
            'pdfs.*'   => ['file', 'mimetypes:application/pdf', 'max:51200'],

            'main_image_index' => ['nullable', 'integer', 'min:0'],
            'after_save' => ['nullable', 'in:show,create,index'],
        ]);

        $validated['isbn'] = trim((string)($validated['isbn'] ?? ''));
        $validated['isbn'] = $validated['isbn'] === '' ? null : $validated['isbn'];

        // 2) Rate limit check pas na validatie
        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Too many uploads. Please try again later.');
        }
        RateLimiter::hit($key, 60);

        // 3) Normaliseer checkbox
        $validated['for_sale'] = (bool) $request->boolean('for_sale');
        if (!$validated['for_sale']) {
            $validated['selling_price'] = null;
        }

        // 4) Authors cleanup (voorkom lege authors)
        $authorNames = $this->parseAuthorNames((string) $validated['authors']);
        if (count($authorNames) < 1) {
            throw ValidationException::withMessages([
                'authors' => 'Please provide at least one author name.',
            ]);
        }

        // 5) Book + media + pivot in transaction (DB-consistent)
        $disk = 'b2';
        $uploadedForCleanup = []; // [ [disk, path], ... ]

        try {
            $book = DB::transaction(function () use ($validated, $request, $authorNames, $disk, &$uploadedForCleanup) {
                $data = $validated;
                unset($data['images'], $data['pdfs'], $data['authors'], $data['main_image_index'], $data['after_save']);

                /** @var \App\Models\Book $book */
                $book = Book::create($data);

                $this->syncAuthorsByNames($book, $authorNames);

                $folderBase = "books/{$book->id}";

                // Images
                $imageUploads = $request->file('images', []);
                $mainIndex = (int) $request->input('main_image_index', 0);

                if (count($imageUploads) > 0) {
                    $mainIndex = max(0, min($mainIndex, count($imageUploads) - 1));
                    $nextSort = 0;

                    foreach ($imageUploads as $i => $uploaded) {
                        $filename = (string) \Illuminate\Support\Str::uuid() . '.' . $uploaded->extension();
                        $path = $uploaded->storeAs($folderBase, $filename, $disk);
                        $uploadedForCleanup[] = [$disk, $path];

                        $book->media()->create([
                            'disk'          => $disk,
                            'path'          => $path,
                            'mime_type'     => $uploaded->getMimeType(),
                            'size'          => $uploaded->getSize(),
                            'original_name' => $uploaded->getClientOriginalName(),
                            'collection'    => 'images',
                            'is_main'       => ($i === $mainIndex),
                            'sort_order'    => $nextSort++,
                        ]);
                    }

                    // Safety: force EXACTLY 1 main image (also fixes "0 main" case)
                    $imagesQuery = MediaFile::where('attachable_type', Book::class)
                        ->where('attachable_id', $book->id)
                        ->where('collection', 'images');

                    $mainCount = (int) (clone $imagesQuery)->where('is_main', 1)->count();

                    if ($mainCount === 0) {
                        // No main set -> pick first by sort_order/id
                        $first = (clone $imagesQuery)->orderBy('sort_order')->orderBy('id')->first();

                        (clone $imagesQuery)->update(['is_main' => 0]);

                        if ($first) {
                            $first->update(['is_main' => 1]);
                        }
                    } elseif ($mainCount > 1) {
                        // Too many mains -> keep newest main only
                        (clone $imagesQuery)->where('is_main', 1)
                            ->orderBy('id', 'desc')
                            ->skip(1)
                            ->update(['is_main' => 0]);
                    }
                }

                // PDFs
                $pdfUploads = $request->file('pdfs', []);
                foreach ($pdfUploads as $uploaded) {
                    $filename = (string) \Illuminate\Support\Str::uuid() . '.' . $uploaded->extension();
                    $path = $uploaded->storeAs($folderBase, $filename, $disk);
                    $uploadedForCleanup[] = [$disk, $path];

                    $book->media()->create([
                        'disk'          => $disk,
                        'path'          => $path,
                        'mime_type'     => $uploaded->getMimeType(),
                        'size'          => $uploaded->getSize(),
                        'original_name' => $uploaded->getClientOriginalName(),
                        'collection'    => 'files',
                        'is_main'       => false,
                        'sort_order'    => null,
                    ]);
                }

                return $book;
            });
        } catch (\Throwable $e) {
            // best-effort cleanup van reeds geuploade bestanden
            foreach ($uploadedForCleanup as [$d, $p]) {
                try {
                    Storage::disk($d)->delete($p);
                } catch (\Throwable $ignore) {
                }
            }
            throw $e;
        }

        $after = $request->input('after_save', 'show');

        return match ($after) {
            'create' => redirect()
                ->route('admin.books.create')
                ->with('success', 'Book saved. You can add another one.'),
            'index' => redirect()
                ->route('admin.books.index')
                ->with('success', 'Book successfully added.'),
            default => redirect()
                ->route('admin.books.show', $book)
                ->with('success', 'Book successfully added.'),
        };
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
            'isbn' => 'nullable|string|max:255',
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

        $validated['isbn'] = trim((string)($validated['isbn'] ?? ''));
        $validated['isbn'] = $validated['isbn'] === '' ? null : $validated['isbn'];

        $authorsRaw = (string) $validated['authors'];
        unset($validated['authors'], $validated['cover']);

        $book->update($validated);

        $names = $this->parseAuthorNames($authorsRaw);
        $this->syncAuthorsByNames($book, $names);

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('b2')->delete($book->cover);
            }
            $coverPath = $request->file('cover')->store('covers', 'b2');
            $book->update(['cover' => $coverPath]);
        }

        return redirect()->route('admin.books.show', $book)->with('success', 'Book successfully updated.');
    }

    public function destroy(Book $book)
    {
        $book->load('media');
        $disk = Storage::disk('b2');

        foreach ($book->media as $file) {
            if ($file->path) {
                $disk->delete($file->path);
            }
            $file->delete();
        }

        // cover path delete (als je cover echt als bestandspad gebruikt)
        if ($book->cover) {
            $disk->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted.');
    }

    private function parseAuthorNames(string $authors): array
    {
        return collect(explode(',', $authors))
            ->map(fn($n) => trim($n))
            ->filter(fn($n) => $n !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function syncAuthorsByNames(Book $book, array $authorNames): void
    {
        $authorIds = collect($authorNames)
            ->map(fn($name) => Author::firstOrCreate(['name' => $name])->id)
            ->all();

        $book->authors()->sync($authorIds);
    }
}
