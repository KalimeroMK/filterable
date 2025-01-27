<?php
namespace Kalimeromk\Filterable;

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
        FilterableMacros::register();

    }

    public function register(): void
    {
        //
    }
}

