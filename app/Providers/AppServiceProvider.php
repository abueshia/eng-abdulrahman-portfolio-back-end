<?php

/**
 * AppServiceProvider
 *
 * Registers application-level services and Eloquent model observers.
 */

declare(strict_types=1);

namespace App\Providers;

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Observers\FrontendDeployObserver;
use App\Observers\SettingObserver;
use App\Observers\TestimonialDeployObserver;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Setting::observe(SettingObserver::class);

        // Rebuild the static frontend (Vercel Deploy Hook) when public content changes.
        Project::observe(FrontendDeployObserver::class);
        Experience::observe(FrontendDeployObserver::class);
        Faq::observe(FrontendDeployObserver::class);
        Testimonial::observe(TestimonialDeployObserver::class);

        View::composer(['admin.layouts.includes.aside', 'admin.layouts.aside'], static function (ViewContract $view): void {
            $unreadCount = 0;

            try {
                if (auth()->guard('admin')->check()) {
                    $unreadCount = app(ContactMessageRepositoryInterface::class)->countUnread();
                }
            } catch (Throwable) {
                $unreadCount = 0;
            }

            $view->with('sidebarUnreadMessagesCount', $unreadCount);
        });
    }
}
