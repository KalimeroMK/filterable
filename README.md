# Filterable

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kalimeromk/filterable.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kalimeromk/filterable.svg?style=flat-square)](https://packagist.org/packages/kalimeromk/filterable)

#### In Laravel, we commonly face the problem of adding repetitive filtering code, this package will address this problem.

## Install

`composer require kalimeromk/filterable`

## Usage

#### To use Filterable trait we need to include trait into our model

``` bash
Use Filterable;
```

#### While adding Filterable trait in the model class, we need to add some properties.

```
$fillable: Specify all the fields which exist in your database(add if not exist).
```

```
$likeFields: Add the list of fields on which you want to apply “LIKE” filtering. Like the name, email, address ...
```

```
$boolFields:- Add fields on which you want to apply Boolean filtering.
```
## Examples

``` 
        protected $fillable = [
            'name',
            'email',
            'address',
        ];
```

```
        protected array $likeFields = [
            'name',
            'email',
            'address',
        ];
```

```
        protected array $boolFields = [
            'is_active',
        ];
```

#### After all the above changes, now we only need to call filter() function with Request array data

```
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
	protected $model;
  
	public function __construct(User $model)
	{
		$this->model = $model;
	}
  
	public function index(Request $request)
	{
		$users = $this->model
                  ->filter($request->all())
                  ->get();
		
		return view('users.index', compact('users'));
	}
}
```

## Introduction

This post aims to provide a simple example of how to use traits with Laravel Query Scopes. The trait will sort columns
based on "sort" and "direction" being found within the request.
The end result looks like this with a Post model as an example:

```
Post::sort(request())
```

### Model setup

The model setup requires 2 things:

1. Add a `use Sortable;` statement
2. Add a `sortable` public property which is an array of values that may be sorted

Example below allows sorting for the columns: id, views, published_at

```
<?php

namespace App;

use Kalimeromk\Filterable\Trait\Sortable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Sortable;

    public $sortables = ['id', 'views', 'published_at'];
}
```

### Trait usage

Below is an example of the usage of the sortable trait (query scope).

```
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
	protected $model;
  
	public function __construct(User $model)
	{
		$this->model = $model;
	}
  
	public function index(Request $request)
	{
		$users = $this->model
                  ->sort($request->all())
                  ->get();
		
		return view('users.index', compact('users'));
	}
}
```

NOTE: This also works with filter and with
pagination `$users = $this->model->sort($request->all())->filter()->paginate(10)`

### Resources

* [Laravel Query Scopes - Local Scopes](https://laravel.com/docs/5.5/eloquent#local-scopes)

## Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [kalimeromk](https://github.com/kalimeromk)
- [All Contributors](https://github.com/kalimeromk/filterable/contributors)

## Security
If you discover any security-related issues, please email zbogoevski@gmail.com or use the issue tracker.

## License
The MIT License (MIT). Please see [License File](/filterable/LICENSE.md) for more information.
