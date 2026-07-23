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
    public const HOME = '/';

    public const EMPHOME = 'hrm-dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
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
        // API rate limiting: 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        // 🔒 SECURITY: Rate limiting for user management operations
        // Prevents abuse of user creation/update endpoints
        // Increased to 100/min to prevent blocking legitimate admin activity
        RateLimiter::for('user-management', function (Request $request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });

        // 🔒 SECURITY: Rate limiting for role management operations
        // Prevents abuse of role creation/update endpoints
        // Increased to 100/min to prevent blocking legitimate admin activity
        RateLimiter::for('role-management', function (Request $request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });

        // 🔒 SECURITY: Rate limiting for sensitive operations
        // General rate limiting for critical security operations
        // Increased to 200/min to allow normal user workflows
        RateLimiter::for('sensitive-operations', function (Request $request) {
            return Limit::perMinute(200)->by(optional($request->user())->id ?: $request->ip());
        });

        // 🔒 SECURITY: Rate limiting for login attempts (additional IP-based protection)
        // This works alongside the LoginRequest rate limiting for extra security
        // Increased to 30/min to allow legitimate retry attempts
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
