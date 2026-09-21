<?php

declare(strict_types=1);

namespace Kalimeromk\Filterable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalimeromk\Filterable\FilterableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->integer('age')->nullable();
            $table->integer('tax')->nullable();
            $table->string('domain')->nullable();
            $table->boolean('is_active')->default(false);
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title');
            $table->text('content');
        });
    }

    protected function getPackageProviders($app): array
    {
        return [FilterableServiceProvider::class];
    }
}
