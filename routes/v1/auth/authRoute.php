<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Auth\AuthControllerGlobal;
use App\Http\Controllers\Api\v1\IndexController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(['log.route'])->group(function () {

    
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user',[IndexController::class,'index'])->name('users');
        Route::post('/logout',[AuthController::class,'logout']);
    });

    // registering and checking
    Route::post('/check-email',[AuthController::class, 'checkEmail']);
    
    Route::post('/check-number',[AuthController::class, 'checkNumber']);
    Route::middleware(['checkStandardAttribute'])->group(function(){
        Route::post('/register-user',[AuthController::class,'registerUser']);
        
        Route::post('/submit/company-name',[AuthController::class, 'companyName']);
        Route::post('/submit/company-address',[AuthController::class, 'companyAddress']);
        
    });
    Route::get('/getAdressByCoor',[AuthController::class,'getAdressByCoor']);
    Route::post('/roles',[AuthController::class, 'roles']);
    Route::get('/show/cities',[AuthController::class, 'showCities']);
    Route::get('/show/regions/{city_id}',[AuthController::class, 'showRegions']);
    Route::get('/show/quarter/{region_id}',[AuthController::class, 'showQuarters']);
    Route::post('/new-password-request',[AuthController::class, 'setPasswordRequest']);
    Route::post('/set-password',[AuthController::class, 'setPassword']);
    
    Route::middleware(['checkStandardAttribute'])->group(function(){
        Route::post('/set-email',[AuthController::class, 'setEmail']);
        Route::post('/set-company-member',[AuthController::class, 'setCompanyMember']);
    });
    
    // send sms
    
    Route::post('/confirm-number-by-number',[AuthController::class, 'confirmNumberByNumber']);
    Route::post('/confirm-number',[AuthController::class, 'confirmNumber']);
    Route::post('/confirm-response',[AuthController::class, 'confirmResponse']);


    Route::middleware(['checkStandardAttribute'])->group(function(){
        Route::post('/register-global',[AuthControllerGlobal::class,'registerUserGlobal']);
        Route::post('/update-user-global',[AuthControllerGlobal::class,'updateUserGlobal']);

        
    });
});

