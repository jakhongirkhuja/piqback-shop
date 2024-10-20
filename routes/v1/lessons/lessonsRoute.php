<?php
use App\Http\Controllers\Api\v1\Lesson\LessonController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('lessons')->group(function(){
       
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[LessonController::class, 'lessonList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/add',[LessonController::class, 'lessonAdd']);
            Route::post('/edit',[LessonController::class, 'lessonEdit']);
            Route::post('/delete',[LessonController::class, 'lessonDelete']);
            Route::post('/content/add',[LessonController::class, 'lessonContentAdd']);
            Route::post('/content/image/add',[LessonController::class, 'lessonContentImageAdd']);
            Route::post('/content/image/edit',[LessonController::class, 'lessonContentImageEdit']);

            Route::post('/content/edit',[LessonController::class, 'lessonContentEdit']);
            Route::post('/content/delete',[LessonController::class, 'lessonContentDelete']);
            Route::post('/logs',[LessonController::class, 'logs']);


            Route::post('/extraMaterial/add',[LessonController::class, 'lessonExtraMaterialAdd']);
            Route::post('/extraMaterial/edit',[LessonController::class, 'lessonExtraMaterialEdit']);
            Route::post('/extraMaterial/delete/{extra_id}',[LessonController::class, 'lessonExtraMaterialDelete']);
        });
       
    });
});
