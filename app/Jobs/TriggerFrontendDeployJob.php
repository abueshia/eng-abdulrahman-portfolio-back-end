<?php

/**
 * TriggerFrontendDeployJob
 *
 * Calls the Vercel Deploy Hook URL to rebuild the static frontend. Failures are
 * swallowed and logged without the hook URL (the URL itself acts as a secret).
 */

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TriggerFrontendDeployJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * A failed deploy request is not retried; the next content change triggers a new one.
     */
    public int $tries = 1;

    public function __construct(
        public readonly string $hookUrl,
    ) {}

    /**
     * Send the deploy request to Vercel.
     */
    public function handle(): void
    {
        try {
            $response = Http::connectTimeout(3)->timeout(5)->post($this->hookUrl);

            if ($response->failed()) {
                Log::warning('Frontend deploy hook responded with an error.', [
                    'status' => $response->status(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Frontend deploy hook request failed.', [
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
