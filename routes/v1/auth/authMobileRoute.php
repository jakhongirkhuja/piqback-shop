<?php

use App\Http\Controllers\Api\v1\Auth\Auth2Controller;
use App\Http\Controllers\Api\v1\Auth\MobileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1-1')->middleware(['log.response','checkStandardAttribute'])->group(function () {

    
    Route::get('/mobile-user-check',[Auth2Controller::class, 'mobileUserCheck'])->withoutMiddleware([CheckStandardAttributes::class]);
    
    Route::post('/mobile-login',[Auth2Controller::class, 'mobileLogin']);
    Route::post('/mobile-send-sms',[MobileController::class, 'mobileSendSmsLogin']);
    Route::post('/mobile-send-confirm',[MobileController::class, 'mobileSendSmsLoginConfirm']);
    Route::get('/mobile-get-number',[MobileController::class, 'mobileGetNumber'])->withoutMiddleware([CheckStandardAttributes::class]);
    Route::post('/mobile-put-number',[MobileController::class, 'mobilePutNumber']);
    Route::post('/mobile-put-number-confirm',[MobileController::class, 'mobilePutNumberConfirm']);
    Route::post('/mobile-register',[MobileController::class, 'mobileRegister']);


   
});

Route::prefix('v1-1')->middleware(['checkStandardAttribute'])->group(function () {
    
    Route::get('/mobile-version',[MobileController::class, 'mobileVersion'])->withoutMiddleware([CheckStandardAttributes::class]);
    

});

Route::prefix('v1-1')->middleware(['auth:sanctum','checkStandardAttribute'])->group(function () {
    Route::get('/mobile-user',[MobileController::class, 'mobileUserCheck'])->withoutMiddleware([CheckStandardAttributes::class]);
   
    Route::post('/mobile-version',[MobileController::class, 'mobileVersionPost']);
    Route::post('/mobile-version-delete/{id}',[MobileController::class, 'mobileVersionDelete']);

});