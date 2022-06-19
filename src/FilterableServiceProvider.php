<?php

    namespace Kalimeromk\Filterable;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Arr;
    use Illuminate\Support\ServiceProvider;

    class FilterableServiceProvider extends ServiceProvider
    {
        /**
         * Register services.
         *
         * @return void
         */
        public function register(): void
        {
            $this->publishes([
                __DIR__.'/Trait/Filterable.php' => app_path('/Trait/Filterable.php'),
                __DIR__.'/Trait/Sortable.php'   => app_path('/Trait/Sortable.php'),
        
            ]);
        }
    
        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot(): void
        {
            Builder::macro('whereLike', function ($attributes, string $searchTerm) {
                $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (Arr::wrap($attributes) as $attribute) {
                        $query->when(
                            str_contains($attribute, '.'),
                            function (Builder $query) use ($attribute, $searchTerm) {
                                [$relationName, $relationAttribute] = explode('.', $attribute);
                                $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                    $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                                });
                            },
                            function (Builder $query) use ($attribute, $searchTerm) {
                                $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                            }
                        );
                    }
                });
            
                return $this;
            });
        }
    }
