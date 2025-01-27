<?php

namespace Kalimeromk\Filterable\Traits;

trait Filterable
{
    public function scopeFilter($query, array $filters = [])
    {
        $tableName = $this->getTable();
        $fillableFields = $this->getFillable();

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->boolFields ?? []) && $value !== null) {
                // Boolean filtering
                $query->where($field, (bool) $value);
            } elseif (in_array($field, $fillableFields) && $value !== null) {
                // Apply filter based on field type
                $this->applyFieldFilter($query, $tableName, $field, $value);
            }
        }

        return $query;
    }

    protected function applyFieldFilter($query, string $tableName, string $field, $value): void
    {
        if (in_array($field, $this->likeFields ?? [])) {
            // LIKE filtering
            $query->where($tableName . '.' . $field, 'LIKE', "%$value%");
        } elseif (is_array($value)) {
            // Automatically use whereIn for array values
            $query->whereIn($tableName . '.' . $field, $value);
        } elseif (str_ends_with($field, '_min')) {
            // Use >= for minimum values
            $query->where($tableName . '.' . rtrim($field, '_min'), '>=', $value);
        } elseif (str_ends_with($field, '_max')) {
            // Use <= for maximum values
            $query->where($tableName . '.' . rtrim($field, '_max'), '<=', $value);
        } else {
            // Default equality filter
            $query->where($tableName . '.' . $field, $value);
        }
    }
}