<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RecommendationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Contracts\RecommendationServiceInterface::class,
            \App\Services\RecommendationService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
