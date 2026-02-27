# CollectorWWII Roadmap

Last updated: 2026-02-27

## 1) Session Summary (Completed)

### A. Project context and baseline alignment
- Reviewed repository docs and working guidance.
- Located and incorporated external roadmap context from Claude memory.

### B. For Sale page expansion (Task #2)
- Extended `/for-sale` aggregation to include:
  - books, items, banknotes, coins, magazines, newspapers, postcards, stamps
- Added type filters for all sections in the UI.
- Unified card data shape (`type`, `type_label`, `title`, `price`, `created_at`, `image`, `url`).
- Used `card_title` where applicable (banknotes/coins/postcards/stamps).
- Kept existing sorting and pagination behavior.

Files:
- `app/Http/Controllers/Public/ForSaleController.php`
- `resources/views/for-sale/index.blade.php`

### C. Admin items view consistency fix (Task #3)
- Updated `Admin\ItemController` create/edit views to use admin namespace:
  - `admin.items.create`
  - `admin.items.edit`
- Added copied admin view files for create/edit.

Files:
- `app/Http/Controllers/Admin/ItemController.php`
- `resources/views/admin/items/create.blade.php`
- `resources/views/admin/items/edit.blade.php`

### D. Admin dashboard real stats (Task #4)
- Replaced placeholder dashboard with section statistics for all 8 sections.
- Added totals, for-sale counts, created-this-week counts.
- Added quick actions and direct “Manage” links from dashboard cards.

Files:
- `app/Http/Controllers/Admin/DashboardController.php`
- `resources/views/admin/dashboard.blade.php`

### E. Blog admin management (Task #5)
- Added full admin CRUD for blog posts stored in `storage/app/public/blog.json`.
- Added stable JSON `id` handling and date-sorted persistence.
- Added admin views and sidebar links.

Files:
- `app/Http/Controllers/Admin/BlogController.php`
- `routes/admin.php`
- `resources/views/admin/blog/index.blade.php`
- `resources/views/admin/blog/create.blade.php`
- `resources/views/admin/blog/edit.blade.php`
- `resources/views/admin/partials/sidebar.blade.php`

### F. Migration reliability + execution
- Diagnosed `magazines.deleted_at` error as pending migration issue.
- Hardened migrations with idempotent column checks for mixed DB states:
  - `2026_02_27_000001_add_fields_to_magazines_table.php`
  - `2026_02_27_000002_add_fields_to_newspapers_table.php`
- Started Sail MySQL and successfully applied pending `2026_02_27_*` migrations.

### G. Lookup management (major expansion)
- Added centralized admin lookup manager covering broad secondary tables:
  - books/items/shared/banknotes/coins/postcards/stamps lookup families
- Added searchable listing and add-option flow.
- Added protected deletion logic:
  - delete blocked when option is referenced by active records
  - in-use count shown in UI
- Kept existing AJAX lookup creation route working by moving it to:
  - `admin/lookups/ajax/{type}`

Files:
- `app/Http/Controllers/Admin/LookupIndexController.php`
- `routes/admin.php`
- `resources/views/admin/lookups/index.blade.php`
- `resources/views/admin/partials/sidebar.blade.php`

### H. Admin UI/UX improvements
- Modernized admin shell visual structure.
- Sidebar improvements:
  - grouped navigation
  - active states
  - collapsible sections
  - search filter for nav links
- Clarified and regrouped shared lookups (`Origins`, `Locations`).

Files:
- `resources/views/layouts/admin.blade.php`
- `resources/views/admin/partials/sidebar.blade.php`
- `resources/views/admin/dashboard.blade.php`

### I. Site-wide UI/UX pass (shared templates)
- Improved header/nav consistency and accessibility.
- Added skip-link and stronger focus-visible behavior.
- Improved nav-link styling and route-active consistency.
- Added/standardized `Map` link in header navigation.
- Refined global footer behavior and homepage-specific rendering.

Files:
- `resources/views/layouts/app.blade.php`
- `resources/views/components/nav-bar.blade.php`
- `resources/views/components/nav-link.blade.php`
- `resources/views/components/admin-header.blade.php`
- `resources/css/app.css`

### J. Homepage fit/readability adjustments
- Home now designed to fit viewport height without page scrolling.
- Added compact in-home footer links so footer content remains visible.
- Tuned responsive scaling and latest-post preview length for readability.

Files:
- `resources/views/home.blade.php`
- `resources/views/layouts/app.blade.php`

### K. New Map feature (public + admin)
- Public map page:
  - `/map` with leaflet markers for visited locations
  - marker popup: name, coordinates, description, photos
  - Fancybox-enabled photo gallery in popups
- Admin map location management:
  - CRUD for map locations
  - map pin placement in create/edit via clickable map
  - location search in create/edit (Nominatim + Photon fallback)
  - one-step photo upload on create + ongoing media management on edit
- Reused polymorphic media system by adding `map-locations` media type.

Files:
- `app/Models/MapLocation.php`
- `app/Policies/MapLocationPolicy.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/Public/MapController.php`
- `app/Http/Controllers/Admin/MapLocationController.php`
- `app/Http/Controllers/Admin/MediaFileController.php`
- `routes/web.php`
- `routes/admin.php`
- `resources/views/map/index.blade.php`
- `resources/views/admin/map-locations/index.blade.php`
- `resources/views/admin/map-locations/create.blade.php`
- `resources/views/admin/map-locations/edit.blade.php`
- `resources/views/components/nav-bar.blade.php`
- `resources/js/app.js` (Fancybox selector broadened to `[data-fancybox]`)

---

## 2) Current Status

### Working
- For-sale includes all intended sections.
- Admin blog CRUD is functional.
- Admin dashboard shows useful stats.
- Lookup manager supports broad secondary tables with safe-delete protection.
- Public map page and admin map-location workflow are implemented.
- Pending schema changes from 2026 roadmap were migrated on Sail MySQL.

### Known environment caveat
- Local frontend build in this shell reported optional Rollup dependency issue:
  - missing `@rollup/rollup-linux-x64-gnu`
- Runtime app behavior and Blade compilation were validated through `sail artisan view:cache`.

---

## 3) Plan of Action (Next Steps)

### Immediate (next session)
1. Add feature tests for:
   - for-sale aggregation/filtering/sorting
   - lookup delete protection (in-use vs unused)
   - map-locations create/edit with coordinates + photos
2. Add user-facing validation hints on map search failures/rate limits.
3. Add tiny admin help text/tooltips for lookup deletion rules.

### Short-term
1. Contact form implementation (mail + queue) from original roadmap Task #6.
2. Add quick “open on map” link from map-location admin index rows.
3. Improve map popup content formatting and image count badges.
4. Add optional category filters to public map (country/type/period if needed).

### Medium-term
1. Global search across sections.
2. Selling workflow enhancements (sold status, sold date/price, badges).
3. Media ordering UI (drag-and-drop using existing `sort_order`).
4. SEO meta strategy (`description`, `og:image`, structured data).

---

## 4) Future Roadmap (Strategic)

1. Admin user/role management UI.
2. Public map clustering and performance optimization for large marker sets.
3. Audit and harmonize index/show page UX patterns for all sections.
4. Add analytics-friendly event tracking (search usage, map interactions, for-sale clicks).
5. CI checks for migrations + Blade compile + targeted feature smoke tests.

---

## 5) Operational Notes

- Admin routes are mounted via:
  - `web + auth + is_admin`
  - prefix `admin/`
  - route name prefix `admin.`
- Media uploads continue using Backblaze B2 (`disk=b2`).
- For map locations, photos are stored under:
  - `map-locations/{id}/...`

---

## 6) End-of-day checkpoint

- Major roadmap objectives for this session were delivered.
- System now includes stronger admin tooling and a new public-facing map feature.
- Recommended next session focus: tests + contact form.

