<?php

use App\Http\Controllers\Api\v1\Course\CourseController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('course')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/category',[CourseController::class, 'categoryList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/category',[CourseController::class, 'categoryPost']);
            Route::post('/category/add',[CourseController::class, 'categoryPostAdd'])->middleware('auth:sanctum');
            Route::post('/category/edit/{category_id}',[CourseController::class, 'categoryPostEdit']);
            Route::post('/category/editNew/{category_id}',[CourseController::class, 'categoryPostEditNew'])->middleware('auth:sanctum');
            Route::post('/category/delete/{category_id}',[CourseController::class, 'categoryPostDelete']);
            Route::get('/',[CourseController::class, 'courseList'])->name('admin_course_list')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/submit',[CourseController::class, 'courseSubmit']);
            Route::post('/submit/add',[CourseController::class, 'courseSubmitAdd'])->middleware('auth:sanctum');
            Route::post('/edit/{course_id}',[CourseController::class, 'courseEdit']);
            Route::post('/editNew/{course_id}',[CourseController::class, 'courseEditNew'])->middleware('auth:sanctum');;
            Route::post('/delete/{course_id}',[CourseController::class, 'courseDelete']);
            Route::get('/getCourseStatistics/{course_id}',[CourseController::class, 'getCourseStatistics'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/getCourseStatisticsNotPassed/{course_id}',[CourseController::class, 'getCourseStatisticsNotPassed'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/logs',[CourseController::class, 'logs']);

            Route::get('/pins/{category_id}',[CourseController::class, 'pinList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/pins/{category_id}',[CourseController::class, 'pinSubmit']);
        });
       
    });
});
