<?php

namespace Kalimeromk\Filterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class FilterableMacros
{
    public static function register(): void
    {
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            return $this->where(function (Builder $query) use ($attributes, $searchTerm): void {
                foreach (Arr::wrap($attributes) as $attribute) {
                    if (str_contains($attribute, '.')) {
                        [$relationName, $relationAttribute] = explode('.', $attribute);
                        $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm): void {
                            $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                        });
                    } else {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
        });
    }
}