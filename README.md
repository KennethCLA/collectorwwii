# CollectorWWII

A personal catalogue and display site for a World War II collection. Built with Laravel 11, Tailwind CSS, and Alpine.js.

## What it is

CollectorWWII is a full-stack web application with two distinct panels:

- **Public site** — a read-only display of the collection, accessible to anyone
- **Admin panel** — full CRUD management of all items, media, blog posts, and lookups (role-gated)

Eight collection sections are supported: **Books, Items, Banknotes, Coins, Magazines, Newspapers, Postcards, Stamps.**

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ≥ 8.2 |
| Composer | ≥ 2.x |
| Node.js | ≥ 20.x |
| npm | ≥ 10.x |
| MySQL | ≥ 8.0 (production) |

PHP extensions: `pdo_mysql`, `pdo_sqlite` (testing), `gd` or `imagick`, `fileinfo`, `mbstring`, `openssl`, `bcmath`, `xml`, `curl`.

---

## Installation

```bash
# 1. Clone
git clone <repo-url> collectorwwii
cd collectorwwii

# 2. PHP dependencies
composer install

# 3. Node dependencies
npm install

# 4. Environment
cp .env.example .env
php artisan key:generate
```

### Key environment variables

```dotenv
# Application
APP_URL=https://collectorwwii.eu

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=collectorwwii
DB_USERNAME=root
DB_PASSWORD=secret

# Backblaze B2 (media storage)
B2_KEY=your-key-id
B2_SECRET=your-application-key
B2_REGION=eu-central-003
B2_BUCKET=your-bucket-name
B2_URL=https://your-bucket.s3.eu-central-003.backblazeb2.com

# Mail (contact form, queued)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@collectorwwii.eu
MAIL_FROM_NAME="CollectorWWII"

# Google Books API (optional — enables ISBN lookup in book admin)
GOOGLE_BOOKS_API_KEY=your-api-key
```

### Database setup

```bash
php artisan migrate
# Optional: seed with test data
php artisan migrate:fresh --seed
```

---

## Running locally

Start all services in one command:

```bash
composer run dev
```

Or individually:

```bash
php artisan serve                     # Laravel development server
npm run dev                           # Vite asset watcher
php artisan queue:listen --tries=1    # Queue worker (contact form mail)
```

---

## Building for production

```bash
npm run build
php artisan optimize
```

---

## Testing

Tests run against an in-memory SQLite database — no database setup needed.

```bash
# All tests
php artisan test

# Specific file
php artisan test tests/Feature/AdminBookStoreUpdateTest.php

# Filter by name
php artisan test --filter="stores a book"
```

---

## Code style

```bash
./vendor/bin/pint
```

---

## Deployment

```bash
ssh ploi@116.203.104.195 "cd /home/ploi/collectorwwii.eu && git pull && php artisan migrate --force && php artisan optimize"
```

---

## Architecture

### Directory structure

```
app/
  Http/Controllers/
    Admin/          — Admin CRUD controllers (one per section + blog + map + lookups)
    Public/         — Public read-only controllers
    Ajax/           — AJAX-only endpoints (inline lookup creation)
  Models/           — Eloquent models
  Policies/         — Authorization policies (one per model + AdminOnlyPolicy)

config/
  collector.php     — Feature flags (enabled_sections)

resources/
  css/
    app.css         — Tailwind entry point + show-page CSS custom properties
    components.css  — Third-party overrides (Choices.js dropdown style)
  js/
    app.js          — Alpine.js, Fancybox, Choices.js init
    bootstrap.js    — Axios setup
  views/
    admin/          — Admin panel views (one subfolder per section)
    layouts/        — app.blade.php, admin.blade.php
    components/     — Blade components (nav-bar, admin-header, form/*)
    partials/       — Shared partials (show-media.blade.php)
    {section}/      — Public views per section (index.blade.php, show.blade.php)

routes/
  web.php           — Public routes
  admin.php         — Admin routes (prefix: /admin, auth middleware)

storage/
  app/public/
    blog.json       — Blog content (multi-language JSON array)
```

### Routing

| Route group | File | Prefix | Auth |
|---|---|---|---|
| Public | `routes/web.php` | `/` | None |
| Admin | `routes/admin.php` | `/admin` | `auth` + role policy |

Admin routes are registered as a group in `AppServiceProvider`. Authorization within admin is enforced by Laravel Policies (`role_id === 1` = admin).

### Two-layout system

- **`layouts/app.blade.php`** — Base layout. Automatically detects admin vs public routes and renders the correct nav. Contains the sticky header, footer, and header-height CSS variable logic.
- **`layouts/admin.blade.php`** — Extends `app`. Adds the 264px sidebar. Admin views use `@yield('admin-content')` instead of `@yield('content')`.

### Media system

All uploads go to **Backblaze B2** (S3-compatible) via the `b2` filesystem disk.

| Detail | Value |
|---|---|
| Storage path | `{type}/{id}/{uuid}.{ext}` |
| Max file size | 50 MB |
| Collections | `images`, `files` (PDFs) |
| Main image | `is_main = 1`, enforced — exactly one per record |
| On main delete | Next available image auto-promoted |

Media routes (shared across all sections):

```
POST   /admin/{type}/{id}/media          — upload
DELETE /admin/{type}/media/{file}        — delete
PATCH  /admin/{type}/media/{file}/main   — set as main
```

### Frontend stack

| Library | Version | Purpose |
|---|---|---|
| Tailwind CSS | 3.x | All styling |
| Alpine.js | 3.x | Mobile menu, filter drawer, thumbnail state, collapse |
| `@alpinejs/collapse` | — | Animated collapse on mobile menu |
| Fancybox | v5 | Image lightbox / gallery |
| Choices.js | — | Enhanced `<select>` dropdowns |
| Leaflet.js | CDN | Interactive map page |

**Tailwind theme palette:**

| Alias | Hex | Usage |
|---|---|---|
| `sage` (DEFAULT) | `#697367` | Primary backgrounds, nav, info cards |
| `sage-500` | `#565e55` | Sidebar, deeper card backgrounds |
| `sage-600` | `#4f5750` | Nav primary bar (BAR 1) |
| `sage-650` | `#636c65` | Nav collection bar (BAR 2), mobile menu bg |
| `sage-900` | `#343933` | Darkest elements, PDF viewer, thumbnail bg |
| `sage-950` | `#2c3335` | Home page hero cards, deepest dark |
| `khaki` | `#c2b280` | Accent colour, dividers, stencil highlights |
| `maroon` | `#6c2114` | Site title, destructive action buttons |
| `feldgrau` | `#4d5d53` | Wehrmacht field grey — error pages, reserved |

### Blog

Stored as `storage/app/public/blog.json` — an array of posts with `title`, `content`, `date`, and optional `author`. Multi-language via a `lang` session key (EN / NL / DE / FR). Managed via the admin blog CRUD.

### Feature flags

`config/collector.php` controls which sections are active:

```php
'enabled_sections' => [
    'books'      => true,
    'items'      => true,
    'magazines'  => true,
    'newspapers' => true,
    'banknotes'  => true,
    'coins'      => true,
    'postcards'  => true,
    'stamps'     => true,
],
```

Set any section to `false` to remove it from the nav and disable its routes without touching data.

---

## Models & database

### Collection models

All eight collection models share: `for_sale` boolean, `selling_price`, `purchase_price`, `purchase_date`, polymorphic `media` relation, `mainImage` morphOne, `card_title` accessor, `image_url` accessor.

| Model | Key relations |
|---|---|
| `Book` | `Author` (many-via-pivot), `BookSeries`, `BookCover`, `BookTopic`, `Location`, `Origin`. Soft deletes. |
| `Item` | `ItemCategory`, `ItemNationality`, `ItemOrganization`, `Origin`. Soft deletes. |
| `Banknote` | `Country`, `Currency`, `NominalValue`, `BanknoteSeries`, `BanknoteTimePeriod` |
| `Coin` | `Country`, `CoinShape`, `CoinMaterial`, `CoinOccasion` |
| `Magazine` | `title`, `issue_number`, `publication_date` |
| `Newspaper` | `title`, `publication_date` |
| `Postcard` | `Country`, `PostcardType` |
| `Stamp` | `Country`, `StampType` |

### Shared lookup models

All lookups follow the pattern `{ id, name }` with `$fillable = ['name']`:

`Country`, `Currency`, `NominalValue`, `BanknoteSeries`, `BanknoteTimePeriod`, `CoinShape`, `CoinMaterial`, `CoinOccasion`, `PostcardType`, `StampType`

---

## Admin panel overview

Access at `/admin` — requires `role_id = 1`.

| Section | Key features |
|---|---|
| **Dashboard** | BEFEHLSZENTRALE — live collection counts, weekly adds, quick-access links |
| **Books** | Full CRUD, ISBN lookup via Google Books API, comma-separated author management, image + PDF uploads |
| **Items** | Full CRUD, category / nationality / organization relations, image uploads |
| **Banknotes / Coins / Magazines / Newspapers / Postcards / Stamps** | Full CRUD, image uploads, for-sale toggle + selling price |
| **Blog** | Create / edit / delete posts in four languages (EN, NL, DE, FR) |
| **Map** | Add / edit / delete map markers with coordinates, descriptions, and photos |
| **Lookups** | Manage all dropdown reference values; inline AJAX modal for adding new values without leaving a form |
| **For Sale** | Aggregated read-only view of all for-sale items across all sections |
| **Profile** | Change display name / password |

---

## License

Private project — all rights reserved.
