<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalimeromk\Filterable\Traits\Filterable;

class User extends Model
{
    use Filterable;

    public $timestamps = false;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'age', 'tax', 'domain', 'is_active'];

    protected $boolFields = ['is_active'];

    protected $likeFields = ['name', 'email'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
