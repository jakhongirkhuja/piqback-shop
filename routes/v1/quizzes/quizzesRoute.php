<?php

use App\Http\Controllers\Api\v1\Certificate\CertificateController;
use App\Http\Controllers\Api\v1\Quizzes\QuestionController;
use App\Http\Controllers\Api\v1\Quizzes\QuestionVariantController;
use App\Http\Controllers\Api\v1\Quizzes\QuizController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('quizz')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[QuizController::class, 'quizList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/add',[QuizController::class, 'quizAdd'])->middleware('auth:sanctum');;
            Route::post('/edit',[QuizController::class, 'quizedit'])->middleware('auth:sanctum');;
            Route::post('/delete',[QuizController::class, 'quizDelete'])->middleware('auth:sanctum');;
            Route::post('/logs',[QuizController::class, 'logs']);
            
            Route::prefix('questions')->group(function(){
                Route::post('/add',[QuestionController::class, 'questionadd'])->middleware('auth:sanctum');;
                Route::post('/edit',[QuestionController::class, 'questionedit'])->middleware('auth:sanctum');;
                Route::post('/delete',[QuestionController::class, 'questionDelete'])->middleware('auth:sanctum');;
            });
            Route::prefix('variants')->group(function(){
                Route::post('/add',[QuestionVariantController::class, 'variantadd'])->middleware('auth:sanctum');;
                Route::post('/edit',[QuestionVariantController::class, 'variantedit'])->middleware('auth:sanctum');;
                Route::post('/delete',[QuestionVariantController::class, 'variantDelete'])->middleware('auth:sanctum');;
            });

            Route::post('/certificate',[CertificateController::class, 'certificateGet'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/certificate/post',[CertificateController::class, 'certificatePost']);
            Route::get('/dash',[QuizController::class, 'quizDash'])->withoutMiddleware([CheckStandardAttributes::class]);
        });
       
    });
});
