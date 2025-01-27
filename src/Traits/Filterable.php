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
                $query->where($field, (bool)$value);
            } elseif (in_array($field, $fillableFields) && $value !== null) {
                $this->applyFieldFilter($query, $tableName, $field, $value);
            }
        }

        return $query;
    }

    protected function applyFieldFilter($query, $tableName, $field, $value)
    {
        if (in_array($field, $this->likeFields ?? [])) {
            $query->where($tableName . '.' . $field, 'LIKE', "%$value%");
        } elseif (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }
}