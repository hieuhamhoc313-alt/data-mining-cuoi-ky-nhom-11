<?php

namespace App\Providers;

use App\Repositories\PropertyRepository;
use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PropertyRepositoryInterface::class,
            PropertyRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
