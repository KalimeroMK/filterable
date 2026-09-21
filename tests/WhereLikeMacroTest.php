<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable\Tests;

use Illuminate\Database\Eloquent\Builder;
use Kalimeromk\Filterable\Tests\Models\Post;
use Kalimeromk\Filterable\Tests\Models\User;

final class WhereLikeMacroTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::query()->insert([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'is_active' => true],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.org', 'is_active' => true],
        ]);

        Post::query()->insert([
            ['user_id' => 1, 'title' => 'Learning Laravel', 'content' => 'A Laravel tutorial.'],
            ['user_id' => 1, 'title' => 'Advanced Laravel', 'content' => 'Deep dive into features.'],
            ['user_id' => 2, 'title' => 'Getting Started', 'content' => 'Introduction to programming.'],
        ]);
    }

    public function testMacroIsRegistered(): void
    {
        $this->assertTrue(Builder::hasGlobalMacro('whereLike'));
    }

    public function testSearchesOwnColumns(): void
    {
        $this->assertSame(['Jane Doe'], $this->names(['name', 'email'], 'example.org'));
    }

    public function testSearchesAcrossMultipleColumnsWithOr(): void
    {
        $this->assertSame(['John Doe', 'Jane Doe'], $this->names(['name', 'email'], 'Doe'));
    }

    public function testAcceptsASingleAttributeAsString(): void
    {
        $this->assertSame(['John Doe'], $this->names('email', 'john@'));
    }

    public function testSearchesRelatedColumns(): void
    {
        $this->assertSame(['John Doe'], $this->names(['posts.title', 'posts.content'], 'Laravel'));
    }

    public function testSearchesNestedRelations(): void
    {
        $this->assertSame(['John Doe'], $this->names(['posts.user.email'], 'john@'));
    }

    public function testKeepsOtherConstraintsIntact(): void
    {
        $names = User::query()
            ->where('is_active', true)
            ->whereLike(['name'], 'Doe')
            ->orderBy('id')
            ->pluck('name')
            ->all();

        $this->assertSame(['John Doe', 'Jane Doe'], $names);
    }

    /**
     * @param string[]|string $attributes
     *
     * @return string[]
     */
    private function names(array|string $attributes, string $term): array
    {
        return User::query()->whereLike($attributes, $term)->orderBy('id')->pluck('name')->all();
    }
}
