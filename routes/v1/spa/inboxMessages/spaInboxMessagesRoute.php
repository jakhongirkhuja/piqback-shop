<?php

use App\Http\Controllers\Api\v1\Spa\SpaCourseController;
use App\Http\Controllers\Api\v1\Spa\SpaInboxMessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1-1')->middleware(['auth:sanctum','checkStandardAttribute'])->group(function () {

    
    Route::prefix('spa-inboxMessage')->group(function(){
        Route::controller(SpaInboxMessageController::class)->group(function () {
            Route::get('/', 'getInboxMessage')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/postPromocodeInbox', 'postPromocodeInbox');
            Route::post('/postInboxMessageLog', 'postInboxMessageLog');
        });
    });
});
