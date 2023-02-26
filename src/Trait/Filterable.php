<?php

namespace Kalimeromk\Filterable\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Add filtering to the query builder.
     *
     * @param Builder $query The query builder instance.
     * @param array $filters Array of filters.
     *
     * @return Builder
     */
    public function scopeFilter($query, array $filters = [])
    {
        $tableName = $this->getTable();
        $fillableFields = $this->getFillable();

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->boolFields) && $value !== null) {
                $query->where($field, (bool)$value);
            } elseif (in_array($field, $fillableFields) && $value !== null) {
                if (in_array($field, $this->likeFields) && is_numeric($value)) {
                    $query->where($tableName . '.' . $field, 'LIKE', "$value");
                } elseif (in_array($field, $this->likeFields)) {
                    $query->where($tableName . '.' . $field, 'LIKE', "%$value%");
                } elseif (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
}
