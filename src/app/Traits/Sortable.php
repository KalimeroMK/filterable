<?php

namespace Kalimeromk\Filterable\app\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Scope a query to sort results.
     *
     * @param Builder $query
     * @param Request $request
     *
     * @return Builder
     */
    public function scopeSort(Builder $query, Request $request): Builder
    {
        // Get sortable column
        $sortables = $this->sortables ?? [];

        // Get the column to sort
        $sort = $request->get('sort');

        // Get the direction of which to sort
        $direction = $request->get('direction', 'desc');

        // Ensure column to sort is part of model's sortables property
        // and that the direction is a valid value
        if ($sort && in_array($sort, $sortables) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        }

        // No sorting, return query
        return $query;
    }
}
