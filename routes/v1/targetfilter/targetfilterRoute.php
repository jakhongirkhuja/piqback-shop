<?php

use App\Http\Controllers\Api\v1\TargetFilter\TargetFilterController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('targetFilter')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[TargetFilterController::class, 'targetList'])->withoutMiddleware([CheckStandardAttributes::class]);
            // Route::get('/edit/{phonebook_id}',[phonebookController::class, 'phonebookEditGet'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/edit/{target_id}',[TargetFilterController::class, 'targetEditSubmit']);
            Route::post('/add',[TargetFilterController::class, 'targetSubmit']);
            Route::post('/delete/{target_id}',[TargetFilterController::class, 'targetDelete']);
        });
    });
});
