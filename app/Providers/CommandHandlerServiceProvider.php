<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Src\Application\Auth\Command\LoginCommand;
use Src\Application\Auth\Command\LoginHandler;
use Src\Application\Auth\Command\SendOtpCommand;
use Src\Application\Auth\Command\SendOtpHandler;
use Src\Application\Auth\Command\VerifyNewDeviceCommand;
use Src\Application\Auth\Command\VerifyNewDeviceHandler;
use Src\Application\Auth\Command\VerifyRemoveDeviceCommand;
use Src\Application\Auth\Command\VerifyRemoveDeviceHandler;

class CommandHandlerServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->singleton(CommandBus::class, function () {
            $handlers = [
                LoginCommand::class => app(LoginHandler::class),
                SendOtpCommand::class => app(SendOtpHandler::class),
                VerifyNewDeviceCommand::class => app(VerifyNewDeviceHandler::class),
                VerifyRemoveDeviceCommand::class => app(VerifyRemoveDeviceHandler::class)
            ];
            $locator = new InMemoryLocator($handlers);

            $middleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleInflector()
            );

            return new CommandBus([$middleware]);
        });
    }
}
