<?php

use App\Http\Controllers\Api\v1\Bot\BotController;
use App\Http\Controllers\Api\v1\Bot\BotTeamController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    

    Route::prefix('bot')->group(function(){

        Route::middleware(['botToken','checkStandardAttribute'])->group(function () {
            Route::post('/register',[BotController::class, 'botRegister']);
            Route::post('/checkHash',[BotController::class, 'checkHash']);

            Route::post('/submitReport',[BotController::class, 'submitReport'])->withoutMiddleware([CheckStandardAttributes::class]);
        });
        
        Route::prefix('myteam')->group(function(){
            Route::middleware(['botToken','checkstoreowner','checkStandardAttribute'])->group(function () {
                Route::get('/',[BotTeamController::class, 'showTeamList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/delete/{team_id}',[BotTeamController::class, 'teamDelete']);
                Route::post('/add',[BotTeamController::class, 'teamAddSubmit']);
                
            });
        });

       
    });

    
});
