<?php

/**
 * ProjectViewModel
 *
 * Supplies form state, route action/method, active categories, active technologies,
 * and enum options to the Admin Project create/edit form view.
 */

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\ProjectStatusEnum;
use App\Enums\ProjectTypeEnum;
use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\TechnologyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\ViewModels\ViewModel;

class ProjectViewModel extends ViewModel
{
    public Project $project;

    public function __construct(?Project $project = null)
    {
        $this->project = $project ?? new Project;
    }

    public function action(): string
    {
        return $this->project->exists
            ? route('admin.project.update', ['project' => $this->project->id])
            : route('admin.project.store');
    }

    public function method(): string
    {
        return $this->project->exists ? 'PUT' : 'POST';
    }

    /**
     * @return Collection<int, Category>
     */
    public function categories(): Collection
    {
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = app(CategoryRepositoryInterface::class);

        return $categoryRepository->getActive();
    }

    /**
     * @return Collection<int, Technology>
     */
    public function technologies(): Collection
    {
        /** @var TechnologyRepositoryInterface $technologyRepository */
        $technologyRepository = app(TechnologyRepositoryInterface::class);

        return $technologyRepository->getActive();
    }

    /**
     * @return array<string, string>
     */
    public function projectTypes(): array
    {
        return ProjectTypeEnum::options();
    }

    /**
     * @return array<string, string>
     */
    public function projectStatuses(): array
    {
        return ProjectStatusEnum::options();
    }

    /**
     * Resolve the structured challenge items for each locale, preferring old input.
     *
     * Each locale stores a list of `{title, description}` items. Legacy plain-text
     * values are wrapped into a single item so they remain editable.
     *
     * @return array<string, array<int, array{title: string, description: string}>>
     */
    public function challengeItems(): array
    {
        $items = [];

        foreach (array_keys((array) config('language')) as $locale) {
            $locale = (string) $locale;
            $raw = old('challenge.'.$locale, $this->project->getTranslation('challenge', $locale, false));

            $items[$locale] = $this->normalizeChallengeItems($raw);
        }

        return $items;
    }

    /**
     * @return array<int, int>
     */
    public function selectedTechnologyIds(): array
    {
        if (! $this->project->exists) {
            return [];
        }

        return $this->project->technologies
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<int, array{title: string, description: string}>
     */
    private function normalizeChallengeItems(mixed $raw): array
    {
        if (is_string($raw)) {
            return trim($raw) === '' ? [] : [['title' => '', 'description' => $raw]];
        }

        if (! is_array($raw)) {
            return [];
        }

        $items = [];

        foreach ($raw as $item) {
            if (is_string($item)) {
                $items[] = ['title' => '', 'description' => $item];

                continue;
            }

            if (is_array($item)) {
                $items[] = [
                    'title' => is_scalar($item['title'] ?? null) ? (string) $item['title'] : '',
                    'description' => is_scalar($item['description'] ?? null) ? (string) $item['description'] : '',
                ];
            }
        }

        return $items;
    }
}
