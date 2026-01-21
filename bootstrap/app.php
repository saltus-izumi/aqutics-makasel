<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', \App\Http\Middleware\SetRoleSessionConfig::class);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login');
            }

            if ($request->is('owner', 'owner/*')) {
                return route('owner.login');
            }

            // それ以外のデフォルト
            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
