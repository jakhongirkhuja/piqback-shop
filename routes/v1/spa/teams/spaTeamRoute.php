<?php

use App\Http\Controllers\Api\v1\Company\CompanyController;
use App\Http\Controllers\Api\v1\Spa\SpaTeamController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1')->middleware(['log.route'])->group(function () {
    Route::prefix('spa-teams')->group(function(){
        
        
        
        Route::middleware(['checkStandardAttribute','auth:sanctum'])->group(function(){
            Route::get('/team/show',[SpaTeamController::class, 'companyTeams'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/team/users/list/{team_id}',[SpaTeamController::class, 'companyTeamsUserList'])->withoutMiddleware([CheckStandardAttributes::class]); //getting list of members by....
            Route::get('/team/users/notAlocated',[SpaTeamController::class, 'companyTeamsUserNotAlocated'])->withoutMiddleware([CheckStandardAttributes::class]); 
            Route::post('/team/create',[SpaTeamController::class, 'companyTeamCreate']); // creating Company Team
            Route::post('/team/update',[SpaTeamController::class, 'companyTeamUpdate']); // update Company Team name
            Route::post('/team/delete',[SpaTeamController::class, 'companyTeamDelete']); // remove Company Team
            
            Route::post('/team/users/add',[SpaTeamController::class, 'companyTeamUserAdd']); // adding users to a Created team
            Route::post('/team/users/delete',[SpaTeamController::class, 'companyTeamUserDelete']); // remove users from  team
          
        });
    });
});

