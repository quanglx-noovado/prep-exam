<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Auth\Repository\DeviceRepository;
use Src\Domain\Auth\Repository\UserRepository;
use Src\Infrastructure\Persistence\Mysql\MysqlDeviceRepository;
use Src\Infrastructure\Persistence\Mysql\MysqlUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserRepository::class, MysqlUserRepository::class);
        $this->app->singleton(DeviceRepository::class, MysqlDeviceRepository::class);
    }
}
