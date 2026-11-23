<?php

/**
 * Testimonial Model
 *
 * Represents a client review and rating for Eng. Abdelrahman Abu Eisha's work.
 */

declare(strict_types=1);

namespace App\Models;

use App\Traits\HandleUploadFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model implements HasMedia
{
    use HandleUploadFile;
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    /**
     * The table associated with the model.
     */
    protected $table = 'testimonials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'name_job',
        'company_name',
        'description',
        'meta_title',
        'rating',
        'is_featured',
        'is_active',
        'sort_order',
        'source',
        'consent_at',
        'parent_id',
        'lft',
        'rgt',
        'depth',
    ];

    /**
     * Translatable JSON attributes managed by Spatie Translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'name',
        'name_job',
        'company_name',
        'description',
        'meta_title',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'consent_at' => 'datetime',
    ];

    /**
     * Resolve a translatable attribute for the given locale with intelligent fallback.
     * If the requested locale is empty, returns the alternate locale or any available translation.
     */
    public function localized(string $attribute, ?string $locale = null): ?string
    {
        $targetLocale = $locale ?? app()->getLocale();
        $value = (string) $this->getTranslation($attribute, $targetLocale, false);
        if (trim($value) !== '') {
            return $value;
        }

        // Fallback to alternate locale (ar <-> en)
        $alternateLocale = $targetLocale === 'ar' ? 'en' : 'ar';
        $altValue = (string) $this->getTranslation($attribute, $alternateLocale, false);
        if (trim($altValue) !== '') {
            return $altValue;
        }

        // Fallback to any first non-empty translation
        $translations = (array) $this->getTranslations($attribute);
        foreach ($translations as $text) {
            if (is_string($text) && trim($text) !== '') {
                return $text;
            }
        }

        return null;
    }

    /**
     * Register media collections for client avatar image.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testimonials')->singleFile();
    }

    /**
     * Get the portfolio project associated with this client testimonial.
     *
     * @return BelongsTo<Project, Testimonial>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Scope a query to only include active testimonials.
     *
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured testimonials.
     *
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order testimonials with newest reviews first.
     *
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Scope a query to only include testimonials from a given source (website or dashboard).
     *
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeFromSource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }
}
