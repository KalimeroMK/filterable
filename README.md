# Filterable

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/kalimeromk/filterable.svg?style=flat-square)](https://packagist.org/packages/kalimeromk/filterable)

#### In Laravel, we commonly face the problem of adding repetitive filtering code, sorting and search as well this package will address this problem.

## Install

`composer require kalimeromk/filterable`

## Usage for Filtering property

#### To use Filterable trait we need to include trait into our model

``` bash
Use Filterable;
```

#### While adding Filterable trait in the model class, we need to add some properties as well.

```
$getFillable: Specify all the fields which exist in your table.
```


```
$boolFields:- Add fields on which you want to apply Boolean filtering.
```
## Examples

``` 
        protected array $getFillable = [
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

## Usage for Sort property

#### To use Sortable trait we need to include trait into our model

``` bash
Use Sortable;
```

#### While adding Sortable trait in the model class, we need to add some properties as well.

```
$sortable:- Add fields on which you want to apply sort property.
```

## Examples

``` 
        protected array $sortable = [
            'id',
            'name',
            'email',
            'address'
        ];
```

#### After all the above changes, now we only need to call sort() function with Request array data

Example below allows sorting for the columns: id, name, email, address

```
<?php

namespace App;

use Kalimeromk\Filterable\Trait\Sortable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Sortable;

    public $sortables = ['id', 'name', 'email', 'address'];
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

## Usage for whereLike property

#### To use whereLike search need first to specified all the table row you want to search try

```
$likeRows:- Add fields on which you want to apply whereLike property.
```

## Examples

``` 
    public const likeRows = [
        'name',
        'email',
        'address'
   ];
```

#### After all the above changes, now we only need to call whereLike() function with Request array data

Example below allows searching for the columns: name, email, address

```
<?php

namespace App;

use Kalimeromk\Filterable\Trait\Sortable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public const likeRows = [
        'name',
        'email',
        'address'
        ];
}
```

this method can be used to search try relation as well just update const with relation and row name for search

```bash
 public const likeRows = [
        'name',
        'email',
        'address',
        'country.name'
        ];
```

### whereLike usage

Below is an example of the usage of the whereLike method (query scope).

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
                  ->whereLike(User::likeRows, Arr::get($request, 'search'))
                  ->get();
		
		return view('users.index', compact('users'));
	}
}
```

NOTE: This also works with filter, sort and with
pagination

```bash
$users = $this->model->whereLike(User::likeRows, Arr::get($request, 'search'))->sort($request->all())->filter($request->all())->paginate(10)
```

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
