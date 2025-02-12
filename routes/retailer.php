<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Retailers\DistributorPageController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/distributor/{id}', [DistributorPageController::class, 'show'])
        ->name('distributor.show');
});

