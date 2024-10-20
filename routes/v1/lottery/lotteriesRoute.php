<?php

use App\Http\Controllers\Api\v1\Lottery\LotteryController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('lottery')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[LotteryController::class, 'lotteryList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/add',[LotteryController::class, 'lotteryAdd']);
            Route::post('/edit',[LotteryController::class, 'lotteryEdit']);
            Route::post('/delete',[LotteryController::class, 'lotteryDelete']);
            Route::post('/export',[LotteryController::class, 'lotteryExportLogs']);
            Route::post('/updateStatus',[LotteryController::class, 'updateStatus']);
        });
       
    });
});
