# Code Review Report — Anime & Waifu Vault

> **Purpose:** Static analysis of the repository.  
> **Scope:** All source files.  
> **Date:** 2026-02-25  
> No code has been rewritten. This report focuses on analysis and actionable suggestions only.

---

## Table of Contents

1. [index.php](#1-indexphp)
2. [api.php](#2-apiphp)
3. [search_api.js](#3-search_apijs)
4. [service-worker.js](#4-service-workerjs)
5. [db.php](#5-dbphp)
6. [database.sql](#6-databasesql)
7. [Summary Table](#7-summary-table)
8. [Recommended Modular Architecture](#8-recommended-modular-architecture)

---

## 1. `index.php`

**Total lines:** 1 247

This single file combines HTML markup, a large CSS `<style>` block, inline JavaScript, and modal templates. It is the most significant source of complexity and readability issues in the project.

---

### 1.1 Oversized Embedded CSS Block

| Property | Value |
|----------|-------|
| **Lines** | 12 – 321 |
| **Size** | ~309 lines |
| **Issue type** | Readability · Scalability · Maintainability |

**Problem:** All styles are declared inside a `<style>` tag inside the HTML document. Adding or changing styles requires opening the same file that contains business logic and markup.

**Suggestion:**
- Extract to a dedicated `assets/css/style.css` file and link it with `<link rel="stylesheet" href="assets/css/style.css">`.
- Consider splitting into logical partials: `base.css`, `components.css`, `modals.css`.

---

### 1.2 Oversized Embedded JavaScript Block

| Property | Value |
|----------|-------|
| **Lines** | 619 – 1 193 |
| **Size** | ~574 lines |
| **Issue type** | Complexity · Readability · Scalability · Maintainability |

**Problem:** All client-side application logic lives inside a single `<script>` block. It mixes state management, API calls, DOM rendering, form handling, and event wiring with no separation of concerns.

**Suggestion:** Split into focused ES modules or plain JS files:

| Suggested file | Responsibility |
|----------------|----------------|
| `assets/js/state.js` | Global state (`allAnimes`, `allWaifus`, `currentFilter`, etc.) |
| `assets/js/api.js` | All `fetch()` wrappers for `api.php` |
| `assets/js/anime.js` | `loadAnimes`, `renderAnimes`, `renderAnimeCard`, `submitAnime`, `editAnime`, `deleteAnime`, `filterAnime`, `openAnimeModal`, `closeAnimeModal`, `resetAnimeForm`, `showAnimeDetail` |
| `assets/js/waifu.js` | `loadWaifus`, `renderWaifus`, `renderWaifuCard`, `submitWaifu`, `editWaifu`, `deleteWaifu`, `openWaifuModal`, `closeWaifuModal`, `resetWaifuForm`, `showWaifuDetail`, `toggleFav`, `renderModalGallery`, `uploadToGallery`, `deleteGalleryItem` |
| `assets/js/dashboard.js` | `loadDashboard` |
| `assets/js/ui.js` | `showPage`, `showToast`, `openLightbox`, `escHtml`, `previewAnimeImg`, `previewWaifuPict`, `showAnimeImgPreview`, `revertToOfficial` |
| `assets/js/app.js` | Boot / init / event listener wiring (DOMContentLoaded) |

---

### 1.3 `loadDashboard()` — Near-Limit Complexity

| Property | Value |
|----------|-------|
| **Lines** | 648 – 695 |
| **Size** | 47 lines |
| **Issue type** | Complexity · Readability |

**Problem:** A single function fetches both anime and waifu data, updates four stat counters, renders three separate card grids, toggles two "see more" buttons, and updates the background image. While just under 50 lines, it has too many responsibilities.

**Suggestion:** Break into smaller, single-responsibility helpers:
```
loadDashboard()           → orchestrator only
  updateStats(animes, waifus)
  renderFavWaifus(waifus)
  renderFavAnimes(animes)
  renderRecentAnimes(animes)
  updateWaifuBackground(favWaifus)
```

---

### 1.4 `renderAnimeCard()` — Inline HTML Template

| Property | Value |
|----------|-------|
| **Lines** | 719 – 758 |
| **Size** | 39 lines |
| **Issue type** | Readability · Maintainability |

**Problem:** A long multi-line template literal containing HTML is constructed inside a JavaScript function. It mixes presentational markup with logic, making both harder to read and test.

**Suggestion:**
- Use a `<template id="anime-card-tpl">` element in the HTML and clone it in JS, or
- Separate the HTML string into a clearly named template function `animeCardTemplate(a)` in a `templates.js` file.

---

### 1.5 `renderWaifuCard()` — Inline HTML Template

| Property | Value |
|----------|-------|
| **Lines** | 904 – 936 |
| **Size** | 32 lines |
| **Issue type** | Readability · Maintainability |

Same issue as §1.4 — inline HTML template mixed with JavaScript logic.

**Suggestion:** Same approach as §1.4: extract to a `waifuCardTemplate(w)` helper or a `<template>` element.

---

### 1.6 Duplicate `close*Modal` Pattern

| Property | Value |
|----------|-------|
| **Lines** | 803 – 807 (anime), 979 – 983 (waifu) |
| **Issue type** | Duplication |

Both `closeAnimeModal(e)` and `closeWaifuModal(e)` share the same logic of checking whether the click target is the overlay and then removing the `open` class.

**Suggestion:** Extract a generic helper:
```js
function closeModalOnOverlayClick(e, modalId) {
    if (!e || e.target.id === modalId) {
        document.getElementById(modalId).classList.remove('open');
    }
}
```

---

### 1.7 Duplicate `reset*Form` Pattern

| Property | Value |
|----------|-------|
| **Lines** | 809 – 819 (anime), 985 – 992 (waifu) |
| **Issue type** | Duplication |

Both functions call `form.reset()` and then manually clear hidden inputs and UI elements. The pattern is repeated.

**Suggestion:** Extract a shared `resetForm(formId, hiddenIds, previewIds)` utility.

---

### 1.8 Duplicated `delete*` Confirmation Pattern

| Property | Value |
|----------|-------|
| **Lines** | 826 – 832 (anime), 1015 – 1021 (waifu) |
| **Issue type** | Duplication |

Both `deleteAnime(id)` and `deleteWaifu(id)` follow an identical pattern:
1. `confirm()` dialog
2. `fetch()` to delete endpoint
3. `showToast()` message
4. Reload the entity list
5. Reload the dashboard

**Suggestion:** Extract a generic `deleteEntity(action, id, label, reloadFn)` helper.

---

### 1.9 `submitAnime` and `submitWaifu` — Structural Similarity

| Property | Value |
|----------|-------|
| **Lines** | 834 – 868 (anime), 1023 – 1050 (waifu) |
| **Issue type** | Duplication · Complexity |

Both functions build a `FormData` object, POST to `api.php`, check for success, show a toast, close the modal, and reload data. The shape is identical; only the fields differ.

**Suggestion:** Consider a generic `submitEntity(formConfig)` helper that accepts a configuration object describing which fields to collect and which actions to take on success/failure.

---

### 1.10 Detail Modals Placed After `</script>` Tag

| Property | Value |
|----------|-------|
| **Lines** | 1 195 – 1 245 |
| **Issue type** | Readability · Maintainability |

The `#detail-modal-anime` and `#detail-modal-waifu` HTML blocks appear *after* the closing `</script>` tag and before `</body>`. This is an unusual ordering (modals at the top of body is more conventional) and makes the file harder to navigate.

**Suggestion:** Move all modal HTML to a consistent location — either immediately after `<body>` opens, or group all modals just before the `</body>` closing tag before the scripts.

---

## 2. `api.php`

**Total lines:** 187

---

### 2.1 Duplicate Image-Handling Logic

| Property | Value |
|----------|-------|
| **Lines** | 41 – 47 (`add_anime`), 65 – 70 (`update_anime`) |
| **Issue type** | Duplication |

Both the `add_anime` and `update_anime` cases contain the same three-way decision:
1. If a file is uploaded → call `uploadFile()`
2. Else if a URL was posted → use the URL
3. Else → keep the existing value

This block appears again in the `add_waifu` and `update_waifu` cases (lines 121 and 147 – 148).

**Suggestion:** Extract to a shared helper:
```php
function resolveImagePath(
    array $files,
    string $fileKey,
    string $subdir,
    ?string $existingPath = null
): ?string {
    if (!empty($files[$fileKey]['name'])) {
        return uploadFile($files[$fileKey], $subdir);
    }
    if (!empty($_POST[$fileKey . '_url'])) {
        return $_POST[$fileKey . '_url'];
    }
    return $existingPath;
}
```

---

### 2.2 Duplicate Toggle-Favorite Pattern

| Property | Value |
|----------|-------|
| **Lines** | 85 – 97 (`toggle_anime_fav`), 170 – 182 (`toggle_fav`) |
| **Issue type** | Duplication |

Both cases fetch the current `is_fav` value, compute the inverse, and update the row. The logic is identical apart from the table name and `id` source.

**Suggestion:** Extract a generic helper:
```php
function toggleBoolColumn(PDO $db, string $table, string $column, int $id): void {
    $stmt = $db->prepare("SELECT {$column} FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    $db->prepare("UPDATE {$table} SET {$column} = ? WHERE id = ?")
       ->execute([$current ? 0 : 1, $id]);
}
```

---

### 2.3 Missing Input Validation

| Property | Value |
|----------|-------|
| **Lines** | 49 – 58 (`add_anime`), 72 – 73 (`update_anime`), 125 – 126 (`add_waifu`), 151 – 152 (`update_waifu`) |
| **Issue type** | Scalability · Maintainability |

Required fields (e.g., `judul`, `nama`) are used directly without validating they are non-empty before the SQL statement. A missing required field will either cause a DB error or silently insert an empty string.

**Suggestion:** Add a centralized validation step before each INSERT/UPDATE, or use a simple validation helper that returns errors before the DB call.

---

### 2.4 Monolithic Switch Statement

| Property | Value |
|----------|-------|
| **Lines** | 12 – 186 |
| **Size** | 174 lines |
| **Issue type** | Scalability · Maintainability |

All 12 API actions are handled in a single `switch` statement in one file. Adding a new resource (e.g., reviews, tags) means extending this already-large block.

**Suggestion:** Split into separate handler files or classes:
```
handlers/
  AnimeHandler.php    → get_animes, get_anime_details, add_anime, update_anime, delete_anime, toggle_anime_fav
  WaifuHandler.php    → get_waifus, get_waifu_details, add_waifu, update_waifu, delete_waifu, toggle_fav
  GalleryHandler.php  → add_gallery_item, delete_gallery_item
```
`api.php` then becomes a thin router that dispatches to the appropriate handler.

---

### 2.5 Duplicate `header('Content-Type: application/json')` Call

| Property | Value |
|----------|-------|
| **Lines** | 6 (`api.php`) and 60 (`db.php` → `jsonResponse()`) |
| **Issue type** | Duplication |

The `Content-Type: application/json` header is set at line 6 of `api.php` and again inside `jsonResponse()` in `db.php`. One of these is redundant.

**Suggestion:** Remove the header from `api.php` line 6 and rely solely on `jsonResponse()` to set it.

---

## 3. `search_api.js`

**Total lines:** 176

---

### 3.1 Near-Identical `searchAnimeAPI` and `searchWaifuAPI` Functions

| Property | Value |
|----------|-------|
| **Lines** | 28 – 75 (`searchAnimeAPI`), 77 – 116 (`searchWaifuAPI`) |
| **Size** | 48 and 40 lines respectively |
| **Issue type** | Duplication · Maintainability |

Both functions share the same structure:
1. Read input value from a named element
2. Early return if length < 2
3. Toggle loading indicator
4. `fetch()` from Jikan API with the query
5. Handle 429 rate-limit
6. Sort results with `sortResults()`
7. Render results as HTML list items
8. Toggle visibility of the results list

The differences are only: the element IDs used, the API endpoint (`/anime` vs `/characters`), the render template, and the results array written to.

**Suggestion:** Extract a generic `searchJikanAPI(config)` function:
```js
async function searchJikanAPI({
    inputId, resultsId, loadingId,
    endpoint, sortKey, renderItem, storageVar
}) { /* shared logic */ }
```
Then:
```js
async function searchAnimeAPI() {
    await searchJikanAPI({
        inputId: 'api-search',
        resultsId: 'search-results-list',
        loadingId: 'search-loading',
        endpoint: 'anime',
        sortKey: 'title',
        renderItem: renderAnimeSearchItem,
        storageVar: 'animeSearchResults'
    });
}
```

---

### 3.2 Magic Strings for DOM IDs

| Property | Value |
|----------|-------|
| **Lines** | Throughout the file |
| **Issue type** | Maintainability |

DOM element IDs (`'api-search'`, `'search-results-list'`, `'search-loading'`, etc.) are hardcoded as plain strings in multiple places across `search_api.js` and `index.php`. Renaming an element requires finding and updating every occurrence.

**Suggestion:** Define a constants object at the top of the file:
```js
const ELEMENTS = {
    animeSearchInput:  'api-search',
    animeResultsList:  'search-results-list',
    animeLoading:      'search-loading',
    waifuSearchInput:  'api-search-waifu',
    waifuResultsList:  'search-waifu-results-list',
    waifuLoading:      'search-waifu-loading',
};
```

---

### 3.3 Module-Level Mutable Variables

| Property | Value |
|----------|-------|
| **Lines** | 3 – 8 |
| **Issue type** | Scalability |

`animeTimer`, `waifuTimer`, `waifuSearchResults`, and `animeSearchResults` are declared as module-level variables. In a plain `<script>` context, they become global state that any other script can accidentally overwrite.

**Suggestion:** Wrap the module in an IIFE or convert to an ES module (`type="module"`) to scope these variables properly.

---

## 4. `service-worker.js`

**Total lines:** 214

---

### 4.1 `offlinePage()` Exceeds 50 Lines with Inline HTML

| Property | Value |
|----------|-------|
| **Lines** | 128 – 184 |
| **Size** | 56 lines |
| **Issue type** | Complexity · Readability |

`offlinePage()` returns a `Response` object whose body is a 52-line HTML template literal. The function itself exceeds the 50-line threshold, and mixing full HTML inside a JavaScript function makes both hard to maintain.

**Suggestion:**
- Extract the HTML string to a named constant `OFFLINE_HTML` defined at the top of the file, keeping the function itself to just a few lines:
```js
function offlinePage() {
    return new Response(OFFLINE_HTML, {
        headers: { 'Content-Type': 'text/html; charset=utf-8' },
        status: 200
    });
}
```
- Long-term: store the offline page as a separate `offline.html` file and cache it during the install event, then serve it from cache directly.

---

### 4.2 Unused `CACHE_NAME` Constant

| Property | Value |
|----------|-------|
| **Lines** | 3 |
| **Issue type** | Readability |

`CACHE_NAME = 'anime-waifu-vault-v1'` is defined but never used; all operations use `STATIC_CACHE`. The leftover constant is confusing.

**Suggestion:** Remove `CACHE_NAME` or consolidate into a single cache name constant.

---

### 4.3 Empty Stub Event Listeners

| Property | Value |
|----------|-------|
| **Lines** | 186 – 213 |
| **Issue type** | Readability |

The `sync` and `push` event listeners are present but only contain `console.log` stubs. While placeholders can be useful, they inflate the file size and may confuse readers into thinking the feature is implemented.

**Suggestion:** Either implement the feature or remove the stubs and add a comment noting the feature is planned.

---

## 5. `db.php`

**Total lines:** 65

This file is relatively clean, but has a few issues worth noting.

---

### 5.1 Hardcoded Database Credentials

| Property | Value |
|----------|-------|
| **Lines** | 4 – 7 |
| **Issue type** | Scalability · Maintainability |

Database host, name, user, and password are hardcoded as PHP `define()` constants at the top of the file.

**Suggestion:** Load credentials from environment variables or a `.env` file (with a library like `phpdotenv`), and add `.env` to `.gitignore`. Example:
```php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'anime_waifu_vault');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
```

---

### 5.2 `uploadFile()` Silent Failure on Invalid Extension or Size

| Property | Value |
|----------|-------|
| **Lines** | 44 – 45 |
| **Issue type** | Maintainability |

When the file extension is not allowed or the file exceeds 5 MB, the function returns `null` silently. The caller has no way to distinguish "upload failed due to bad extension" from "upload failed due to file too large" from "no file provided."

**Suggestion:** Return a structured result or throw a typed exception so callers can provide specific error messages to the user:
```php
function uploadFile(array $file, string $subdir = ''): array {
    // Returns ['path' => string] on success or ['error' => string] on failure
}
```

---

### 5.3 `jsonResponse()` Calls `exit` Implicitly After Every Response

| Property | Value |
|----------|-------|
| **Lines** | 62 |
| **Issue type** | Maintainability |

Calling `exit` inside `jsonResponse()` makes the control flow non-obvious. Any code after a `jsonResponse()` call is silently dead code.

**Suggestion:** Document this explicitly with a `@noreturn` annotation and a comment, or change the design so `api.php` returns from each `case` after `jsonResponse()` is called, and `jsonResponse()` does not call `exit`.

---

## 6. `database.sql`

**Total lines:** 45

---

### 6.1 `genres` Column Missing from `animes` Table

| Property | Value |
|----------|-------|
| **Lines** | 9 – 19 (table definition) |
| **Issue type** | Correctness |

`api.php` references `genres` in INSERT (line 49) and UPDATE (line 72) queries, and `index.php` renders `a.genres` in detail views (line 1158). However, the `animes` table in `database.sql` has no `genres` column.

**Suggestion:** Add the column to the schema:
```sql
genres VARCHAR(500) DEFAULT NULL,
```

---

### 6.2 `official_pict_url` Column Missing from `waifus` Table

| Property | Value |
|----------|-------|
| **Lines** | 22 – 33 (table definition) |
| **Issue type** | Correctness |

`api.php` inserts `official_pict_url` into `waifus` (line 125) and `index.php` reads `w.official_pict_url` (lines 906, 965, 968, 1172). The column is not defined in the schema.

**Suggestion:** Add the column:
```sql
official_pict_url VARCHAR(500) DEFAULT NULL,
```

---

### 6.3 `waifu_gallery` Table Missing from Schema

| Property | Value |
|----------|-------|
| **Lines** | — (missing entirely) |
| **Issue type** | Correctness |

`api.php` performs INSERT, SELECT, and DELETE operations on a `waifu_gallery` table (lines 113–115, 132, 160, 166), but this table is not defined in `database.sql`. Any fresh installation will fail when these actions are called.

**Suggestion:** Add the table definition:
```sql
CREATE TABLE IF NOT EXISTS waifu_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    waifu_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (waifu_id) REFERENCES waifus(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### 6.4 `art_path` Column in `waifus` Not Used in Application Code

| Property | Value |
|----------|-------|
| **Lines** | 30 (table definition) |
| **Issue type** | Readability · Maintainability |

The `art_path` column exists in the `waifus` table but `api.php` comments note it was removed in favour of the gallery table (line 150). The column is only accessed in `index.php` via `w.art_path` in the card render (line 909) as a fallback.

**Suggestion:** Either remove the column from the schema and the card render fallback, or document why it is kept as a legacy fallback.

---

## 7. Summary Table

| File | Lines | Issue Type | Severity |
|------|-------|-----------|---------|
| `index.php` | 12 – 321 | CSS embedded in HTML | Medium |
| `index.php` | 619 – 1 193 | JavaScript embedded in HTML (574 lines) | High |
| `index.php` | 648 – 695 | `loadDashboard()` — too many responsibilities | Medium |
| `index.php` | 719 – 758 | `renderAnimeCard()` — inline HTML template | Medium |
| `index.php` | 904 – 936 | `renderWaifuCard()` — inline HTML template | Medium |
| `index.php` | 803 – 807, 979 – 983 | Duplicate close-modal pattern | Low |
| `index.php` | 809 – 819, 985 – 992 | Duplicate reset-form pattern | Low |
| `index.php` | 826 – 832, 1015 – 1021 | Duplicate delete-entity pattern | Low |
| `index.php` | 834 – 868, 1023 – 1050 | Structurally identical submit handlers | Medium |
| `index.php` | 1 195 – 1 245 | Detail modals placed after `</script>` tag | Low |
| `api.php` | 41 – 47, 65 – 70, 121, 147 – 148 | Duplicate image-handling logic | Medium |
| `api.php` | 85 – 97, 170 – 182 | Duplicate toggle-favorite pattern | Medium |
| `api.php` | 49 – 58, 72 – 73, 125 – 126, 151 – 152 | Missing input validation | High |
| `api.php` | 12 – 186 | Monolithic switch (12 actions in one file) | High |
| `api.php` | 6 | Redundant `Content-Type` header | Low |
| `search_api.js` | 28 – 75, 77 – 116 | Near-identical search functions | High |
| `search_api.js` | Throughout | Magic-string DOM IDs | Low |
| `search_api.js` | 3 – 8 | Module-level mutable global state | Medium |
| `service-worker.js` | 128 – 184 | `offlinePage()` > 50 lines with inline HTML | Medium |
| `service-worker.js` | 3 | Unused `CACHE_NAME` constant | Low |
| `service-worker.js` | 186 – 213 | Empty stub event listeners | Low |
| `db.php` | 4 – 7 | Hardcoded credentials | High |
| `db.php` | 44 – 45 | Silent failure on invalid upload | Medium |
| `db.php` | 62 | `exit` hidden inside helper function | Low |
| `database.sql` | 9 – 19 | Missing `genres` column | High |
| `database.sql` | 22 – 33 | Missing `official_pict_url` column | High |
| `database.sql` | — | Missing `waifu_gallery` table | High |
| `database.sql` | 30 | `art_path` column unused but kept | Low |

---

## 8. Recommended Modular Architecture

The following structure separates concerns and enables independent development, testing, and maintenance of each layer.

```
anime_waifu_vault/
├── index.php                    # Thin PHP shell: outputs HTML skeleton only
├── api.php                      # Thin router: delegates to handlers
├── db.php                       # DB connection + shared helpers (uploadFile, jsonResponse)
├── .env                         # Database credentials (not committed)
├── database.sql                 # Complete schema (add missing columns/tables)
│
├── handlers/                    # PHP API handlers (one class per resource)
│   ├── AnimeHandler.php
│   ├── WaifuHandler.php
│   └── GalleryHandler.php
│
├── assets/
│   ├── css/
│   │   ├── base.css             # Reset, body, typography
│   │   ├── components.css       # Cards, buttons, inputs, badges
│   │   └── modals.css           # Modal overlays, lightbox, toast
│   └── js/
│       ├── state.js             # Shared mutable state
│       ├── api.js               # fetch() wrappers
│       ├── templates.js         # HTML template functions (animeCardTemplate, etc.)
│       ├── anime.js             # Anime CRUD + rendering
│       ├── waifu.js             # Waifu CRUD + rendering + gallery
│       ├── dashboard.js         # Dashboard load + stats
│       ├── search_api.js        # Generic + specialised Jikan search
│       ├── ui.js                # Navigation, toast, lightbox, escHtml
│       └── app.js               # Boot: import modules, wire event listeners
│
├── templates/                   # (Optional) PHP HTML partials
│   ├── navbar.php
│   ├── modals/
│   │   ├── anime-modal.php
│   │   ├── waifu-modal.php
│   │   ├── detail-anime.php
│   │   └── detail-waifu.php
│   └── pages/
│       ├── dashboard.php
│       ├── anime.php
│       └── waifu.php
│
├── manifest.json
├── service-worker.js
├── offline.html                 # Extracted offline fallback page
├── uploads/
│   ├── anime/
│   ├── waifu/
│   └── fanart/
└── README.md
```

### Key Architectural Principles to Apply

1. **Single Responsibility** — Each file/function does one thing.
2. **DRY (Don't Repeat Yourself)** — Shared patterns (image upload, toggle, delete confirmation) live in one place.
3. **Fail Loudly** — Validation errors and upload failures return structured error messages, not silent `null`.
4. **Environment Configuration** — Credentials come from the environment, not source code.
5. **Schema Completeness** — `database.sql` must be the single source of truth for the full schema; every column and table referenced in code must be present.
