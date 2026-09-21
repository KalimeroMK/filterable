# Filterable - Laravel Package for Dynamic Filtering

**Filterable** is a Laravel package designed to simplify dynamic filtering and searching across models and their
relationships, eliminating the need for repetitive query code. It provides an easy-to-use trait and a query macro for
building powerful, dynamic query filters.

---

## Requirements

- PHP 8.1 – 8.5
- Laravel 10, 11 or 12

## Installation

Require the package via Composer:

```bash
composer require kalimeromk/filterable
```

The package uses package auto-discovery, so no manual registration is needed. If auto-discovery is disabled, register
the service provider yourself:

```php
// config/app.php
'providers' => [
    Kalimeromk\Filterable\FilterableServiceProvider::class,
];
```

---

## Usage

### 1. The `whereLike` macro

`whereLike` performs a `LIKE` search over several columns at once, including columns on related models. All conditions
are grouped and combined with `OR`, so it can be chained with other constraints safely.

```php
use App\Models\User;

$users = User::query()
    ->whereLike(['name', 'email'], 'John')
    ->get();
```

#### Searching related models

Use dot notation. Everything before the last dot is treated as the relation, so nested relations work too:

```php
$users = User::query()
    ->whereLike(['posts.title', 'posts.content'], 'Laravel')
    ->with('posts')
    ->get();

$users = User::query()
    ->whereLike(['posts.comments.body'], 'thanks')
    ->get();
```

A single attribute may be passed as a plain string: `->whereLike('email', 'john@')`.

---

### 2. The `Filterable` trait

The trait adds a `filter()` scope that turns a request payload into query constraints.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalimeromk\Filterable\Traits\Filterable;

class User extends Model
{
    use Filterable;

    protected $fillable = ['name', 'email', 'age', 'is_active'];

    // LIKE instead of exact match, for columns of this model
    protected $likeFields = ['name', 'email'];

    // cast to boolean before comparing
    protected $boolFields = ['is_active'];
}
```

#### How each filter key is resolved

| Filter key       | Condition applied                        | Requirement                          |
|------------------|------------------------------------------|--------------------------------------|
| `is_active`      | `where('users.is_active', (bool) $value)` | listed in `$boolFields`              |
| `name`           | `where('users.name', 'LIKE', '%value%')`  | in `$fillable` **and** `$likeFields` |
| `email`          | exact match                               | in `$fillable`                       |
| `['a', 'b']`     | `whereIn`                                 | in `$fillable`                       |
| `age_min`        | `where('users.age', '>=', $value)`        | **`age`** in `$fillable`             |
| `age_max`        | `where('users.age', '<=', $value)`        | **`age`** in `$fillable`             |

Keys that don't resolve to a fillable column are ignored, which keeps arbitrary request input from reaching the query.
`null` values are skipped, so absent query parameters don't narrow the result set.

> [!IMPORTANT]
> For `age_min` / `age_max` it is the **base column** (`age`) that must be in `$fillable` — not `age_min` itself.

`$likeFields` applies to this model's own columns. To search related models, use the `whereLike` macro.

#### Example controller

```php
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'is_active', 'age_min', 'age_max']);

        return response()->json(User::filter($filters)->get());
    }
}
```

**Request:**

```
GET /users?name=Jane&is_active=1&age_min=25&age_max=35
```

Returns active users whose name contains "Jane" and whose age is between 25 and 35.

---

## Testing

The package is tested with [Orchestra Testbench](https://github.com/orchestral/testbench):

```bash
composer install
composer test
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
