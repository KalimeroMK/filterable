<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable;

use Illuminate\Support\ServiceProvider;

final class FilterableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilterableMacros::register();
    }
}
