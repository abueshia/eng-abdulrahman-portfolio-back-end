<?php

/**
 * ExperienceService
 *
 * Manages professional career timeline entries, technology pivot synchronization,
 * company logo uploads, reordering, and cache invalidation.
 */

declare(strict_types=1);

namespace App\Services;

use App\Models\Experience;
use App\Repositories\Contracts\ExperienceRepositoryInterface;
use App\Services\Support\PortfolioCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ExperienceService
{
    public const CACHE_MODULE = 'experiences';

    public function __construct(
        private readonly ExperienceRepositoryInterface $experienceRepository,
        private readonly PortfolioCache $cache,
    ) {}

    /**
     * Get all active experiences ordered chronologically with technologies and media (cached).
     *
     * @return Collection<int, Experience>
     */
    public function getActiveTimeline(): Collection
    {
        /** @var Collection<int, Experience> $experiences */
        $experiences = $this->cache->remember(
            self::CACHE_MODULE,
            'timeline',
            fn (): Collection => $this->experienceRepository->getActive(),
        );

        return $experiences;
    }

    /**
     * Paginate experiences for the Admin Dashboard.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateForAdmin(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->experienceRepository->paginateForAdmin($perPage, $filters);
    }

    /**
     * Build an ordered Eloquent query with eager-loaded relations for Admin Yajra DataTables.
     *
     * @return Builder<Experience>
     */
    public function getAdminDataTableQuery(): Builder
    {
        /** @var Builder<Experience> $query */
        $query = $this->experienceRepository->adminQuery(['technologies', 'media']);

        return $query;
    }

    /**
     * Get all experiences ordered by sort_order for the Admin drag-and-drop reorder screen.
     *
     * @return Collection<int, Experience>
     */
    public function getAllForReorder(): Collection
    {
        /** @var Collection<int, Experience> $experiences */
        $experiences = $this->experienceRepository->all(['*'], ['technologies', 'media'])
            ->sortBy('sort_order')
            ->values();

        return $experiences;
    }

    /**
     * Normalize multi-line achievements for each language into clean newline-separated lines.
     * Strips leading list bullet characters, removes empty lines, and enforces reasonable limits.
     *
     * @param  array<string, mixed>|null  $achievements
     * @return array<string, string>
     */
    public function normalizeAchievements(?array $achievements): array
    {
        if ($achievements === null) {
            return [];
        }

        $normalized = [];
        foreach (['ar', 'en'] as $locale) {
            $raw = $achievements[$locale] ?? null;
            if (is_array($raw)) {
                $lines = $raw;
            } elseif (is_string($raw) && trim($raw) !== '') {
                // Normalize line breaks (\r\n, \r -> \n)
                $standardized = str_replace(["\r\n", "\r"], "\n", $raw);
                $lines = explode("\n", $standardized);
            } else {
                $normalized[$locale] = '';

                continue;
            }

            $cleanedLines = [];
            foreach ($lines as $line) {
                if (! is_string($line)) {
                    continue;
                }
                // Strip leading list bullets, dash, asterisk, plus, bullet dots, numbers like 1. or 1)
                $stripped = trim((string) preg_replace('/^([-\*+•·‣]|\d+[\.\)])\s*/u', '', trim($line)));

                if ($stripped !== '') {
                    // Limit max 300 characters per achievement line
                    if (mb_strlen($stripped) > 300) {
                        $stripped = mb_substr($stripped, 0, 300);
                    }
                    $cleanedLines[] = $stripped;
                }

                // Max 12 achievements per experience
                if (count($cleanedLines) >= 12) {
                    break;
                }
            }

            $normalized[$locale] = implode("\n", $cleanedLines);
        }

        return $normalized;
    }

    /**
     * Create a new experience entry from validated data.
     *
     * @param  array<string, mixed>  $validated
     */
    public function create(array $validated, ?UploadedFile $image = null): Experience
    {
        $experience = DB::transaction(function () use ($validated, $image): Experience {
            if (array_key_exists('achievements', $validated) && is_array($validated['achievements'])) {
                $validated['achievements'] = $this->normalizeAchievements($validated['achievements']);
            }

            $technologyIds = is_array($validated['technologies'] ?? null)
                ? array_map('intval', $validated['technologies'])
                : [];

            $uploadedImage = $image ?? (isset($validated['image']) && $validated['image'] instanceof UploadedFile ? $validated['image'] : null);
            unset($validated['technologies'], $validated['image']);

            if (! empty($validated['is_current'])) {
                $validated['end_date'] = null;
            }

            $validated['sort_order'] = isset($validated['sort_order']) && $validated['sort_order'] !== ''
                ? (int) $validated['sort_order']
                : $this->experienceRepository->nextSortOrder();

            /** @var Experience $created */
            $created = $this->experienceRepository->create($validated);

            $this->experienceRepository->syncTechnologies($created, $technologyIds);

            if ($uploadedImage !== null) {
                $created->storeFile($uploadedImage);
            }

            return $created->load(['technologies', 'media']);
        });

        $this->flushCache();

        return $experience;
    }

    /**
     * Update an existing experience entry from validated data.
     *
     * @param  array<string, mixed>  $validated
     */
    public function update(Experience $experience, array $validated, ?UploadedFile $image = null): Experience
    {
        $updated = DB::transaction(function () use ($experience, $validated, $image): Experience {
            if (array_key_exists('achievements', $validated) && is_array($validated['achievements'])) {
                $validated['achievements'] = $this->normalizeAchievements($validated['achievements']);
            }

            $hasTechKey = array_key_exists('technologies', $validated);
            $technologyIds = is_array($validated['technologies'] ?? null)
                ? array_map('intval', $validated['technologies'])
                : [];

            $uploadedImage = $image ?? (isset($validated['image']) && $validated['image'] instanceof UploadedFile ? $validated['image'] : null);
            unset($validated['technologies'], $validated['image']);

            if (! empty($validated['is_current'])) {
                $validated['end_date'] = null;
            }

            /** @var Experience $saved */
            $saved = $this->experienceRepository->update($experience, $validated);

            if ($hasTechKey) {
                $this->experienceRepository->syncTechnologies($saved, $technologyIds);
            }

            if ($uploadedImage !== null) {
                $saved->updateFile($uploadedImage);
            }

            return $saved->load(['technologies', 'media']);
        });

        $this->flushCache();

        return $updated;
    }

    /**
     * Delete an experience entry and detach its technologies/media.
     */
    public function delete(Experience $experience): bool
    {
        $deleted = DB::transaction(function () use ($experience): bool {
            $experience->technologies()->detach();
            $experience->deleteFiles();

            return $this->experienceRepository->delete($experience);
        });

        $this->flushCache();

        return $deleted;
    }

    /**
     * Reorder experience timeline items.
     *
     * @param  array<int, int|array{id: int, sort_order?: int}>  $items
     */
    public function reorder(array $items): void
    {
        DB::transaction(function () use ($items): void {
            $this->experienceRepository->reorder($items);
        });

        $this->flushCache();
    }

    /**
     * Flush cached experience query results.
     */
    public function flushCache(): void
    {
        $this->cache->flush([self::CACHE_MODULE]);
    }
}
