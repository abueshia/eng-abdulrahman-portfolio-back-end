<?php

/**
 * FrontendDeployHook
 *
 * Requests a rebuild of the static React frontend through the configured Vercel
 * Deploy Hook. Requests are debounced (at most one per configured window) and the
 * trigger never throws: a missing URL or a failure is silently ignored and logged.
 */

declare(strict_types=1);

namespace App\Services\Support;

use App\Jobs\TriggerFrontendDeployJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class FrontendDeployHook
{
    /**
     * Cache key holding the debounce lock.
     */
    public const LOCK_KEY = 'portfolio.frontend_deploy_hook.lock';

    /**
     * Request a frontend rebuild unless one was already requested within the debounce window.
     */
    public function request(): void
    {
        $url = trim((string) config('services.vercel.deploy_hook_url', ''));
        if ($url === '') {
            return;
        }

        try {
            $window = max(1, (int) config('services.vercel.deploy_hook_debounce_seconds', 60));

            // Cache::add() only succeeds when the key does not exist yet: one trigger per window.
            if (! Cache::add(self::LOCK_KEY, true, $window)) {
                return;
            }

            TriggerFrontendDeployJob::dispatch($url);
        } catch (Throwable $exception) {
            Log::warning('Frontend deploy hook could not be requested.', [
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
