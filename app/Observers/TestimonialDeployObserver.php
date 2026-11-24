<?php

/**
 * TestimonialDeployObserver
 *
 * Requests a frontend rebuild only when a change affects publicly visible
 * testimonials: saving an approved (active) testimonial, approving or
 * un-approving one, or deleting an approved one. Pending website submissions
 * never trigger a rebuild.
 */

declare(strict_types=1);

namespace App\Observers;

use App\Models\Testimonial;
use App\Services\Support\FrontendDeployHook;

class TestimonialDeployObserver
{
    public function __construct(
        private readonly FrontendDeployHook $deployHook,
    ) {}

    /**
     * Handle the Testimonial "saved" event (covers both created and updated).
     */
    public function saved(Testimonial $testimonial): void
    {
        if ((bool) $testimonial->is_active || $testimonial->wasChanged('is_active')) {
            $this->deployHook->request();
        }
    }

    /**
     * Handle the Testimonial "deleted" event.
     */
    public function deleted(Testimonial $testimonial): void
    {
        if ((bool) $testimonial->is_active) {
            $this->deployHook->request();
        }
    }
}
