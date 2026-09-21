<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable\Traits;

use Illuminate\Database\Eloquent\Builder;

use function in_array;
use function is_array;
use function str_ends_with;
use function substr;

trait Filterable
{
    /**
     * Applies a set of request filters to the query.
     *
     * Keys are matched against the model's fillable columns. A key suffixed with `_min` or `_max`
     * is applied as a range condition on the column left of the suffix.
     *
     * @param array<string, mixed> $filters
     */
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $table = $this->getTable();
        $fillable = $this->getFillable();

        /** @var string[] $boolFields */
        $boolFields = $this->boolFields ?? [];

        foreach ($filters as $field => $value) {
            if ($value === null) {
                continue;
            }

            $field = (string) $field;

            if (in_array($field, $boolFields, true)) {
                $query->where($table . '.' . $field, (bool) $value);
                continue;
            }

            [$column, $operator] = $this->resolveFilterColumn($field);

            if (!in_array($column, $fillable, true)) {
                continue;
            }

            $this->applyFieldFilter($query, $table, $column, $operator, $value);
        }

        return $query;
    }

    /**
     * Splits a filter key into the column it targets and the comparison operator it implies.
     *
     * @return array{0: string, 1: string|null}
     */
    protected function resolveFilterColumn(string $field): array
    {
        if (str_ends_with($field, '_min')) {
            return [substr($field, 0, -4), '>='];
        }

        if (str_ends_with($field, '_max')) {
            return [substr($field, 0, -4), '<='];
        }

        return [$field, null];
    }

    protected function applyFieldFilter(
        Builder $query,
        string $table,
        string $column,
        ?string $operator,
        mixed $value,
    ): void {
        $qualified = $table . '.' . $column;

        if ($operator !== null) {
            if (!is_array($value)) {
                $query->where($qualified, $operator, $value);
            }

            return;
        }

        if (is_array($value)) {
            $query->whereIn($qualified, $value);

            return;
        }

        /** @var string[] $likeFields */
        $likeFields = $this->likeFields ?? [];

        if (in_array($column, $likeFields, true)) {
            $query->where($qualified, 'LIKE', '%' . $value . '%');

            return;
        }

        $query->where($qualified, $value);
    }
}
