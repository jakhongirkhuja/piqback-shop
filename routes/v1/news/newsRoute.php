<?php
use App\Http\Controllers\Api\v1\Lesson\LessonController;
use App\Http\Controllers\Api\v1\News\NewsController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('news')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[NewsController::class, 'newslist'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/add',[NewsController::class, 'newsAdd']);
            Route::post('/edit/{id}',[NewsController::class, 'newsEdit']);
            Route::post('/delete/{id}',[NewsController::class, 'newsDelete']);
        });
       
    });
});
