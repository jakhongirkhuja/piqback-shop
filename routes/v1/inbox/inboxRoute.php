<?php

use App\Http\Controllers\Api\v1\Inbox\InboxController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('inbox')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[InboxController::class, 'inboxList'])->withoutMiddleware([CheckStandardAttributes::class]);
            // Route::get('/edit/{inbox_id}',[InboxController::class, 'inboxEditGet'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/edit/{inbox_id}',[InboxController::class, 'inboxEditSubmit']);
            Route::post('/add',[InboxController::class, 'inboxSubmit']);
            Route::post('/delete/{inbox_id}',[InboxController::class, 'inboxDelete']);
        });
    });
});
