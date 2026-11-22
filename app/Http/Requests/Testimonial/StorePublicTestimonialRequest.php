<?php

/**
 * StorePublicTestimonialRequest
 *
 * Validates public client review/testimonial submissions from the portfolio
 * website or REST API. Supports optional locale selection, single-language and
 * multilingual inputs, frontend alias keys, Honeypot bot protection, and optional avatar uploads.
 */

declare(strict_types=1);

namespace App\Http\Requests\Testimonial;

use App\Http\Requests\BasePortfolioFormRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class StorePublicTestimonialRequest extends BasePortfolioFormRequest
{
    /**
     * Public submission does not require dashboard authorization.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs by normalizing common frontend alias keys and locale before validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Client name aliases
        if (! $this->has('name') && $this->filled('client_name')) {
            $merge['name'] = $this->input('client_name');
        }

        // Role / Job title aliases
        if (! $this->has('name_job')) {
            if ($this->filled('client_role')) {
                $merge['name_job'] = $this->input('client_role');
            } elseif ($this->filled('job_title')) {
                $merge['name_job'] = $this->input('job_title');
            } elseif ($this->filled('role')) {
                $merge['name_job'] = $this->input('role');
            }
        }

        // Company name aliases
        if (! $this->has('company_name') && $this->filled('company')) {
            $merge['company_name'] = $this->input('company');
        }

        // Testimonial / Description comment aliases
        if (! $this->has('description')) {
            if ($this->filled('testimonial')) {
                $merge['description'] = $this->input('testimonial');
            } elseif ($this->filled('comment')) {
                $merge['description'] = $this->input('comment');
            } elseif ($this->filled('message')) {
                $merge['description'] = $this->input('message');
            } elseif ($this->filled('content')) {
                $merge['description'] = $this->input('content');
            }
        }

        // Default rating to 5 if not provided
        if (! $this->has('rating') || $this->input('rating') === null || $this->input('rating') === '') {
            $merge['rating'] = 5;
        }

        // Normalize avatar file input to image
        if (! $this->hasFile('image') && $this->hasFile('avatar')) {
            $this->files->set('image', $this->file('avatar'));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Determine the submission locale (explicit input, request header, or app locale).
     */
    public function targetLocale(): string
    {
        $inputLocale = (string) $this->input('locale');
        if (in_array($inputLocale, ['ar', 'en'], true)) {
            return $inputLocale;
        }

        $appLocale = app()->getLocale();

        return in_array($appLocale, ['ar', 'en'], true) ? $appLocale : 'ar';
    }

    /**
     * Validation rules for public client review submission.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'locale' => ['nullable', 'string', 'in:ar,en'],
            'name' => ['required'],
            'name.ar' => ['nullable', 'string', 'min:2', 'max:80'],
            'name.en' => ['nullable', 'string', 'min:2', 'max:80'],
            'name_job' => ['nullable'],
            'name_job.ar' => ['nullable', 'string', 'max:100'],
            'name_job.en' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable'],
            'company_name.ar' => ['nullable', 'string', 'max:100'],
            'company_name.en' => ['nullable', 'string', 'max:100'],
            'description' => ['required'],
            'description.ar' => ['nullable', 'string', 'min:10', 'max:1000'],
            'description.en' => ['nullable', 'string', 'min:10', 'max:1000'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'consent' => ['sometimes', 'accepted'],
            'hp' => ['prohibited'],
            'website' => ['nullable', 'string', 'max:0'],
            'honeypot' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * Custom validation rules for string / array attributes.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $name = $this->input('name');
            if (is_string($name)) {
                $len = mb_strlen(trim($name));
                if ($len < 2) {
                    $v->errors()->add('name', __('portfolio_validation.messages.min_string', ['min' => 2]));
                } elseif ($len > 80) {
                    $v->errors()->add('name', __('portfolio_validation.messages.max_string', ['max' => 80]));
                }
            } elseif (is_array($name) && empty($name['ar']) && empty($name['en'])) {
                $v->errors()->add('name', __('portfolio_validation.messages.required'));
            }

            $desc = $this->input('description');
            if (is_string($desc)) {
                $len = mb_strlen(trim($desc));
                if ($len < 10) {
                    $v->errors()->add('description', __('portfolio_validation.messages.min_string', ['min' => 10]));
                } elseif ($len > 1000) {
                    $v->errors()->add('description', __('portfolio_validation.messages.max_string', ['max' => 1000]));
                }
            } elseif (is_array($desc) && empty($desc['ar']) && empty($desc['en'])) {
                $v->errors()->add('description', __('portfolio_validation.messages.required'));
            }
        });
    }

    /**
     * Format API validation errors using the standardized ApiResponseTrait envelope.
     */
    protected function failedValidation(ValidatorContract $validator): void
    {
        if ($this->is('api/*') || $this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'code' => 422,
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Convert validated input into normalized structure compatible with Spatie Translatable.
     * Enforces is_active = false and source = 'website'.
     *
     * @return array<string, mixed>
     */
    public function toTestimonialData(): array
    {
        $validated = $this->validated();
        $targetLocale = $this->targetLocale();
        $otherLocale = $targetLocale === 'ar' ? 'en' : 'ar';

        $formatField = static function (mixed $value) use ($targetLocale, $otherLocale): array {
            if (is_array($value)) {
                $ar = isset($value['ar']) && trim((string) $value['ar']) !== '' ? (string) $value['ar'] : null;
                $en = isset($value['en']) && trim((string) $value['en']) !== '' ? (string) $value['en'] : null;

                return ['ar' => $ar, 'en' => $en];
            }

            if ($value === null || trim((string) $value) === '') {
                return ['ar' => null, 'en' => null];
            }

            $str = trim((string) $value);

            return [
                $targetLocale => $str,
                $otherLocale => null,
            ];
        };

        return [
            'name' => $formatField($validated['name'] ?? null),
            'name_job' => $formatField($validated['name_job'] ?? null),
            'company_name' => $formatField($validated['company_name'] ?? null),
            'description' => $formatField($validated['description'] ?? null),
            'meta_title' => $formatField($validated['name'] ?? null),
            'rating' => (int) ($validated['rating'] ?? 5),
            'project_id' => ! empty($validated['project_id']) ? (int) $validated['project_id'] : null,
            'is_active' => false,      // Must be reviewed & activated from dashboard
            'is_featured' => false,
            'source' => 'website',      // Identifies public website origin
            'consent_at' => now(),      // Record GDPR/privacy consent timestamp
        ];
    }
}
