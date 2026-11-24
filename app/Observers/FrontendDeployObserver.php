<?php

/**
 * FrontendDeployObserver
 *
 * Requests a frontend rebuild whenever public portfolio content (projects,
 * experiences, FAQs) is saved or deleted.
 */

declare(strict_types=1);

namespace App\Observers;

use App\Services\Support\FrontendDeployHook;
use Illuminate\Database\Eloquent\Model;

class FrontendDeployObserver
{
    public function __construct(
        private readonly FrontendDeployHook $deployHook,
    ) {}

    /**
     * Handle the "saved" event (covers both created and updated).
     */
    public function saved(Model $model): void
    {
        $this->deployHook->request();
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->deployHook->request();
    }
}
