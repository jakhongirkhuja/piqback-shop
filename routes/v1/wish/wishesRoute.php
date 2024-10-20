<?php

use App\Http\Controllers\Api\v1\Wish\WishController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('wish')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[WishController::class, 'wishList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/add',[WishController::class, 'wishAdd']);
            Route::post('/edit',[WishController::class, 'wishEdit']);
        });
       
    });
});
