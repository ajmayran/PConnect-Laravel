<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RetailerMiddleware;
use App\Http\Middleware\ApprovedDistributor;
use App\Http\Middleware\CheckDistributorBlock;
use App\Http\Middleware\CheckRetailerCredentials;
use App\Http\Middleware\EnsureProfileIsCompleted;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'approved' => ApprovedDistributor::class,
            'profile.completed' => EnsureProfileIsCompleted::class,
            'retailer' => RetailerMiddleware::class,
            'admin' => AdminMiddleware::class,
            'checkRole'  => CheckRole::class,
            'check.distributor.block' => CheckDistributorBlock::class,
            'check.retailer.credentials' => CheckRetailerCredentials::class,
            'check.subscription' => CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
