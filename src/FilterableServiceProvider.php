<?php

    namespace Kalimeromk\Filterable;

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
                __DIR__.'/Trait/Filterable.php' => app_path('/Trait/Filter.php'),
                __DIR__.'/Models/Filter.php'    => app_path('/Models/Filter.php'),
            ]);
        }

        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot(): void
        {
            //
        }
    }
