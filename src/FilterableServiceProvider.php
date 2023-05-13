namespace Kalimeromk\Filterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class FilterableServiceProvider extends ServiceProvider
{
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
                    $this->buildWhereLikeQuery($query, $attribute, $searchTerm);
                }
            });
        });
    }

    /**
     * Build the "where like" query for the given attribute and search term.
     *
     * @param  Builder  $query
     * @param  string  $attribute
     * @param  string  $searchTerm
     */
    private function buildWhereLikeQuery(Builder $query, string $attribute, string $searchTerm): void
    {
        if (str_contains($attribute, '.')) {
            [$relationName, $relationAttribute] = explode('.', $attribute);

            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
            });
        } else {
            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
        }
    }
}

