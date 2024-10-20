<?php

use App\Http\Controllers\Api\v1\IndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('web')->group(function(){
        Route::prefix('category')->group(function(){
            Route::get('/list',[IndexController::class,'categoryList']);
            Route::get('/courses/all/{category_id?}',[IndexController::class,'courseList']);
            Route::get('/courses/each/{course_id}',[IndexController::class,'courseEach']);
        });
        Route::get('/course/info/{course_id}',[IndexController::class,'getCourseInfo']);
        Route::post('/form',[IndexController::class,'formPost']);
        Route::post('/form/detailed',[IndexController::class,'formDetailed']);
        Route::get('/storeProductsShow',[IndexController::class,'storeProducts']);
        
    });

    Route::post('/phoneNumberStatus',[IndexController::class,'phoneNumberStatus']);
    
    Route::post('/scouts',[IndexController::class,'scout']);
    Route::post('/relUser',[IndexController::class,'reluser']);
});
