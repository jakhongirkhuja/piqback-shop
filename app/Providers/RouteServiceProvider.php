<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/auth/authRoute.php'));    
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/company/companyRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/groups/groupRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/course/courseRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/lessons/lessonsRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/quizzes/quizzesRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/admin/adminRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/wish/wishesRoute.php'));             
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/auth/auth2Route.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/inbox/inboxRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/promocode/promocodeRoute.php'));  
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/targetfilter/targetfilterRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/store/storeRoute.php'));   
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/bot/botRoute.php'));  
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/academy/userRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/auth/authMobileRoute.php'));    

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/news/newsRoute.php'));   
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/spa/courses/spaCourseRoute.php')); 
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/spa/news/spaNewsRoute.php')); 
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/spa/user/spaUserRoute.php'));  
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/spa/inboxMessages/spaInboxMessagesRoute.php'));    
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/spa/teams/spaTeamRoute.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/v1/lottery/lotteriesRoute.php'));                              
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
