<?php

use App\Http\Controllers\Api\v1\Spa\SpaCourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1-1')->middleware(['auth:sanctum','checkStandardAttribute'])->group(function () {

    
    Route::prefix('spa-courses')->group(function(){
        Route::controller(SpaCourseController::class)->group(function () {
            Route::get('/', 'courseInfo')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/getCategories', 'getCategories')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/getLessons', 'getLessons')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/getLesson', 'getLesson')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/checkQuizTry', 'checkQuizTry')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/mycourses', 'myCourses')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/mycoursesPassed', 'myPassedCourses')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/courses', 'courses')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/courseSearch', 'courseSearch')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/saveLessonLog', 'saveLessonLog');
            Route::post('/saveCourseLog', 'saveCourseLog');
            Route::post('/lessonQuizPost', 'lessonQuizPost');
            Route::get('/lessonQuizAccessCheck', 'lessonQuizAccessCheck')->withoutMiddleware([CheckStandardAttributes::class]);

            Route::get('/getCourseStatistics/{course_id}', 'getCourseStatistics')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/getCourseStatisticsNotPassed/{course_id}', 'getCourseStatisticsNotPassed')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/sendNotification', 'sendNotification');


            Route::get('/getCategories/{type}', 'getCategoriesByType')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/coursesNew', 'coursesByCategory')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/mycoursesNew', 'myCoursesNew')->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/mycoursesPassedNew', 'myPassedCoursesNew')->withoutMiddleware([CheckStandardAttributes::class]);
        });
    });
});
