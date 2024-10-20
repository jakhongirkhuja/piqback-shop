<?php

use App\Http\Controllers\Api\v1\Groups\GroupController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('groups')->group(function(){
        //middleware = auth:sanctum;
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[GroupController::class, 'grouplist'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/companies/{group_id}',[GroupController::class, 'groupCompanylist'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/members/{group_id}',[GroupController::class, 'groupMemberslist'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/',[GroupController::class, 'groupPostName']);
            
            
            //add/delete/ group members --- delete not implemented yet
            Route::post('/addGroupMembers',[GroupController::class, 'addGroupMembers']);
            Route::post('/deleteGroupMembers/{member_id}',[GroupController::class, 'deleteGroupMembers']);
            
            //add/delete group company
            Route::post('/addGroupCompanies',[GroupController::class, 'addGroupCompanies']);
            Route::post('/deleteGroupCompany/{company_id}',[GroupController::class, 'deleteGroupCompany']);

            //edit/active/deactive group name
            Route::post('/groupNameEdit/{id}',[GroupController::class, 'groupNameEdit']);
            Route::post('/groupstatus/{id}',[GroupController::class, 'groupstatus']);
            Route::post('/groupDelete/{id}',[GroupController::class, 'groupDelete']);

            // add/delete Company Restrictions
            Route::get('/companyRestriction/{group_id}',[GroupController::class, 'listCompanyRestriction'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/companyRestriction/add',[GroupController::class, 'addCompanyRestriction']);
            Route::post('/companyRestriction/delete/{company_id}',[GroupController::class, 'deleteCompanyRestriction']);

            // add /delete Member Restrictions
            Route::get('/memberRestriction/{group_id}',[GroupController::class, 'listMemberRestriction'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/memberRestriction/add',[GroupController::class, 'addMemberRestriction']);
            
            Route::post('/memberRestriction/delete/{member_id}',[GroupController::class, 'deleteMemberRestriction']);

        });
       
    });
});
