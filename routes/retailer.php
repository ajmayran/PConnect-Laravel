<?php

use App\Http\Controllers\DistributorPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/distributor/{id}', [DistributorPageController::class, 'show'])->name('distributor.show');
    
});