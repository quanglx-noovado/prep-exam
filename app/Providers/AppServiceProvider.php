<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Auth\AuthService;
use Src\Infrastructure\Auth\AuthServiceImplement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthService::class, AuthServiceImplement::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
