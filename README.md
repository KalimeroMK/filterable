
# Filterable - Laravel Package for Dynamic Filtering

**Filterable** is a Laravel package designed to simplify dynamic filtering and searching across models and their relationships, eliminating the need for repetitive query code. It provides an easy-to-use trait and macros for building powerful, dynamic query filters.

---

## Installation

1. Require the package via Composer:
   ```bash
   composer require kalimeromk/filterable
   ```

2. The package supports auto-discovery, so no manual registration of the service provider is needed. However, if you are using an older Laravel version, add the service provider to your `config/app.php`:
   ```php
   'providers' => [
       Kalimeromk\Filterable\PackageServiceProvider::class,
   ];
   ```

---

## Usage

### 1. Using `whereLike` Macro

The `whereLike` macro allows you to perform "LIKE" searches on multiple fields, including related model fields.

#### Example:

```php
use App\Models\User;

$users = User::query()
    ->whereLike(['name', 'email'], 'John')
    ->get();
```

This will return all users where the `name` or `email` fields contain the string "John".

#### Searching on Related Models:

```php
$users = User::query()
    ->whereLike(['posts.title', 'posts.content'], 'Laravel')
    ->with('posts')
    ->get();
```

This will return all users who have posts with a title or content containing the string "Laravel".

---

### 2. Using the `Filterable` Trait

The `Filterable` trait allows you to dynamically filter a model based on specific criteria, including `LIKE` queries, boolean fields, and `whereIn` filters.

#### Setup:
Add the `Filterable` trait to your model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalimeromk\Filterable\Traits\Filterable;

class User extends Model
{
    use Filterable;

    protected $fillable = ['name', 'email', 'is_active'];

    // Define fields for specific filters
    protected $boolFields = ['is_active'];
    protected $likeFields = ['name', 'email'];
}
```

#### Example Controller:

```php
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'is_active']);

        $users = User::filter($filters)->get();

        return response()->json($users);
    }
}
```

#### Example API Request:
- **Request:**
  ```
  GET /users?name=Jane&is_active=true
  ```
- **Result:**
  Returns all active users (`is_active = true`) whose name contains "Jane".

---

## Testing the Package

This package uses [Orchestra Testbench](https://github.com/orchestral/testbench) for testing.

### Running Tests

To run the tests:

1. Install the dependencies:
   ```bash
   composer install
   ```

2. Run PHPUnit:
   ```bash
   vendor/bin/phpunit
   ```

---

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
