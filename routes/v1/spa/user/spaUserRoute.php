<?php


use App\Http\Controllers\Api\v1\Spa\SpaUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckStandardAttributes;

Route::prefix('v1-1')->middleware(['auth:sanctum','checkStandardAttribute'])->group(function () {

    Route::prefix('spa-users')->group(function(){
        Route::get('/users-top-iqc-statistics',[SpaUserController::class, 'userTopIqcStat'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::get('/user-info',[SpaUserController::class, 'userInfo'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::get('/user-transactions',[SpaUserController::class, 'userTransactions'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::post('/user-info-change',[SpaUserController::class, 'userInfoChange']);

        Route::post('/user-profile-delete',[SpaUserController::class, 'deleteUserProfile']);
        Route::post('/user-phoneNumber-change',[SpaUserController::class, 'UserPhoneNumberChange']);
        Route::post('/user-password-change',[SpaUserController::class, 'UserPasswordChange']);
        Route::post('/user-email-change',[SpaUserController::class, 'profileEmail']);
        Route::post('/user-profileCompany-change',[SpaUserController::class, 'UserprofileCompany']);
        Route::post('/user-companyMember-approve',[SpaUserController::class, 'UserprofileCompanyMembersApprove']);
        
        Route::get('/user-company-members-list',[SpaUserController::class, 'userCompanyMembersList'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::post('/user-company-members-change',[SpaUserController::class, 'userCompanyMemberChange']);
        Route::post('/user-company-change',[SpaUserController::class, 'userCompanyChange']);
        Route::post('/user-buy-course',[SpaUserController::class, 'buyCourseIqc']);
        Route::get('/user-bought-course-statistics',[SpaUserController::class, 'boughtCourseStatistics'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::post('/user-promocode',[SpaUserController::class, 'promoActivate']);
        Route::post('/user-add-iqc',[SpaUserController::class, 'userAddIqc']);
        Route::get('/user-qrcode-appear',[SpaUserController::class, 'userQrcodeAppearCheck'])->withoutMiddleware([CheckStandardAttributes::class]);
        Route::get('/user-course-count',[SpaUserController::class, 'userCourseCount'])->withoutMiddleware([CheckStandardAttributes::class]);

        Route::get('/user-lotteries',[SpaUserController::class, 'userlotteries'])->withoutMiddleware([CheckStandardAttributes::class]);;
        Route::post('/user-phoneNumber-update',[SpaUserController::class, 'sendSms']);
        Route::post('/user-phoneNumber-code-confirm',[SpaUserController::class, 'confirmUpdateNumber']);
    });
});

