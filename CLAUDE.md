# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

`osoobe/laravel-utilities` is a **Laravel package** (not a standalone application) that provides reusable Eloquent traits, static helper classes, Blueprint migration macros, a generic AJAX resource endpoint, and a base Artisan command. It targets Laravel 6–10. The namespace root is `Osoobe\Utilities`, mapped from `./src`.

## Commands

**Run all tests:**
```bash
vendor/bin/phpunit
# or
composer test
```

**Run a single test file:**
```bash
vendor/bin/phpunit tests/SomeTest.php
```

**Install dependencies:**
```bash
composer install
```

There is no build step, asset pipeline, or database migration in this package itself.

## Architecture

### Service Provider (`src/UtilitiesServiceProvider.php`)

The entry point for Laravel's auto-discovery. It does three things:

1. **Blueprint macros** — registers migration helpers callable inside `Schema::table()` / `Schema::create()` closures:
   - `$table->location()` / `$table->dropLocation()` / `$table->addLocationIndex()` / `$table->dropLocationIndex()` — five address columns (country, state, city, street_address, zip_code)
   - `$table->coordinates()` / `$table->dropCoordinates()` — latitude/longitude decimals
   - `$table->userstamp()` / `$table->dropUserstamp()` — polymorphic `creator` and `editor` morph columns
   - `$table->isActive()` / `$table->dropIsActive()` — `is_active` tinyInteger column

2. **Custom validators** — `phone` (delegates to `PhoneNumberHelper::isValid`) and `password` (reads pattern from `config('validation.password.pattern')` and message from `config('validation.password.message')` — these must be provided by the consuming app).

3. **Route loading** — loads `routes/web.php`, which registers `GET /api/resources/{slug}/{format}` as `api.resource.get`.

### Generic AJAX Resource System

A three-part system for serving paginated model data to Bootstrap Table and Select2 front-end widgets without writing per-model endpoints:

- **`config/api-endpoints.php`** — maps a slug string to a model config array with keys: `model`, `id_column`, `text_column`, `full_text_search` (array of columns), `includes` (fields to expose), `conditions`, and an optional `middleware` array and `helper` callable.
- **`AjaxController`** (`src/Http/Controllers/`) — uses `ResourceControllerTrait`. On construction it reads per-slug middleware from the config and applies it dynamically.
- **`ResourceControllerTrait`** (`src/Traits/`) — `getResource($request, $slug, $format)` dispatches to either `bootstrapTableResponse` (format `bst`, default) or `select2Response` (format `select2`). The `model_configs` array is injected into the request so downstream resources can read it.
- **`BootstrapTableCollection`** returns `{ rows: [...], total: N }`. **`Select2Collection`** / **`Select2Resource`** shape data for Select2's `{ id, text }` contract, reading `id_column` and `text_column` from `model_configs`.

To add a new resource endpoint, add an entry to `config/api-endpoints.php` in the consuming app — no new controller or route is needed.

### Eloquent Traits (`src/Traits/`)

Mix into Eloquent models as needed. Required columns per trait:

| Trait | Required columns / contract |
|---|---|
| `Active` | `is_active` (tinyint), `hidden` (tinyint) |
| `HasVerified` | `verified` (tinyint) |
| `IsDefault` | `is_default` (tinyint) |
| `Sorted` | `sort_order` |
| `HasSlug` | `slug` |
| `HasEmail` | `email`, `email_verified_at` |
| `TimeDiff` | `created_at`; optionally `expiry_date` |
| `Userstamp` | `creator_id`, `creator_type`, `editor_id`, `editor_type` (added via `$table->userstamp()` macro) |
| `HasFullTextSearch` / `FullTextSearchTrait` | MySQL FULLTEXT index (add via `MigrationHelper::addFullTextSearch`) |
| `Lang` | `lang` column; expects `App\Language` model in the consuming app |
| `SEO` | Abstract — must implement `getRouteURL()`, `getSEOTitleAttribute()`, `getSEODescriptionAttribute()` |
| `ModelDefaultTrait` | Abstract — must implement `defaultModelValues(): void`; fires on `creating` |
| `HasLocation` | `street_address`, `city`, `state`, `country`, `zip_code` — **deprecated since 1.0.0**, use the Blueprint macro instead |

`FullTextSearchTrait` and `HasFullTextSearch` are nearly identical; `FullTextSearchTrait` additionally provides `scopeOrFullTextSearch`. Prefer `FullTextSearchTrait` for new code.

`TimeDiff` methods that reference `expiry_date` silently return `"Non-Disclosure "` when the property is absent — this is intentional guard behaviour, not a bug.

### Helper Classes (`src/Helpers/`)

All static:

- **`Utilities`** — general-purpose: array/object accessors, percentage/average math, phone formatting, CSV read/write, email variation regex, model comparison.
- **`Str`** — extends `Illuminate\Support\Str`; adds `ucwords`, `ucsnake`, `nameParts`, `boolToString`.
- **`FormatHelper`** — renders URLs, emails, phone numbers as plain string, HTML `<a>`, or Markdown link. Auto-detects type when using `formatString()`.
- **`MigrationHelper`** — `addFullTextSearch($table, $columns, $index)` runs the raw `ALTER TABLE … ADD FULLTEXT` statement.
- **`PhoneNumberHelper`** — E.164 phone validation.
- **`Date`** — date utilities.
- **`GoogleMapsHelper`** / **`MapBoxHelper`** / **`LocationHelper`** — geocoding wrappers.
- **`ImageHelper`** — image utilities.

### Base Artisan Command (`src/Console/Command.php`)

Extends `Illuminate\Console\Command`. Set `$timer = true` on a subclass to automatically print elapsed time after the command finishes.

### Testing

Tests live in `tests/` and extend `Osoobe\Utilities\Tests\TestCase`, which extends Orchestra Testbench's `TestCase`. `getPackageProviders` already registers `UtilitiesServiceProvider`. Add environment setup in `getEnvironmentSetUp` as needed.
