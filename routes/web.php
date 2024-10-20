<?php

use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web'])->group(function () {
    
    Route::get('/language/{locale}',[IndexController::class,'language'])->name('languageChange');
    Route::get('/',[IndexController::class,'index'])->name('index');
    Route::get('/profile',[IndexController::class,'profile'])->name('profile');
    
    
    Route::post('/profile/info',[IndexController::class,'profileInfo'])->name('profile.info');
    Route::post('/profile/phonebook',[IndexController::class,'profilephonebook'])->name('profile.phonebook');
    Route::post('/profile/profilePassword',[IndexController::class,'profilePassword'])->name('profile.profilePassword');
    Route::post('/profile/profileEmail',[IndexController::class,'profileEmail'])->name('profile.profileEmail');
    Route::post('/profile/profileCompany',[IndexController::class,'profileCompany'])->name('profile.profileCompany');
    Route::get('/profile/profileGetRegion',[IndexController::class,'profileGetRegion'])->name('profile.profileGetRegion');
    Route::get('/profile/profileCompany/MembersApprove',[IndexController::class,'profileCompanyMembersApprove'])->name('profile.profileCompanyMembersApprove');
    
    Route::get('/category/{id}',[IndexController::class,'category'])->name('category.web');
    Route::get('/course/{id}',[IndexController::class,'course'])->name('course.web');
    Route::get('/lesson/{id}',[IndexController::class,'lesson'])->name('lesson.web');
    Route::get('/lesson/{id}/quizz',[IndexController::class,'lessonQuiz'])->name('lesson.quiz.web');
    Route::post('/submit/quizz',[IndexController::class,'lessonQuizPost'])->name('lessonPost.quiz.web');
    
    Route::get('/mycourses',[IndexController::class,'mycourses'])->name('mycourses.web');
    Route::get('/savedcourse',[IndexController::class,'savedcourse'])->name('savedcourse.web');
    Route::get('/task',[IndexController::class,'task'])->name('task.web');
    Route::get('/webinars',[IndexController::class,'webinars'])->name('webinars.web');

    Route::post('/savedcourse/submit',[IndexController::class,'savedcoursePost'])->name('savedcoursePost.web');
    Route::post('/savedcourse/remove',[IndexController::class,'savedcourseRemove'])->name('savedcourseRemove.web');
    
    
});

Route::get('/profile/auth/{id}',[IndexController::class,'profileAuth'])->name('profileAuth');
Route::get('/logout',[IndexController::class,'logout'])->name('logout');
Route::get('/logs',[IndexController::class,'logs'])->name('logs.show');

Route::get('/upload/city',[IndexController::class,'uploadcity'])->name('uploadcity');
Route::get('/upload/region',[IndexController::class,'uploadregion'])->name('uploadregion');
Route::get('/upload/quarter',[IndexController::class,'uploadquarter'])->name('uploadquarter');

Route::get('/read-logs', [IndexController::class, 'readLog']);

Route::get('/google-sheets', [GoogleSheetsController::class, 'index']);
Route::post('/google-sheets', [GoogleSheetsController::class, 'update']);
