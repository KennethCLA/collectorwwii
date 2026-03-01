# CollectorWWII — Roadmap

Last updated: 2026-03-01

---

## Current status: Stable · Production

The site is live at [collectorwwii.eu](https://collectorwwii.eu) and feature-complete.
All known bugs resolved. WWII aesthetic theming complete across all pages.

---

## Completed work

### Core features
- Eight collection sections: Books, Items, Banknotes, Coins, Magazines, Newspapers, Postcards, Stamps
- Full admin CRUD per section; image + PDF uploads to Backblaze B2
- Polymorphic media system with main image enforcement and auto-promotion on delete
- ISBN lookup via Google Books API (books admin)
- Lookup manager with inline AJAX creation and safe-delete protection
- Interactive map (Leaflet.js) with marker management, photo galleries, coordinate search
- Blog system — multi-language (EN / NL / DE / FR), admin CRUD, stored as JSON
- Contact form with queued mail
- For-sale aggregated view across all sections — type filter, sort, search, pagination
- Feature flags per section (`config/collector.php → enabled_sections`)
- Laravel Policies per model, role-based access (`role_id = 1` = admin)
- Full test suite (Pest, SQLite in-memory)
- Mobile-responsive layout: Alpine.js filter drawer per index page

### WWII aesthetic theming — Phase 1 & 2 (February 2026)
Military design language: Wehrmacht field manuals, Heer stencil markings, Kriegsmarine charts.

| Element | Details |
|---|---|
| Font | Share Tech Mono as `font-stencil` throughout |
| Balkenkreuz watermarks | Wehrmacht cross at ~4% opacity on hero cards |
| GEHEIM stamps | Admin sections: stencil, −20° rotation, red-900 7% |
| Noise texture | `.noise-texture` CSS class on all main cards (fractal SVG + blend-mode) |
| Double inset border | Document-frame box-shadow on all cards |
| Iron Cross divider | Between NAV BAR 1 and BAR 2 |
| Catalogue badge | `#0042` ID prefix on all index cards |
| Khaki dividers | `border-khaki/20` on show pages and media partials |
| Wehrmacht grid | Map page: khaki repeating-linear-gradient overlay |
| Error pages | 404 → VERMISST, 500 → BESCHÄDIGT (military poster style) |
| Show page headers | "Feldbericht · Objektakte" / "Geheimakte · Verwaltung" |
| Admin dashboard | BEFEHLSZENTRALE — Tätigkeitsbericht with Balkenkreuz watermark |
| Map page | LAGEBERICHT — Operationskarte |
| For Sale page | ZU VERKAUFEN — Feldpost-Auktion |
| Login | ZUGANG — Zugangsberechtigungsausweis |
| Register | REGISTRIERUNG — Personalstammbuch |
| Profile | SOLDBUCH — Dienstakte |

### UX polish — Phase 3 (March 2026)
- **Show page field layout** — compact table-row definition list (3× information density)
- **Mobile menu auto-close** — event delegation closes drawer on any link tap
- **Home page** — Dutch subtitle, "Lees meer →" blog link, all hardcoded hex removed
- **[x-cloak] CSS rule** — prevents Alpine.js flash-of-unstyled-content
- **Nested form bug fix** — all 8 edit pages: HTML5 `form="id"` attribute pattern
- **Top spacing** — map / for-sale / profile: `pt-6` on inner container
- **Color palette** — all hex colors replaced with Tailwind aliases: `sage-650` (#636c65), `sage-950` (#2c3335) added

---

## Future features

### High impact
| Feature | Notes |
|---|---|
| **Global search** | Cross-section search bar in NAV BAR 1. Single `SearchController` querying all 8 models, results page grouped by section. |
| **Condition grading** | Add `condition` field to all models (Mint / Near Mint / VG / Good / Fair / Poor). Visible on show pages, filterable on index. |
| **Collection value** | Dashboard widget: total purchase value, total for-sale value, unrealised margin. |
| **Sold tracking** | Add `sold_at` date + `sold_price` to all models. Mark items as sold (not for sale, with history). Show sold badge. |

### Medium impact
| Feature | Notes |
|---|---|
| **Provenance chain** | `provenance` text field: "Acquired at [auction/market], originally from [unit/region], [year]". Free text or structured JSON. |
| **Timeline view** | Visualise collection on a 1933–1945 chronological axis. Group by production/issue year. |
| **Image comparison slider** | Front/back comparison for coins, banknotes, stamps. Alpine.js drag-to-reveal. |
| **Related items** | "See also" sidebar on show pages based on shared country, origin, or year range. |
| **Print view** | `@media print` CSS for show pages — useful for insurance/valuation documentation. |
| **Media reordering** | Drag-and-drop image reorder using existing `sort_order` column (UI missing). |

### Low impact / polish
| Feature | Notes |
|---|---|
| **Skeleton loaders** | While images load from B2, show a khaki shimmer placeholder. |
| **Progressive images** | Serve WebP via Backblaze transformations; JPEG fallback. |
| **Admin breadcrumbs** | Currently absent on most admin edit/create views. |
| **Admin activity log** | Simple `activity_log` table: user, action, model, timestamp. Shown on dashboard. |
| **CSV/PDF export** | Export any section with current filters applied. |
| **Statistics page** | Public page with Chart.js: distribution by country, year range, category, for-sale ratio. |
| **Keyboard navigation** | Add `tabindex` logic and keyboard shortcuts for admin list operations. |

---

## Known limitations / technical debt

| Area | Detail |
|---|---|
| Blog language | Session-based language switching is not SEO-friendly; no URL-based i18n |
| Map photos | Stored as separate S3 paths without a MediaFile record; bypass the main media system |
| Show page depth | Books have 20+ fields; other sections have 3–6. Consider tabbed layout for books long-term. |
| Admin nav | No active highlighting in admin sidebar beyond the current section |
| Accessibility | Decorative SVGs not all `aria-hidden`. Some icon-only buttons lack `aria-label`. |

---

## Deployment

```bash
ssh ploi@116.203.104.195 "cd /home/ploi/collectorwwii.eu && git pull && php artisan migrate --force && php artisan optimize"
```
