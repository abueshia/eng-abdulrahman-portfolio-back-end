<?php

/**
 * UpdateTestimonialRequest
 *
 * Validates updating an existing client testimonial.
 */

declare(strict_types=1);

namespace App\Http\Requests\Admin\Testimonial;

use App\Http\Requests\BasePortfolioFormRequest;

class UpdateTestimonialRequest extends BasePortfolioFormRequest
{
    public function authorize(): bool
    {
        return $this->authorizeAdminPermission('testimonial.edit')
            || $this->authorizeAdminPermission('testimonials.edit');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'name' => ['required', 'array'],
            'name.ar' => ['nullable', 'string', 'max:255', 'required_without:name.en'],
            'name.en' => ['nullable', 'string', 'max:255', 'required_without:name.ar'],
            'name_job' => ['nullable', 'array'],
            'name_job.ar' => ['nullable', 'string', 'max:255'],
            'name_job.en' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'array'],
            'company_name.ar' => ['nullable', 'string', 'max:255'],
            'company_name.en' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'array'],
            'description.ar' => ['nullable', 'string', 'required_without:description.en'],
            'description.en' => ['nullable', 'string', 'required_without:description.ar'],
            'meta_title' => ['nullable', 'array'],
            'meta_title.ar' => ['nullable', 'string', 'max:255'],
            'meta_title.en' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
