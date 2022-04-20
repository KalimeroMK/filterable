<?php

    namespace Kalimeromk\Filterable;

    use Illuminate\Database\Eloquent\Model;

    class Filter extends Model
    {
        /**
         * @var array
         */
        protected $fillable = [];
        /**
         * set string fields for filtering
         * @var array
         */
        protected array $likeFields = [];

        /**
         * set boolean fields for filtering
         * @var array
         */
        protected array $boolFields = [];

        /**
         * add filtering.
         *
         * @param  $builder  : query builder.
         * @param  array  $filters  : array of filters.
         *
         */
        public function scopeFilter($builder, array $filters = [])
        {
            if ($filters) {
                $tableName      = $this->getTable();
                $FillableFields = $this->fillable;
                foreach ($filters as $field => $value) {
                    if (in_array($field, $this->boolFields) && $value != null) {
                        $builder->where($field, (bool)$value);
                        continue;
                    }
                    if ( ! in_array($field, $FillableFields) || ! $value) {
                        continue;
                    }
                    if (in_array($field, $this->likeFields)) {
                        $builder->where($tableName.'.'.$field, 'LIKE', "%$value%");
                    } elseif (is_array($value)) {
                        $builder->whereIn($field, $value);
                    } else {
                        $builder->where($field, $value);
                    }
                }

                return $builder;
            }

            return $builder;
        }
    }