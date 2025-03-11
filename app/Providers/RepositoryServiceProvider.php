<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Auth\UserRepository;
use Src\Infrastructure\Persistence\Mysql\MysqlUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserRepository::class, MysqlUserRepository::class);
    }
}
