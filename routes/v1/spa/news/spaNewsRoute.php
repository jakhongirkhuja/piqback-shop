<?php

use App\Http\Controllers\Api\v1\Spa\SpaNewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1-1')->middleware(['checkStandardAttribute'])->group(function () {

    Route::prefix('spa-news')->group(function(){
        Route::get('/',[SpaNewsController::class, 'newsInfo'])->withoutMiddleware([CheckStandardAttributes::class]);
        // Route::post('/user-info-change',[SpaUserController::class, 'userInfoChange']);
    });
});

