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
        $middleware->alias([
            'auth.session' => \App\Http\Middleware\AuthenticateSession::class,
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'access.control' => \App\Http\Middleware\AccessControl::class,
            // Role-based middleware
            'root.only' => \App\Http\Middleware\RootOnly::class,
            'customer.only' => \App\Http\Middleware\CustomerOnly::class,
            'customer.or.admin' => \App\Http\Middleware\CustomerOrAdminOnly::class,
            'seller.only' => \App\Http\Middleware\SellerOnly::class,
            'role.redirect' => \App\Http\Middleware\RoleBasedRedirect::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
