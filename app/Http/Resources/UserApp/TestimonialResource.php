<?php

/**
 * TestimonialResource
 *
 * Transforms a Testimonial model into a localized public API resource.
 */

declare(strict_types=1);

namespace App\Http\Resources\UserApp;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Testimonial
 */
class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $locale = app()->getLocale();
        $avatarUrl = $this->getFirstMediaUrl('testimonials');
        $absoluteAvatar = $avatarUrl !== ''
            ? (str_starts_with($avatarUrl, 'http') ? $avatarUrl : url($avatarUrl))
            : null;

        $name = (string) ($this->localized('name', $locale) ?? '');
        $role = $this->localized('name_job', $locale);
        $company = $this->localized('company_name', $locale);
        $description = (string) ($this->localized('description', $locale) ?? '');

        return [
            'id' => $this->id,
            'name' => $name,
            'client_name' => $name,
            'name_job' => $role,
            'client_role' => $role,
            'company_name' => $company,
            'description' => $description,
            'testimonial' => $description,
            'rating' => (int) $this->rating,
            'is_featured' => (bool) $this->is_featured,
            'sort_order' => (int) $this->sort_order,
            'source' => (string) ($this->source ?? 'dashboard'),
            'avatar' => $absoluteAvatar,
            'project' => $this->whenLoaded('project', function () use ($locale): ?array {
                // Never expose a link to an archived, draft or inactive project (it would 404).
                if ($this->project === null || ! $this->project->isPublished()) {
                    return null;
                }

                return [
                    'id' => $this->project->id,
                    'title' => (string) $this->project->getTranslation('title', $locale),
                    'slug' => $this->project->slug,
                ];
            }),
        ];
    }
}
