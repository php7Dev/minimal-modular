<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\ModuleLoader;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withProviders([
    ...ModuleLoader::providers()
    ])
        ->withCommands([
        App\Console\Commands\ModuleMakeAll::class,
        App\Console\Commands\ModuleList::class,
        App\Console\Commands\ModuleEnable::class,
        App\Console\Commands\ModuleDisable::class,
        App\Console\Commands\ModuleMigrate::class,
    ])
    ->create();
