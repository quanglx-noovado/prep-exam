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

class CommandHandlerServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->singleton(CommandBus::class, function () {
            $handlers = [
                LoginCommand::class => app(LoginHandler::class),
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
