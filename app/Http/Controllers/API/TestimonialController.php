<?php

/**
 * API TestimonialController
 *
 * REST API controller for public client testimonials and review submissions.
 */

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonial\StorePublicTestimonialRequest;
use App\Http\Resources\UserApp\TestimonialResource;
use App\Services\TestimonialService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly TestimonialService $testimonialService,
    ) {}

    /**
     * List active client testimonials.
     */
    public function index(): JsonResponse
    {
        $testimonials = $this->testimonialService->getActive();

        return $this->success_response(
            TestimonialResource::collection($testimonials),
            'Testimonials retrieved successfully'
        );
    }

    /**
     * Store a new client review/testimonial submitted from the portfolio website.
     * Enforces is_active = false so reviews must be approved from the dashboard.
     */
    public function store(StorePublicTestimonialRequest $request): JsonResponse
    {
        $image = $request->file('image') ?? $request->file('avatar');

        $data = $request->toTestimonialData();
        // Server-side enforcement of review status
        $data['is_active'] = false;
        $data['is_featured'] = false;
        $data['source'] = 'website';

        $testimonial = $this->testimonialService->create(
            $data,
            $image
        );

        return $this->success_response(
            new TestimonialResource($testimonial),
            'Thank you! Your review has been submitted and is pending approval.',
            201
        );
    }
}
