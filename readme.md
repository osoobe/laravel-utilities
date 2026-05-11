# Laravel Utilities

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/osoobe/utilities.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/osoobe/utilities.svg?style=flat-square)](https://packagist.org/packages/osoobe/utilities)

A collection of Eloquent model traits, static helper classes, migration Blueprint macros, a generic AJAX data endpoint, and a base Artisan command — designed to eliminate boilerplate across Laravel applications.

## Install

```bash
composer require osoobe/laravel-utilities
```

The service provider is auto-discovered. No manual registration needed.

## Table of Contents

- [Migration Blueprint Macros](#migration-blueprint-macros)
- [Eloquent Model Traits](#eloquent-model-traits)
- [Helper Classes](#helper-classes)
- [Generic AJAX Resource Endpoint](#generic-ajax-resource-endpoint)
- [Custom Validators](#custom-validators)
- [Base Artisan Command](#base-artisan-command)
- [Use Cases](#use-cases)
- [Testing](#testing)

---

## Migration Blueprint Macros

The package registers Blueprint macros that add common column groups with a single call.

### Location columns

```php
Schema::create('businesses', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->location();         // country, state, city, street_address, zip_code
    $table->addLocationIndex(); // adds individual indexes on each location column
    $table->timestamps();
});

// Rolling back
Schema::table('businesses', function (Blueprint $table) {
    $table->dropLocationIndex();
    $table->dropLocation();
});
```

### Coordinate columns

```php
Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->coordinates(); // latitude (decimal 16,13), longitude (decimal 16,13)
    $table->timestamps();
});

Schema::table('properties', function (Blueprint $table) {
    $table->dropCoordinates();
});
```

### Userstamp columns

Tracks which user created and last edited a record using polymorphic morphs, supporting multi-model auth setups.

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->userstamp(); // creator_id, creator_type, editor_id, editor_type
    $table->timestamps();
});

Schema::table('posts', function (Blueprint $table) {
    $table->dropUserstamp();
});
```

### Active status column

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->isActive(); // is_active tinyint, nullable, default 1
    $table->timestamps();
});
```

---

## Eloquent Model Traits

### `Active` — Active/inactive record scopes

Requires an `is_active` column (added via `$table->isActive()`).

```php
use Osoobe\Utilities\Traits\Active;

class Product extends Model
{
    use Active;
}

// Usage
Product::active()->get();       // WHERE is_active = 1
Product::notActive()->get();    // WHERE is_active != 1
Product::hidden()->get();       // WHERE hidden = 1
```

### `HasVerified` — Verified record scopes

Requires a `verified` column.

```php
use Osoobe\Utilities\Traits\HasVerified;

class Listing extends Model
{
    use HasVerified;
}

Listing::verified()->get();
Listing::notVerified()->get();
```

### `IsDefault` — Default record scopes

Requires an `is_default` column.

```php
use Osoobe\Utilities\Traits\IsDefault;

class PaymentMethod extends Model
{
    use IsDefault;
}

PaymentMethod::isDefault()->first();
PaymentMethod::notDefault()->get();
```

### `Sorted` — Sort order scope

Requires a `sort_order` column.

```php
use Osoobe\Utilities\Traits\Sorted;

class MenuItem extends Model
{
    use Sorted;
}

MenuItem::sorted()->get(); // ORDER BY sort_order ASC
```

### `HasSlug` — Slug-based lookup

```php
use Osoobe\Utilities\Traits\HasSlug;

class Article extends Model
{
    use HasSlug;
}

// Finds by slug OR id — useful for route model binding
$article = Article::findBySlugOrFail('my-article-title');
```

### `HasEmail` — Email verification scopes

```php
use Osoobe\Utilities\Traits\HasEmail;

class User extends Model
{
    use HasEmail;
}

User::emailVerified()->get();
User::emailNotVerified()->get();
User::email('user@example.com')->first();

// Users who verified their email within the last 7 days
User::emailVerifiedSince(7)->get();

// Check on an instance
if ($user->isEmailVerified()) { ... }
```

### `Userstamp` — Auto-fill creator/editor on save

Requires `userstamp()` Blueprint macro columns. Automatically sets `creator_*` on create and `editor_*` on update using `Auth::user()`.

```php
use Osoobe\Utilities\Traits\Userstamp;

class Invoice extends Model
{
    use Userstamp;
}

// No manual work needed — creator and editor are set automatically on save.
$invoice = Invoice::create(['amount' => 500]);
echo $invoice->creator_type; // "App\Models\User"
echo $invoice->creator_id;   // 42
```

### `TimeDiff` — Date/time scopes and human-readable diffs

```php
use Osoobe\Utilities\Traits\TimeDiff;

class JobPost extends Model
{
    use TimeDiff;
}

// Scopes
JobPost::createdToday()->get();
JobPost::createdSinceWeek()->get();
JobPost::recentlyCreated(3)->get();         // last 3 days
JobPost::recentlyCreated(2, 'subWeeks')->get(); // last 2 weeks
JobPost::betweenDates('created_at', $start, $end)->get();
JobPost::expired()->get();
JobPost::notExpired()->get();

// Accessor
echo $post->created_time_diff; // "3 days ago"

// Instance methods
$post->expireInDays(30);
$post->expireInHours(48);
if ($post->isExpired()) { ... }
if ($post->recentlyCreated(1)) { ... }
```

### `HasFullTextSearch` / `FullTextSearchTrait` — MySQL FULLTEXT search

Add the FULLTEXT index in your migration using `MigrationHelper`, then apply the trait to the model.

```php
// In migration
use Osoobe\Utilities\Helpers\MigrationHelper;

public function up()
{
    MigrationHelper::addFullTextSearch('articles', ['title', 'body'], 'articles_search');
}
```

```php
use Osoobe\Utilities\Traits\FullTextSearchTrait;

class Article extends Model
{
    use FullTextSearchTrait;
}

// Natural language search
Article::fullTextSearch(['title', 'body'], 'laravel utilities')->get();

// Boolean mode
Article::fullTextSearch(['title', 'body'], '+laravel -utilities', true)->get();

// OR version (FullTextSearchTrait only)
Article::orFullTextSearch(['title', 'body'], 'alternative terms')->get();

// Include relevance score in SELECT
$score = Article::selectFTSScore(['title', 'body'], 'laravel');
Article::selectRaw($score)->orderByDesc('fts_score')->get();
```

### `AdvanceWhereQuery` — Conditional where clauses

```php
use Osoobe\Utilities\Traits\AdvanceWhereQuery;

class Order extends Model
{
    use AdvanceWhereQuery;
}

// Applies the WHERE only when $value is non-empty; skips it otherwise
Order::whereKeyOrNull('status', $request->status)->get();
```

### `ModelDefaultTrait` — Set default values before create

```php
use Osoobe\Utilities\Traits\ModelDefaultTrait;

class Subscription extends Model
{
    use ModelDefaultTrait;

    public function defaultModelValues(): void
    {
        $this->status = $this->status ?? 'trial';
        $this->trial_ends_at = $this->trial_ends_at ?? now()->addDays(14);
    }
}
```

### `SEO` — SEO attribute contract

```php
use Osoobe\Utilities\Traits\SEO;

class Product extends Model
{
    use SEO;

    public function getRouteURL(): string
    {
        return route('products.show', $this->slug);
    }

    public function getSEOTitleAttribute(): string
    {
        return $this->name . ' | My Shop';
    }

    public function getSEODescriptionAttribute(): string
    {
        return $this->summary;
    }
}

// $product->url returns getRouteURL()
// $product->seo_title and $product->seo_description are accessible as attributes
```

### `Lang` — Locale-based scopes

Requires a `lang` column and an `App\Language` model in the consuming app.

```php
use Osoobe\Utilities\Traits\Lang;

class Post extends Model
{
    use Lang;
}

Post::lang()->get();   // WHERE lang = current locale
Post::langEN()->get(); // WHERE lang = 'en'
```

---

## Helper Classes

All helpers use static methods and require no instantiation.

### `Utilities`

General-purpose utilities for objects, arrays, math, and strings.

```php
use Osoobe\Utilities\Helpers\Utilities;

// Safe object/array access
$name = Utilities::getObjectValue($user, 'name', 'Guest');
$name = Utilities::getObjectValue($user, ['display_name', 'name'], 'Guest'); // tries each key
$val  = Utilities::getArrayValue($data, 'key', 'default');

// Only set if the value is non-empty
Utilities::setObjectValue($model, 'bio', $request->bio);
Utilities::setArrayValue($data, 'phone', $request->phone);
Utilities::setArrayValueIfEmpty($settings, 'theme', 'light');

// Math
Utilities::calcNumberPercentage(15, 200); // 15% of 200 = 30
Utilities::calc_percentage(30, 200);      // 30/200 as percentage = 15
Utilities::calcAverage(10.0, 20.0, 30.0); // 20.0
Utilities::calcAverageNoZeros(0, 10.0, 20.0); // ignores zeros

// Phone
Utilities::formatPhoneNumber('5551234567');   // "+15551234567"
Utilities::formatPhoneNumber('+15551234567'); // "+15551234567"

// Compare two Eloquent models
Utilities::model_compare($userA, $userB); // true if same class and id

// Email variation regex (handles dots and + aliases)
$regex = Utilities::getEmailVariationRegex('john.doe+tag@gmail.com');

// Array helpers
Utilities::isAssociativeArray(['a' => 1]); // true
Utilities::toAssociativeArray(['a', 'b']); // ['a' => 'a', 'b' => 'b']
Utilities::removeEmpty([0, '', null, 'hello']); // [0, 'hello']

// CSV
$rows = Utilities::csvToArray('/path/to/file.csv');
Utilities::outputCSV('/path/to/output.csv', $rows);
```

### `Str`

Extends `Illuminate\Support\Str` — all built-in Laravel string methods are available.

```php
use Osoobe\Utilities\Helpers\Str;

Str::ucwords('hello world');       // "Hello World"
Str::ucsnake('helloWorld');        // "Hello_World"
Str::boolToString(true);           // "Yes"
Str::boolToString(false, 'B');     // "False"

$parts = Str::nameParts('John Michael Doe');
// $parts->first_name  = "John"
// $parts->middle_name = "Michael"
// $parts->last_name   = "Doe"
```

### `FormatHelper`

Renders URLs, emails, and phone numbers as plain text, HTML anchors, or Markdown links.

```php
use Osoobe\Utilities\Helpers\FormatHelper;

// Auto-detects type
FormatHelper::formatString('https://example.com', 'html');
// <a class='lm-format' href='https://example.com'>https://example.com</a>

FormatHelper::formatString('user@example.com', 'markdown', 'Contact');
// [Contact](mailto:user@example.com)

FormatHelper::formatPhone('+15551234567', 'html', 'Call Us');
// <a class='lm-format' href='tel:+15551234567'>Call Us</a>

FormatHelper::formatEmail('user@example.com', 'markdown');
// [user@example.com](mailto:user@example.com)
```

Supported `$format` values: `'html'`, `'markdown'`, `'string'` (default).

### `Date`

```php
use Osoobe\Utilities\Helpers\Date;

$period = Date::getStartEndDate(Carbon::now(), 'weekly');
// $period->start_date  (start of week)
// $period->end_date    (end of week)
// $period->date        (the input date)

// Supported periods: 'daily'/'day', 'weekly'/'week', 'monthly'/'month'

Date::isBetweenPeriod(Carbon::now(), 'monthly'); // true if today is within the current month
```

### `MigrationHelper`

```php
use Osoobe\Utilities\Helpers\MigrationHelper;

// Add a MySQL FULLTEXT index
MigrationHelper::addFullTextSearch('products', ['name', 'description'], 'products_search');
```

### `PhoneNumberHelper`

Validation pattern is configurable via `config('validation.phone.pattern')`.

```php
use Osoobe\Utilities\Helpers\PhoneNumberHelper;

PhoneNumberHelper::isValid('+1 (555) 123-4567'); // true
PhoneNumberHelper::isValid('123');               // false (too short)
```

### `ImageHelper`

```php
use Osoobe\Utilities\Helpers\ImageHelper;

// Store a base64 image from an API request or form upload
$path = ImageHelper::storeBase64Image(
    $request->avatar,       // data:image/png;base64,...
    'avatars',              // directory
    'user-42',              // filename (extension auto-detected)
    'public',               // filesystem disk
    'public'                // visibility
);
// Returns the stored path, e.g. "avatars/user-42.png", or false on failure

// Strip the data URI prefix
$raw = ImageHelper::base64Only('data:image/jpeg;base64,/9j/4AAQ...');
```

### `MapBoxHelper`

```php
use Osoobe\Utilities\Helpers\MapBoxHelper;

$data = MapBoxHelper::queryLocationData(config('services.mapbox.key'), '1600 Pennsylvania Ave NW, Washington DC');
$cords = MapBoxHelper::getCordsFromData($data);
// $cords->latitude, $cords->longitude

$address = MapBoxHelper::getAddressComponent($data);
// $address->street_address, ->city, ->state, ->state_short,
// ->country, ->country_short, ->zip_code, ->latitude, ->longitude, ->full_address

// Reverse geocode
$data = MapBoxHelper::queryCoordinates(config('services.mapbox.key'), 38.8977, -77.0365);
```

### `GoogleMapsHelper`

```php
use Osoobe\Utilities\Helpers\GoogleMapsHelper;

$cords = GoogleMapsHelper::getGoogleMapCordinates(
    config('services.google.maps_key'),
    '1600 Pennsylvania Ave NW Washington DC'
);
// ['latitude' => 38.897..., 'longitude' => -77.036...]
```

---

## Generic AJAX Resource Endpoint

The package provides a single parameterized route that serves model data to front-end libraries (Bootstrap Table, Select2) without writing per-model controllers or routes.

**Route:** `GET /api/resources/{slug}/{format}`  
**Name:** `api.resource.get`  
**Formats:** `bst` (Bootstrap Table, default), `select2`

### 1. Publish and configure `api-endpoints.php`

Copy the config file to your application:

```bash
php artisan vendor:publish --provider="Osoobe\Utilities\UtilitiesServiceProvider"
```

Then define each resource in `config/api-endpoints.php`:

```php
return [
    'users' => [
        'model'            => \App\Models\User::class,
        'id_column'        => 'id',
        'text_column'      => 'name',
        'full_text_search' => ['name', 'email'],
        'includes'         => ['id', 'name', 'email', 'avatar_url'],
        'conditions'       => ['is_active' => 1],
        // 'middleware'    => ['auth:sanctum'],
        // 'helper'        => fn($request, $query, $configs) => $query->withRole('editor'),
    ],
    'categories' => [
        'model'       => \App\Models\Category::class,
        'id_column'   => 'id',
        'text_column' => 'name',
        'includes'    => ['id', 'name', 'slug'],
        'conditions'  => [],
    ],
];
```

### 2. Call the endpoint from the front end

**Bootstrap Table:**
```javascript
$('#data-table').bootstrapTable({
    url: '/api/resources/users/bst',
    queryParams: params => ({ ...params, search: params.searchText }),
});
```

Response shape:
```json
{ "rows": [...], "total": 120 }
```

**Select2:**
```javascript
$('#user-select').select2({
    ajax: {
        url: '/api/resources/users/select2',
        dataType: 'json',
        data: params => ({ term: params.term, paginate: true }),
    }
});
```

Response shape:
```json
{ "results": [{ "id": 1, "text": "Jane Doe", "email": "jane@example.com" }, ...] }
```

### Query parameters supported

| Parameter | Description |
|---|---|
| `term` / `search` | Text search (FULLTEXT or `LIKE` depending on config) |
| `sort` | Column to sort by (default: `id`) |
| `order` | `asc` or `desc` (default: `asc`) |
| `limit` | Number of records |
| `offset` | Pagination offset (Bootstrap Table) |
| `paginate` | `true` to enable cursor pagination (Select2) |

---

## Custom Validators

Two validation rules are registered automatically.

### `phone`

```php
$request->validate([
    'mobile' => 'required|phone',
]);
```

Override the pattern in `config/validation.php`:

```php
'phone' => [
    'pattern' => '%^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$%i',
],
```

### `password`

```php
$request->validate([
    'password' => 'required|min:8|password',
]);
```

Requires the consuming app to publish a `config/validation.php` with:

```php
'password' => [
    'pattern' => '^(?=.*[A-Z])(?=.*\d).{8,}$',
    'message' => 'The :attribute must contain at least one uppercase letter and one number.',
],
```

---

## Base Artisan Command

Extend `Osoobe\Utilities\Console\Command` to get automatic execution timing.

```php
use Osoobe\Utilities\Console\Command;

class SyncInventory extends Command
{
    protected $timer = true; // prints "Took 2 minutes to complete" after handle()
    protected $signature = 'inventory:sync';

    public function handle()
    {
        // long-running work
    }
}
```

---

## Use Cases

### Multi-tenant CRM with creator tracking

Use `Userstamp` + `Active` to track who created/edited every record and filter inactive ones:

```php
class Contact extends Model
{
    use Userstamp, Active;
}

// In migration
$table->userstamp();
$table->isActive();

// Query active contacts created by a specific user
Contact::active()->where('creator_id', auth()->id())->get();
```

### Job board with expiry and full-text search

Use `TimeDiff` + `FullTextSearchTrait` for a job listing model:

```php
class JobPost extends Model
{
    use TimeDiff, FullTextSearchTrait;
}

// Active, non-expired listings matching a keyword, sorted by relevance
$score = JobPost::selectFTSScore(['title', 'description'], $keyword);
JobPost::notExpired()
    ->selectRaw("*, $score")
    ->fullTextSearch(['title', 'description'], $keyword)
    ->orderByDesc('fts_score')
    ->get();
```

### Geocoding an address on model save

Combine `MapBoxHelper` with a model observer or `ModelDefaultTrait`:

```php
class Property extends Model
{
    use ModelDefaultTrait;

    public function defaultModelValues(): void
    {
        if ($this->street_address && !$this->latitude) {
            $data = MapBoxHelper::queryLocationData(
                config('services.mapbox.key'),
                $this->street_address . ', ' . $this->city
            );
            $cords = MapBoxHelper::getCordsFromData($data);
            if ($cords) {
                $this->latitude  = $cords->latitude;
                $this->longitude = $cords->longitude;
            }
        }
    }
}
```

### Searchable admin panel with Bootstrap Table + Select2

Register your models in `config/api-endpoints.php` and drop in the JS widgets — no backend changes needed when adding new searchable models.

```php
// config/api-endpoints.php
'products' => [
    'model'            => \App\Models\Product::class,
    'id_column'        => 'id',
    'text_column'      => 'name',
    'full_text_search' => ['name', 'sku', 'description'],
    'includes'         => ['id', 'name', 'sku', 'price'],
    'conditions'       => ['is_active' => 1],
    'middleware'       => ['auth:sanctum'],
],
```

```javascript
// Bootstrap Table
$('#products-table').bootstrapTable({ url: '/api/resources/products/bst' });

// Select2 product picker
$('#product-picker').select2({ ajax: { url: '/api/resources/products/select2' } });
```

### Storing user avatar uploads as base64

```php
public function updateAvatar(Request $request)
{
    $path = ImageHelper::storeBase64Image(
        $request->input('avatar'),
        'avatars/' . auth()->id(),
        'profile',
        'public',
        'public'
    );
    if ($path) {
        auth()->user()->update(['avatar_path' => $path]);
    }
}
```

### SEO-ready product pages

```php
class Product extends Model
{
    use SEO, HasSlug;

    public function getRouteURL(): string
    {
        return route('products.show', $this->slug);
    }

    public function getSEOTitleAttribute(): string
    {
        return $this->name . ' — ' . config('app.name');
    }

    public function getSEODescriptionAttribute(): string
    {
        return Str::limit($this->description, 155);
    }
}
```

```blade
{{-- In layout --}}
<title>{{ $product->seo_title }}</title>
<meta name="description" content="{{ $product->seo_description }}">
<link rel="canonical" href="{{ $product->url }}">
```

---

## Testing

```bash
vendor/bin/phpunit

# Single file
vendor/bin/phpunit tests/Helpers/UtilitiesTest.php
```

Tests extend `Osoobe\Utilities\Tests\TestCase` (Orchestra Testbench), which auto-registers the service provider.

## Changelog

Please see [CHANGELOG](changelog.md) for recent changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email b4.oshany@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
