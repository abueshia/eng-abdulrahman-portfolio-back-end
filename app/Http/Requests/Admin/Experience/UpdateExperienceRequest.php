<?php

/**
 * UpdateExperienceRequest
 *
 * Validates updating an existing professional career experience entry.
 */

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Enums\EmploymentTypeEnum;
use App\Http\Requests\BasePortfolioFormRequest;
use Illuminate\Validation\Rule;

class UpdateExperienceRequest extends BasePortfolioFormRequest
{
    public function authorize(): bool
    {
        return $this->authorizeAdminPermission('experiences.edit');
    }

    /**
     * Prepare the input data for validation by normalizing achievement lines.
     */
    protected function prepareForValidation(): void
    {
        $achievements = $this->input('achievements');
        if (is_array($achievements)) {
            $cleaned = [];
            foreach (['ar', 'en'] as $loc) {
                $val = $achievements[$loc] ?? null;
                if (is_string($val)) {
                    $standardized = str_replace(["\r\n", "\r"], "\n", $val);
                    $lines = explode("\n", $standardized);
                    $cleanedLines = [];
                    foreach ($lines as $line) {
                        $stripped = trim((string) preg_replace('/^([-\*+•·‣]|\d+[\.\)])\s*/u', '', trim($line)));
                        if ($stripped !== '') {
                            $cleanedLines[] = mb_strlen($stripped) > 300 ? mb_substr($stripped, 0, 300) : $stripped;
                        }
                        if (count($cleanedLines) >= 12) {
                            break;
                        }
                    }
                    $cleaned[$loc] = implode("\n", $cleanedLines);
                } else {
                    $cleaned[$loc] = $val;
                }
            }
            $this->merge(['achievements' => $cleaned]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'array'],
            'company_name.ar' => ['required', 'string', 'max:255'],
            'company_name.en' => ['required', 'string', 'max:255'],
            'role_title' => ['required', 'array'],
            'role_title.ar' => ['required', 'string', 'max:255'],
            'role_title.en' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', Rule::enum(EmploymentTypeEnum::class)],
            'location' => ['nullable', 'array'],
            'location.ar' => ['nullable', 'string', 'max:255'],
            'location.en' => ['nullable', 'string', 'max:255'],
            'company_url' => ['nullable', 'url', 'max:500'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'summary' => ['required', 'array'],
            'summary.ar' => ['required', 'string'],
            'summary.en' => ['required', 'string'],
            'achievements' => ['nullable', 'array'],
            'achievements.ar' => ['nullable', 'string'],
            'achievements.en' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['integer', 'exists:technologies,id'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ];
    }
}
