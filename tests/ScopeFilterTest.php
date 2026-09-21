<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable\Tests;

use Kalimeromk\Filterable\Tests\Models\User;

final class ScopeFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::query()->insert([
            ['name' => 'John Doe', 'email' => 'john@example.com', 'age' => 20, 'tax' => 5, 'domain' => 'a.com', 'is_active' => true],
            ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'age' => 30, 'tax' => 10, 'domain' => 'b.com', 'is_active' => true],
            ['name' => 'Mark Smith', 'email' => 'mark@example.org', 'age' => 40, 'tax' => 20, 'domain' => 'c.com', 'is_active' => false],
        ]);
    }

    public function testFiltersByExactValue(): void
    {
        $this->assertSame(['Mark Smith'], $this->names(['domain' => 'c.com']));
    }

    public function testFiltersLikeFieldsWithPartialMatch(): void
    {
        $this->assertSame(['John Doe', 'Jane Doe'], $this->names(['name' => 'Doe']));
    }

    public function testFiltersBooleanFields(): void
    {
        $this->assertSame(['John Doe', 'Jane Doe'], $this->names(['is_active' => '1']));
        $this->assertSame(['Mark Smith'], $this->names(['is_active' => '0']));
    }

    public function testFiltersArrayValuesWithWhereIn(): void
    {
        $this->assertSame(['John Doe', 'Mark Smith'], $this->names(['domain' => ['a.com', 'c.com']]));
    }

    public function testIgnoresNullValues(): void
    {
        $this->assertCount(3, $this->names(['domain' => null]));
    }

    public function testIgnoresFieldsThatAreNotFillable(): void
    {
        $this->assertCount(3, $this->names(['id' => 1]));
    }

    public function testFiltersByMinimumValue(): void
    {
        $this->assertSame(['Jane Doe', 'Mark Smith'], $this->names(['age_min' => 30]));
    }

    public function testFiltersByMaximumValue(): void
    {
        $this->assertSame(['John Doe', 'Jane Doe'], $this->names(['age_max' => 30]));
    }

    public function testFiltersByMinimumAndMaximumTogether(): void
    {
        $this->assertSame(['Jane Doe'], $this->names(['age_min' => 25, 'age_max' => 35]));
    }

    public function testMaxSuffixDoesNotCorruptColumnName(): void
    {
        $this->assertSame(['John Doe', 'Jane Doe'], $this->names(['tax_max' => 10]));
    }

    public function testMinSuffixDoesNotCorruptColumnName(): void
    {
        $this->assertSame(['Mark Smith'], $this->names(['domain_min' => 'c.com']));
    }

    public function testRangeFilterIsIgnoredWhenBaseColumnIsNotFillable(): void
    {
        $this->assertCount(3, $this->names(['id_min' => 2]));
    }

    /**
     * @return string[]
     */
    private function names(array $filters): array
    {
        return User::query()->filter($filters)->orderBy('id')->pluck('name')->all();
    }
}
