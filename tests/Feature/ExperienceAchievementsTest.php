<?php

/**
 * ExperienceAchievementsTest
 *
 * Feature tests verifying that career experience achievements are properly
 * normalized into clean lines on persistence, and that ExperienceResource transforms
 * them into arrays of clean strings with locale fallback and legacy compatibility.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EmploymentTypeEnum;
use App\Http\Resources\UserApp\ExperienceResource;
use App\Models\Experience;
use App\Models\User;
use App\Services\ExperienceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceAchievementsTest extends TestCase
{
    use RefreshDatabase;

    private ExperienceService $experienceService;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshApplicationWithLocale('en');
        $this->seed();
        $this->experienceService = app(ExperienceService::class);
        $this->admin = User::query()->first() ?? User::factory()->create();
        $this->admin->assignRole('Super admin');
    }

    /**
     * Test that achievements are normalized on creation and update:
     * - leading list bullets (-, *, +, •, ·, 1., 2)) are stripped
     * - empty lines are removed
     * - lines are standardized with \n
     */
    public function test_achievements_are_normalized_on_save_by_stripping_bullets_and_empty_lines(): void
    {
        $rawArabic = "- قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.\r\n\r\n+ أسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.\n\n• أشرفت على مراجعة الكود وتوجيه مطورين جدد.\n";
        $rawEnglish = "1. Led monolith-to-modules restructuring to improve response latency.\r\n\r\n* Built automated CI/CD pipeline using GitHub Actions.\n\n2) Mentored junior developers and performed code reviews.\n";

        $data = [
            'company_name' => ['ar' => 'شركة تجريبية', 'en' => 'Test Company'],
            'role_title' => ['ar' => 'مهندس برمجيات أول', 'en' => 'Senior Software Engineer'],
            'employment_type' => EmploymentTypeEnum::FullTime->value,
            'start_date' => '2023-01-01',
            'summary' => ['ar' => 'ملخص تجريبي للخبرة', 'en' => 'Test experience summary'],
            'achievements' => [
                'ar' => $rawArabic,
                'en' => $rawEnglish,
            ],
            'is_active' => true,
        ];

        $experience = $this->experienceService->create($data);

        $expectedArabic = "قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.\nأسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.\nأشرفت على مراجعة الكود وتوجيه مطورين جدد.";
        $expectedEnglish = "Led monolith-to-modules restructuring to improve response latency.\nBuilt automated CI/CD pipeline using GitHub Actions.\nMentored junior developers and performed code reviews.";

        $this->assertSame($expectedArabic, $experience->getTranslation('achievements', 'ar', false));
        $this->assertSame($expectedEnglish, $experience->getTranslation('achievements', 'en', false));
    }

    /**
     * Test that achievements submitted as an array of items (repeater inputs) are saved as clean lines.
     */
    public function test_achievements_can_be_submitted_as_repeater_array_and_saved_as_lines(): void
    {
        $payload = [
            'company_name' => ['ar' => 'شركة التقنية المتقدمة', 'en' => 'Advanced Tech'],
            'role_title' => ['ar' => 'قائد فريق برمجيات', 'en' => 'Tech Lead'],
            'employment_type' => EmploymentTypeEnum::FullTime->value,
            'start_date' => '2023-06-01',
            'summary' => ['ar' => 'قيادة الفرق الفنية وتطوير البنية التحتية', 'en' => 'Leading engineering teams and infrastructure'],
            'achievements' => [
                'ar' => [
                    '+ قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.',
                    'أسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.',
                    '• أشرفت على مراجعة الكود وتوجيه مطورين جدد.',
                    '', // empty row that should be filtered out
                ],
                'en' => [
                    'Led system modularization improving latency.',
                    'Built automated CI/CD pipeline.',
                ],
            ],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.experience.store'), $payload);
        $response->assertRedirect(route('admin.experience.index'));

        $experience = Experience::query()->where('company_name->en', 'Advanced Tech')->firstOrFail();

        $expectedAr = "قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.\nأسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.\nأشرفت على مراجعة الكود وتوجيه مطورين جدد.";
        $this->assertSame($expectedAr, $experience->getTranslation('achievements', 'ar', false));

        // And verify API returns array of strings
        app()->setLocale('ar');
        $resource = (new ExperienceResource($experience))->toArray(request());
        $this->assertCount(3, $resource['achievements']);
        $this->assertSame('قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.', $resource['achievements'][0]);
    }

    /**
     * Test that the admin experience edit form renders successfully with existing achievements.
     */
    public function test_admin_experience_edit_screen_renders_successfully(): void
    {
        $experience = Experience::query()->firstOrFail();

        $response = $this->actingAs($this->admin, 'admin')->get("/en/admin/experience/{$experience->id}/edit");

        $response->assertOk()
            ->assertSee('achievements_container_ar', false)
            ->assertSee('achievements_container_en', false);
    }

    /**
     * Test that ExperienceResource transforms multi-line achievements into an array of strings.
     */
    public function test_experience_resource_returns_array_of_strings_for_multiline_achievements(): void
    {
        $experience = Experience::query()->create([
            'company_name' => ['ar' => 'شركة نموذجية', 'en' => 'Sample Corp'],
            'role_title' => ['ar' => 'مهندس معماري برمجيات', 'en' => 'Software Architect'],
            'employment_type' => EmploymentTypeEnum::FullTime,
            'start_date' => '2024-01-01',
            'summary' => ['ar' => 'ملخص المنصب', 'en' => 'Role summary'],
            'achievements' => [
                'ar' => "قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.\nأسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.\nأشرفت على مراجعة الكود وتوجيه مطورين جدد.",
                'en' => "Led monolith restructuring.\nBuilt CI/CD pipeline.\nMentored developers.",
            ],
            'is_active' => true,
        ]);

        app()->setLocale('ar');
        $resourceAr = (new ExperienceResource($experience))->toArray(request());

        $this->assertIsArray($resourceAr['achievements']);
        $this->assertCount(3, $resourceAr['achievements']);
        $this->assertSame('قدت إعادة هيكلة monolith إلى modules مستقلة لتحسين زمن الاستجابة.', $resourceAr['achievements'][0]);
        $this->assertSame('أسست pipeline للاختبارات والنشر المستمر عبر GitHub Actions.', $resourceAr['achievements'][1]);
        $this->assertSame('أشرفت على مراجعة الكود وتوجيه مطورين جدد.', $resourceAr['achievements'][2]);

        app()->setLocale('en');
        $resourceEn = (new ExperienceResource($experience))->toArray(request());

        $this->assertIsArray($resourceEn['achievements']);
        $this->assertCount(3, $resourceEn['achievements']);
        $this->assertSame('Led monolith restructuring.', $resourceEn['achievements'][0]);
    }

    /**
     * Test that legacy single sentence achievements return a 1-element array (backward compatibility).
     */
    public function test_experience_resource_supports_legacy_single_sentence_achievements(): void
    {
        $experience = Experience::query()->create([
            'company_name' => ['ar' => 'شركة سابقة', 'en' => 'Previous Company'],
            'role_title' => ['ar' => 'مطور Laravel', 'en' => 'Laravel Developer'],
            'employment_type' => EmploymentTypeEnum::Contract,
            'start_date' => '2021-01-01',
            'summary' => ['ar' => 'ملخص', 'en' => 'Summary'],
            'achievements' => [
                'ar' => 'تطوير وتسليم أكثر من 15 نظاماً مؤسسياً وواجهة RESTful API مع تحسين أداء قواعد البيانات.',
                'en' => 'Architected and delivered 15+ production web platforms and RESTful APIs.',
            ],
            'is_active' => true,
        ]);

        app()->setLocale('ar');
        $resource = (new ExperienceResource($experience))->toArray(request());

        $this->assertIsArray($resource['achievements']);
        $this->assertCount(1, $resource['achievements']);
        $this->assertSame('تطوير وتسليم أكثر من 15 نظاماً مؤسسياً وواجهة RESTful API مع تحسين أداء قواعد البيانات.', $resource['achievements'][0]);
    }

    /**
     * Test that empty achievements return an empty array.
     */
    public function test_experience_resource_returns_empty_array_when_achievements_empty(): void
    {
        $experience = Experience::query()->create([
            'company_name' => ['ar' => 'شركة', 'en' => 'Company'],
            'role_title' => ['ar' => 'مهندس', 'en' => 'Engineer'],
            'employment_type' => EmploymentTypeEnum::FullTime,
            'start_date' => '2020-01-01',
            'summary' => ['ar' => 'ملخص', 'en' => 'Summary'],
            'achievements' => [
                'ar' => '',
                'en' => '',
            ],
            'is_active' => true,
        ]);

        app()->setLocale('ar');
        $resource = (new ExperienceResource($experience))->toArray(request());

        $this->assertIsArray($resource['achievements']);
        $this->assertEmpty($resource['achievements']);
    }

    /**
     * Test locale fallback: if requested locale is empty, fallback to the alternate locale.
     */
    public function test_experience_resource_falls_back_to_alternate_locale_when_requested_locale_is_empty(): void
    {
        $experience = Experience::query()->create([
            'company_name' => ['ar' => 'شركة التكنولوجيا', 'en' => 'Tech Corp'],
            'role_title' => ['ar' => 'مطور واجهات خلفية', 'en' => 'Backend Developer'],
            'employment_type' => EmploymentTypeEnum::FullTime,
            'start_date' => '2022-01-01',
            'summary' => ['ar' => 'ملخص بالعربي', 'en' => 'English summary'],
            'achievements' => [
                'ar' => "إنجاز أول بالعربي\nإنجاز ثان بالعربي",
                'en' => '', // Empty English achievements
            ],
            'is_active' => true,
        ]);

        app()->setLocale('en'); // Requesting English
        $resource = (new ExperienceResource($experience))->toArray(request());

        $this->assertIsArray($resource['achievements']);
        $this->assertCount(2, $resource['achievements']);
        $this->assertSame('إنجاز أول بالعربي', $resource['achievements'][0]);
        $this->assertSame('إنجاز ثان بالعربي', $resource['achievements'][1]);
    }

    /**
     * Test public API experiences endpoint returns achievements as an array.
     */
    public function test_public_api_experiences_endpoint_returns_achievements_array(): void
    {
        $response = $this->getJson('/api/v1/experiences', ['Accept-Language' => 'ar']);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'company_name',
                        'role_title',
                        'achievements',
                        'highlights',
                        'start_date',
                    ],
                ],
            ]);

        $firstAchievements = $response->json('data.0.achievements');
        $this->assertIsArray($firstAchievements);
    }

    /**
     * Test admin can update experience with end_date, ensuring is_current is automatically turned off.
     */
    public function test_admin_can_update_experience_with_end_date(): void
    {
        $experience = Experience::factory()->create([
            'start_date' => '2021-01-01',
            'end_date' => null,
            'is_current' => true,
        ]);

        $payload = [
            'company_name' => ['ar' => 'شركة أ', 'en' => 'Company A'],
            'role_title' => ['ar' => 'مطور', 'en' => 'Developer'],
            'employment_type' => EmploymentTypeEnum::Freelance->value,
            'start_date' => '2021-01-01',
            'end_date' => '2022-06-30',
            'is_current' => 1, // Even if submitted as 1, specifying end_date must override it to false
            'summary' => ['ar' => 'ملخص عربي', 'en' => 'English summary'],
            'achievements' => [
                'ar' => ['إنجاز 1'],
                'en' => ['Achievement 1'],
            ],
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.experience.update', $experience), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.experience.index'));

        $experience->refresh();
        $this->assertSame('2022-06-30', $experience->end_date?->format('Y-m-d'));
        $this->assertFalse($experience->is_current);
    }
}
