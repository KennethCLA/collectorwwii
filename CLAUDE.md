# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CollectorWWII is a Laravel 11 web application for cataloguing and displaying a WWII-related collection (books, items, banknotes, coins, magazines, newspapers, postcards, stamps). It has a public-facing side and an authenticated admin panel.

## Commands

### Development

Start all services together (server + queue + Vite):
```bash
composer run dev
```

Or individually:
```bash
php artisan serve
npm run dev
php artisan queue:listen --tries=1
```

### Build

```bash
npm run build
```

### Testing

Run all tests (uses Pest):
```bash
php artisan test
```

Run a specific test file:
```bash
php artisan test tests/Feature/AdminBookStoreUpdateTest.php
```

Run a specific test by name filter:
```bash
php artisan test --filter="test name here"
```

Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`), so no real database needed.

### Code Style

```bash
./vendor/bin/pint
```

### Database

```bash
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture

### Routing

- `routes/web.php` — public routes (home/blog, books, items, sections)
- `routes/admin.php` — all admin routes, prefixed `admin/`, protected by auth middleware. Registered as a route group in `app/Providers/AppServiceProvider.php` or bootstrap.

### Two-panel structure

**Public side** (`App\Http\Controllers\Public\`): read-only views for books, items, blog, for-sale, and dynamic section pages. Sections are driven by `config/collector.php` → `enabled_sections`.

**Admin side** (`App\Http\Controllers\Admin\`): full CRUD for all collection types. Access requires `role_id === 1` (enforced via Laravel Policies in `app/Policies/`).

### Models and key relationships

- `Book` — has many `Author` (via `book_authors` pivot, synced by comma-separated name input), belongs to `BookSeries`, `BookCover`, `BookTopic`, `Location`, `Origin`. Uses soft deletes.
- `Item` — belongs to `ItemCategory`, `ItemNationality`, `ItemOrganization`, `Origin`. Uses soft deletes.
- `Magazine` — belongs to `MagazineSeries` (nullable). `series_id` FK on `magazines`.
- `Newspaper` — belongs to `NewspaperSeries` (nullable). `series_id` FK on `newspapers`.
- `MediaFile` — polymorphic (`attachable_type` / `attachable_id`). Used for both `books` and `items`. Has `collection` field (`images` or `files`), `is_main` flag, and `sort_order`. All files stored on Backblaze B2 (`disk = 'b2'`).

### Tree-structured lookup tables

Five existing lookup tables and two new ones support a self-referential `parent_id` hierarchy:

| Table | Used by | Model |
|---|---|---|
| `book_topics` | `books.topic_id` | `BookTopic` |
| `item_categories` | `items.category_id` | `ItemCategory` |
| `item_organizations` | `items.organization_id` | `ItemOrganization` |
| `locations` | books, banknotes, coins, postcards, stamps | `Location` |
| `origins` | `books.origin_id`, `items.origin_id` | `Origin` |
| `magazine_series` | `magazines.series_id` | `MagazineSeries` |
| `newspaper_series` | `newspapers.series_id` | `NewspaperSeries` |

All seven models share the same pattern:
```php
$fillable = ['name', 'parent_id'];

public function parent(): BelongsTo  // self-referential
public function children(): HasMany  // ordered by name

// Returns flat Collection of stdClass {id, name} with depth-prefixed names
// e.g. "— — Auschwitz". Used to populate <select> dropdowns.
public static function flatTree(?int $parentId = null, int $depth = 0): Collection
```

**Unique constraint**: `UNIQUE(name, parent_id)` — same name is allowed under different parents, but siblings must be unique. (MySQL treats NULL `parent_id` values as distinct, so root-level duplicates are theoretically possible but guarded at the UI level.)

### Lookup admin (`LookupIndexController`)

- **Routes**: `GET/POST admin/lookups/{type}`, `PATCH/DELETE admin/lookups/{type}/{id}`
- **Tree types** render as indented rows with recursive usage total (node + all descendants).
- **Flat types** support sortable columns: name, in-use count, created date.
- **Sidebar** toggles between Add mode (with optional parent select for tree types) and Edit mode (rename + reparent, with circular-reference guard) via Alpine.js.
- Type→table config map lives entirely inside `LookupIndexController::config()`.

### `lookups:flatten-to-tree` artisan command

One-time data migration tool. Converts flat dash-separated names (e.g. "Kampen - Polen - Auschwitz") into proper tree nodes.

- Entries whose prefix is shared by ≥2 others are auto-planned.
- Entries with a unique prefix are shown interactively per group for manual approval.
- Supports `all` to process every tree type in one run.
- Idempotent: checks for existing nodes before inserting. Runs inside a transaction.

```bash
docker compose exec laravel.test php artisan lookups:flatten-to-tree all
docker compose exec laravel.test php artisan lookups:flatten-to-tree book-topics
```

### Media system

All file uploads go to the `b2` filesystem disk (Backblaze B2, S3-compatible). Storage paths follow `{type}/{id}/{uuid}.{ext}`. The `MediaFile` model resolves URLs via `Storage::disk($this->disk)->url($path)`.

**Invariant**: exactly one image per attachable can have `is_main = 1`. The `BookController::store` and `MediaFileController` both enforce this after uploads. When the main image is deleted, a new main is automatically promoted.

**Routes** for media (used by both books and items):
- `POST /{type}/{id}/media` → `MediaFileController@store`
- `DELETE /{type}/media/{file}` → `MediaFileController@destroy`
- `PATCH /{type}/media/{file}/main` → `MediaFileController@makeMain`

### Book creation flow

`BookController::create` accepts an optional `isbn` query parameter. If provided, it calls the Google Books API to pre-fill the form. The `store` method handles file uploads inside a `DB::transaction`, with cleanup of S3 files on failure.

### Authorization

Policies (`BookPolicy`, `ItemPolicy`) are registered manually in `AppServiceProvider`. `role_id === 1` means admin. Other collection controllers (banknotes, coins, etc.) use `AdminOnlyPolicy`.

### Frontend

- **Tailwind CSS** (primary) + some Bootstrap components
- **Alpine.js** with the `collapse` plugin — for interactive UI
- **Fancybox** — image gallery lightbox (bound to `[data-fancybox='gallery']`)
- **Choices.js** — enhanced `<select>` on elements with class `js-select`
- Entry point: `resources/js/app.js` and `resources/css/app.css`

### Layouts

- `layouts/app.blade.php` — base layout. Automatically uses admin header when on admin routes.
- `layouts/admin.blade.php` — extends `app`, adds sidebar. Admin views `@yield('admin-content')` instead of `@yield('content')`.
- Admin views live in `resources/views/admin/`, public views in `resources/views/` root subdirectories.

### View conventions

Admin book views use partials: `_fields.blade.php` (form fields), `_form.blade.php` (form wrapper), `_media.blade.php` (image/file upload UI), `_image-card.blade.php`, `_pdf-card.blade.php`.

### Origins

The `origins` table (formerly `item_origins`) is shared between `Book` and `Item` models via `origin_id → origins.id`. It supports the tree structure (`parent_id`) like all other tree lookup tables.
