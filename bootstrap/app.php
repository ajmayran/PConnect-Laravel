<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RetailerMiddleware;
use App\Http\Middleware\ApprovedDistributor;
use App\Http\Middleware\DistributorMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'approved' => ApprovedDistributor::class,
            'distributor' => DistributorMiddleware::class,
            'retailer' => RetailerMiddleware::class,
            'role'  => CheckRole::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
