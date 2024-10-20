<?php

use App\Http\Controllers\Api\v1\Auth\Auth2Controller;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Auth\AuthControllerGlobal;
use App\Http\Controllers\Api\v1\Auth\MobileController;
use App\Http\Controllers\Api\v1\IndexController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;


Route::prefix('v1-1')->middleware(['log.response','checkStandardAttribute'])->group(function () {

    
    // Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[Auth2Controller::class,'login']);

    Route::post('/reset/numberConfirm',[Auth2Controller::class,'resetPassword']);
    Route::post('/reset/codeConfirm',[Auth2Controller::class,'codeConfirm']);
    Route::post('/reset/passwordConfirm',[Auth2Controller::class,'newPasswordConfirm']);

    Route::post('/check-number',[Auth2Controller::class, 'checkNumber']);
    Route::get('/get-number',[Auth2Controller::class, 'getNumber'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-number',[Auth2Controller::class, 'putNumber']);
    
    
    Route::get('/get-info',[Auth2Controller::class, 'getInfo'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-info',[Auth2Controller::class, 'putInfo']);


    Route::get('/get-role',[Auth2Controller::class, 'getRole'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-role',[Auth2Controller::class, 'putRole']);

    Route::get('/get-companyName',[Auth2Controller::class, 'getcompanyName'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-companyName',[Auth2Controller::class, 'putcompanyName']);


    Route::get('/get-companyAddress',[Auth2Controller::class, 'getcompanyAddress'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-companyAddress',[Auth2Controller::class, 'putcompanyAddress']);


    Route::post('/put-password',[Auth2Controller::class, 'putPassword']);
    

    Route::get('/get-email',[Auth2Controller::class, 'getEmail'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/put-email',[Auth2Controller::class, 'putEmail']);

    Route::post('/send-sms',[Auth2Controller::class, 'sendSms']);
    Route::post('/confirm-code',[Auth2Controller::class, 'confirmCode']);


    Route::post('/loginOnly',[Auth2Controller::class, 'loginOnly']);
    
    Route::post('/sendSmsOnlyLogin',[Auth2Controller::class, 'sendSmsOnlyLogin']);
    

    Route::post('/sendSmsOnly',[Auth2Controller::class, 'sendSmsOnly']);
    Route::post('/confirmCodeOnly',[Auth2Controller::class, 'confirmCodeOnly']);
    Route::post('/newRegisterOnly',[Auth2Controller::class, 'newRegisterOnly']);


    Route::middleware(['auth:sanctum','checkStandardAttribute'])->group(function () {
        // Route::get('/user',[IndexController::class,'index'])->name('users');
        Route::post('/logout',[Auth2Controller::class,'logout']);
    });

    // registering and checking
    // Route::post('/check-email',[AuthController::class, 'checkEmail']);
    
    // Route::post('/check-number',[AuthController::class, 'checkNumber']);
    // Route::middleware(['checkStandardAttribute'])->group(function(){
    //     Route::post('/register-user',[AuthController::class,'registerUser']);
        
    //     Route::post('/submit/company-name',[AuthController::class, 'companyName']);
    //     Route::post('/submit/company-address',[AuthController::class, 'companyAddress']);
        
    // });
    // Route::get('/getAdressByCoor',[AuthController::class,'getAdressByCoor']);
    // Route::post('/roles',[AuthController::class, 'roles']);
    // Route::get('/show/cities',[AuthController::class, 'showCities']);
    // Route::get('/show/regions/{city_id}',[AuthController::class, 'showRegions']);
    // Route::get('/show/quarter/{region_id}',[AuthController::class, 'showQuarters']);
    // Route::post('/new-password-request',[AuthController::class, 'setPasswordRequest']);
    // Route::post('/set-password',[AuthController::class, 'setPassword']);
    
    // Route::middleware(['checkStandardAttribute'])->group(function(){
    //     Route::post('/set-email',[AuthController::class, 'setEmail']);
    //     Route::post('/set-company-member',[AuthController::class, 'setCompanyMember']);
    // });
    
    // send sms
    
    // Route::post('/confirm-number-by-number',[AuthController::class, 'confirmNumberByNumber']);
    // Route::post('/confirm-number',[AuthController::class, 'confirmNumber']);
    // Route::post('/confirm-response',[AuthController::class, 'confirmResponse']);


    // Route::middleware(['checkStandardAttribute'])->group(function(){
    //     Route::post('/register-global',[AuthControllerGlobal::class,'registerUserGlobal']);
    //     Route::post('/update-user-global',[AuthControllerGlobal::class,'updateUserGlobal']);

        
    // });

    
});

