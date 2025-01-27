<?php

namespace Kalimeromk\Filterable\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kalimeromk\Filterable\FilterableServiceProvider;
use Kalimeromk\Filterable\Traits\Filterable;
use Orchestra\Testbench\TestCase;

class FilterableTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FilterableServiceProvider::class];
    }

    /** @test */
    public function it_can_filter_records_using_relationships()
    {
        // Create users table
        DB::statement('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)');

        // Create posts table
        DB::statement('CREATE TABLE posts (id INTEGER PRIMARY KEY, user_id INTEGER, title TEXT, content TEXT)');

        // Insert users
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
        ]);

        // Insert posts
        DB::table('posts')->insert([
            ['id' => 1, 'user_id' => 1, 'title' => 'Learning Laravel', 'content' => 'This is a Laravel tutorial.'],
            ['id' => 2, 'user_id' => 1, 'title' => 'Advanced Laravel', 'content' => 'Deep dive into Laravel features.'],
            ['id' => 3, 'user_id' => 2, 'title' => 'Getting Started', 'content' => 'Introduction to programming.'],
        ]);

        // Dynamically define the Post model
        $postModel = new class extends Model {
            protected $table = 'posts';
            protected $fillable = ['user_id', 'title', 'content'];

            public function user()
            {
                return $this->belongsTo(\Kalimeromk\Filterable\Tests\UserModel::class, 'user_id');
            }
        };

        // Dynamically define the User model
        $userModel = new class extends Model {
            use Filterable;

            protected $table = 'users';
            protected $fillable = ['name', 'email'];
            protected $likeFields = ['name', 'email', 'posts.title', 'posts.content'];

            public function posts()
            {
                return $this->hasMany(get_class(new class extends Model {
                    protected $table = 'posts';
                    protected $fillable = ['user_id', 'title', 'content'];
                }), 'user_id');
            }
        };

        // Filter users based on related post content
        $results = $userModel::query()
            ->whereLike(['posts.title', 'posts.content'], 'Laravel')
            ->with('posts')
            ->get();

        // Assertions
        $this->assertCount(1, $results);
        $this->assertEquals('John Doe', $results->first()->name);
        $this->assertCount(2, $results->first()->posts); // John has 2 posts related to Laravel
    }
}