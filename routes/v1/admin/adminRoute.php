<?php

use App\Helper\StandardAttributes;
use App\Http\Controllers\Api\v1\Admin\AdminController;
use App\Http\Controllers\Api\v1\Company\CompanyController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function(){
        Route::get('/statistics',[AdminController::class, 'statistics'])->middleware('auth:sanctum');
        Route::get('/show/users',[AdminController::class, 'showUsers'])->middleware('auth:sanctum');
        Route::get('/show/groups',[AdminController::class, 'showGroups'])->middleware('auth:sanctum');
        Route::get('/statistics/finance',[AdminController::class, 'statisticsFinance'])->middleware('auth:sanctum');
        Route::post('/statistics/finance/date',[AdminController::class, 'statisticsFinanceDate'])->middleware('auth:sanctum');
        Route::get('/statistics/removed-iqc-users',[AdminController::class, 'removedIqcUsers'])->middleware('auth:sanctum');
        
        Route::get('/show/users/{id}',[AdminController::class, 'showUsersId']);
        Route::post('/check-email',[AdminController::class, 'checkEmail']);
        
        Route::post('/iqlabs-users',[AdminController::class, 'iqlabsUsers']);
        Route::middleware(['checkStandardAttribute','auth:sanctum'])->group(function(){
            Route::post('/remove-iqc-users',[AdminController::class, 'removeIqcUsers']);
            Route::post('/remove-iqc-users-group',[AdminController::class, 'removeIqcUsersGroup']);
            Route::post('/add/user',[AdminController::class, 'addUser']);
            Route::post('/update/users',[AdminController::class, 'updateUser']);
            Route::post('/delete/users',[AdminController::class, 'deleteUser']);
            Route::post('/updateUserRole',[AdminController::class, 'updateUserRole']);

            Route::post('/updateUserCompany',[AdminController::class, 'updateUserCompany']);
            Route::post('/mergeUserCompany',[AdminController::class, 'mergeUserCompany']);
            Route::post('/export/users',[AdminController::class, 'userExport']); 
            Route::post('/export/usersAndIqc',[AdminController::class, 'usersAndIqc']); 
            Route::post('/numbers/getStatuses',[AdminController::class, 'getStatuses']);
            Route::post('/numbers/getStatusesMailing',[AdminController::class, 'getStatusesMailing']);
        });
        // Route::get('/team/show/{company_id}',[CompanyController::class, 'companyTeams']);
        // Route::get('/team/users/list',[CompanyController::class, 'companyTeamsUserList']); //getting list of members by....
        
        // Route::middleware(['checkStandardAttribute'])->group(function(){
        //     Route::post('/team/create',[CompanyController::class, 'companyTeamCreate']); // creating Company Team
        //     Route::post('/team/users/add',[CompanyController::class, 'companyTeamUserAdd']); // adding users to a Created team
        // });
    });
});

