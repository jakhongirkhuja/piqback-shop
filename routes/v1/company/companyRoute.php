<?php

use App\Http\Controllers\Api\v1\Company\CompanyController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(['log.route'])->group(function () {
    Route::prefix('company')->group(function(){
        Route::get('/search',[CompanyController::class, 'showCompanySearch']);
        Route::get('/show/all',[CompanyController::class, 'showAllCompanies'])->middleware('auth:sanctum');
        Route::get('/getCompanyId/{company_id}',[CompanyController::class, 'companyGetById']);
        Route::get('/team/show/{company_id}',[CompanyController::class, 'companyTeams']);
        Route::get('/team/users/list/{team_id}',[CompanyController::class, 'companyTeamsUserList']); //getting list of members by....
        
        
        Route::middleware(['checkStandardAttribute','auth:sanctum'])->group(function(){
            Route::post('/update',[CompanyController::class, 'companyUpdate']); // creating Company Team
            Route::post('/delete',[CompanyController::class, 'companyDelete']); // creating Company Team
            Route::post('/update/owner',[CompanyController::class, 'companyUpdateOwner']); // creating Company Team
            
            Route::post('/team/create',[CompanyController::class, 'companyTeamCreate']); // creating Company Team
            Route::post('/team/update',[CompanyController::class, 'companyTeamUpdate']); // update Company Team name
            Route::post('/team/delete',[CompanyController::class, 'companyTeamDelete']); // remove Company Team
            
            Route::post('/team/users/add',[CompanyController::class, 'companyTeamUserAdd']); // adding users to a Created team
            Route::post('/team/users/delete',[CompanyController::class, 'companyTeamUserDelete']); // remove users from  team
            
            Route::post('/export',[CompanyController::class, 'companyExport']); 
            Route::post('/memberStatusUpdate',[CompanyController::class, 'companyMemberStatusUpdate']);
        });
    });
});

