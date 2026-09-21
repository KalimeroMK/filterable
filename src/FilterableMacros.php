<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FilterableMacros
{
    public static function register(): void
    {
        if (Builder::hasGlobalMacro('whereLike')) {
            return;
        }

        Builder::macro('whereLike', function (array|string $attributes, string $searchTerm): Builder {
            /** @var Builder $this */
            return $this->where(static function (Builder $query) use ($attributes, $searchTerm): void {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $attribute = (string) $attribute;

                    if (!Str::contains($attribute, '.')) {
                        $query->orWhere($attribute, 'LIKE', '%' . $searchTerm . '%');

                        continue;
                    }

                    $relation = Str::beforeLast($attribute, '.');
                    $column = Str::afterLast($attribute, '.');

                    $query->orWhereHas($relation, static function (Builder $query) use ($column, $searchTerm): void {
                        $query->where($column, 'LIKE', '%' . $searchTerm . '%');
                    });
                }
            });
        });
    }
}
