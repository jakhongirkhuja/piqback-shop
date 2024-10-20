<?php

use App\Http\Controllers\Api\v1\Academy\UserController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('academy')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/userInfo',[UserController::class, 'userInfo'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/phonenumber/search',[UserController::class, 'phoneNumberSearch'])->withoutMiddleware([CheckStandardAttributes::class]);
            // Route::get('/edit/{promocode_id}',[promocodeController::class, 'promocodeEditGet'])->withoutMiddleware([CheckStandardAttributes::class]);
            // Route::post('/user/{promocode_id}',[PromocodeController::class, 'promocodeEditSubmit']);
            // Route::post('/add',[PromocodeController::class, 'promocodeSubmit']);
            // Route::post('/delete/{promocode_id}',[PromocodeController::class, 'promocodeDelete']);
        });
    });
});
