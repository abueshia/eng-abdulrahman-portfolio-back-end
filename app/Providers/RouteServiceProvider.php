<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/admin';

    /**
     * Maximum public API read requests (GET/HEAD) per minute per IP address.
     */
    public const PUBLIC_API_READS_PER_MINUTE = 120;

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Light global limit for public read endpoints. Write endpoints (POST) keep
        // their own stricter route-level throttles, so they are not counted here.
        RateLimiter::for('api', static function (Request $request): Limit {
            if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
                return Limit::none();
            }

            return Limit::perMinute(self::PUBLIC_API_READS_PER_MINUTE)->by((string) $request->ip());
        });

        $this->routes(function (): void {
            // Both middleware must be passed in one call: a second `middleware()`
            // call on the registrar replaces the first one instead of merging.
            Route::middleware(['api', 'ApiSetting'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'AdminCheck'])
//                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
        });
    }
}
