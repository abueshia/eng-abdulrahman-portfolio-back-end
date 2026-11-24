<?php

/**
 * PublicApiHardeningTest
 *
 * Covers the public API contract fixes: query-preserving pagination links,
 * the always-array `challenge` field, fully translated validation messages for
 * the public POST endpoints, hiding unpublished projects linked from testimonials,
 * and the global rate limit on public GET endpoints.
 *
 * Runs against the dedicated test database configured in phpunit.xml.
 */

declare(strict_types=1);

namespace Tests\Feature\API;

use App\Enums\ProjectStatusEnum;
use App\Enums\ProjectTypeEnum;
use App\Models\Project;
use App\Models\Testimonial;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PublicApiHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Item 1: every pagination link keeps lang, per_page and all filters.
     */
    public function test_pagination_links_preserve_lang_per_page_and_filters(): void
    {
        Project::factory()->count(3)->create(['project_type' => ProjectTypeEnum::Saas]);
        Project::factory()->create(['project_type' => ProjectTypeEnum::WebApp]);

        $response = $this->getJson('/api/v1/projects?lang=ar&per_page=1&type=saas');
        $response->assertOk()
            ->assertJsonPath('pagination.total_items', 3)
            ->assertJsonPath('pagination.total_pages', 3)
            ->assertJsonPath('pagination.prev_page_url', '');

        $expected = ['lang' => 'ar', 'per_page' => '1', 'type' => 'saas'];
        $this->assertLinkQuery((string) $response->json('pagination.first_page_url'), $expected + ['page' => '1']);
        $this->assertLinkQuery((string) $response->json('pagination.next_page_url'), $expected + ['page' => '2']);
        $this->assertLinkQuery((string) $response->json('pagination.last_page_url'), $expected + ['page' => '3']);

        // Following the link as-is keeps the filter, page size and language.
        $next = $this->getJson((string) $response->json('pagination.next_page_url'));
        $next->assertOk()
            ->assertJsonPath('pagination.current_page', 2)
            ->assertJsonPath('pagination.total_items', 3)
            ->assertJsonCount(1, 'data');
        $this->assertLinkQuery((string) $next->json('pagination.prev_page_url'), $expected + ['page' => '1']);
    }

    /**
     * Item 2: challenge is always an array (empty when missing, wrapped when legacy text).
     */
    public function test_challenge_is_always_an_array(): void
    {
        $empty = Project::factory()->create(['challenge' => ['ar' => '', 'en' => '']]);
        $structured = Project::factory()->create(['challenge' => [
            'ar' => [['title' => 'عنوان', 'description' => 'وصف']],
            'en' => [['title' => 'Scaling', 'description' => 'High traffic']],
        ]]);
        $legacy = Project::factory()->create(['challenge' => ['ar' => 'نص قديم', 'en' => 'Legacy text']]);

        $this->getJson("/api/v1/projects/{$empty->slug}?lang=en")
            ->assertOk()
            ->assertJsonPath('data.challenge', []);

        $this->getJson("/api/v1/projects/{$structured->slug}?lang=en")
            ->assertOk()
            ->assertJsonPath('data.challenge', [['title' => 'Scaling', 'description' => 'High traffic']]);

        $this->getJson("/api/v1/projects/{$legacy->slug}?lang=ar")
            ->assertOk()
            ->assertJsonPath('data.challenge', [['title' => '', 'description' => 'نص قديم']]);

        foreach ($this->getJson('/api/v1/projects?lang=en&per_page=50')->json('data') as $project) {
            $this->assertIsArray($project['challenge']);
        }
    }

    /**
     * Item 3: no raw translation key or unresolved placeholder in any POST validation message.
     */
    public function test_public_post_validation_messages_are_fully_translated(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $testimonialPayloads = [
            [],
            [
                'name' => 'A', 'description' => 'short', 'rating' => 9, 'project_id' => 999999,
                'locale' => 'fr', 'consent' => 'no', 'website' => 'bot', 'honeypot' => 'bot', 'hp' => 'bot',
            ],
            ['name' => ['ar' => '', 'en' => ''], 'description' => ['ar' => '', 'en' => '']],
            [
                'name' => str_repeat('a', 81), 'description' => str_repeat('b', 1001), 'rating' => 'abc',
                'project_id' => 'x', 'name_job' => str_repeat('c', 101), 'company_name' => str_repeat('d', 101),
            ],
            [
                'name' => ['ar' => 'a', 'en' => str_repeat('e', 81)],
                'description' => ['ar' => 'short', 'en' => str_repeat('f', 1001)],
                'name_job' => ['ar' => str_repeat('g', 101)], 'company_name' => ['en' => str_repeat('h', 101)],
                'rating' => 0,
            ],
        ];

        $contactPayloads = [
            [],
            [
                'name' => ['not', 'a', 'string'], 'email' => 'not-an-email', 'message' => str_repeat('m', 5001),
                'phone' => str_repeat('1', 51), 'subject' => str_repeat('s', 256), 'service' => str_repeat('v', 151),
                'budget_range' => str_repeat('b', 121), 'timeline' => str_repeat('t', 121),
                'type' => 'unknown_type', 'website' => 'bot', 'honeypot' => 'bot',
            ],
            ['name' => str_repeat('n', 151), 'email' => str_repeat('e', 150).'@example.com', 'message' => 'Hello'],
        ];

        foreach (['ar', 'en'] as $locale) {
            $responses = [];

            foreach ($testimonialPayloads as $payload) {
                $responses[] = $this->postJson("/api/v1/testimonials?lang={$locale}", $payload);
            }

            // File rules (multipart): wrong type, too large, and a non-file value.
            $responses[] = $this->post("/api/v1/testimonials?lang={$locale}", [
                'name' => 'Valid Name', 'description' => 'A valid testimonial body.',
                'image' => UploadedFile::fake()->create('document.pdf', 6000, 'application/pdf'),
                'avatar' => 'not-a-file',
            ], ['Accept' => 'application/json']);

            foreach ($contactPayloads as $payload) {
                $responses[] = $this->postJson("/api/v1/contact?lang={$locale}", $payload);
            }

            $this->assertMessagesAreTranslated($responses, $locale);
        }
    }

    /**
     * Item 4: a testimonial never links to an archived, draft or inactive project.
     */
    public function test_testimonial_project_is_null_when_project_is_not_published(): void
    {
        $published = Project::factory()->create();
        $archived = Project::factory()->create(['status' => ProjectStatusEnum::Archived]);
        $draft = Project::factory()->create(['status' => ProjectStatusEnum::Draft]);
        $inactive = Project::factory()->create(['is_active' => false]);

        $expectations = [];
        foreach ([$published, $archived, $draft, $inactive] as $index => $project) {
            $testimonial = Testimonial::factory()->create(['project_id' => $project->id, 'sort_order' => $index + 1]);
            $expectations[$testimonial->id] = $project->is($published) ? $published->slug : null;
        }

        $data = collect($this->getJson('/api/v1/testimonials?lang=en')->assertOk()->json('data'))->keyBy('id');

        foreach ($expectations as $testimonialId => $expectedSlug) {
            $project = $data[$testimonialId]['project'];

            if ($expectedSlug === null) {
                $this->assertNull($project, "Testimonial {$testimonialId} must not expose an unpublished project.");
            } else {
                $this->assertSame($expectedSlug, $project['slug']);
                $this->getJson("/api/v1/projects/{$expectedSlug}")->assertOk();
            }
        }
    }

    /**
     * Item 5: public GET endpoints are limited to 120 requests per minute per IP,
     * while POST endpoints keep only their own route-level throttle.
     */
    public function test_public_get_endpoints_are_throttled_per_minute(): void
    {
        $limit = RouteServiceProvider::PUBLIC_API_READS_PER_MINUTE;
        $this->assertSame(120, $limit);

        $first = $this->getJson('/api/v1/faqs');
        $first->assertOk()
            ->assertHeader('X-RateLimit-Limit', (string) $limit)
            ->assertHeader('X-RateLimit-Remaining', (string) ($limit - 1));

        for ($i = 2; $i <= $limit; $i++) {
            $this->getJson($i % 2 === 0 ? '/api/v1/projects' : '/api/v1/experiences')->assertOk();
        }

        $this->getJson('/api/v1/testimonials')
            ->assertStatus(429)
            ->assertHeader('X-RateLimit-Limit', (string) $limit);

        // POST endpoints are not counted against nor blocked by the read limit.
        $this->postJson('/api/v1/contact', [])
            ->assertStatus(422)
            ->assertHeader('X-RateLimit-Limit', '5');
    }

    /**
     * Assert that a pagination link carries exactly the expected query parameters.
     *
     * @param  array<string, string>  $expected
     */
    private function assertLinkQuery(string $url, array $expected): void
    {
        $this->assertNotSame('', $url);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        ksort($query);
        ksort($expected);

        $this->assertSame($expected, $query, "Unexpected query string in {$url}");
    }

    /**
     * @param  array<int, TestResponse>  $responses
     */
    private function assertMessagesAreTranslated(array $responses, string $locale): void
    {
        $messages = [];

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $messages[] = (string) $response->json('message');

            foreach ((array) $response->json('errors') as $fieldMessages) {
                foreach ((array) $fieldMessages as $message) {
                    $messages[] = (string) $message;
                }
            }
        }

        $this->assertNotEmpty($messages);

        foreach (array_unique($messages) as $message) {
            $this->assertDoesNotMatchRegularExpression('/\b(validation|portfolio_validation)\.[a-z_.]+/', $message, "Raw translation key [{$locale}]: {$message}");
            $this->assertDoesNotMatchRegularExpression('/:[a-z_]+\b/', $message, "Unresolved placeholder [{$locale}]: {$message}");

            if ($locale === 'ar') {
                $this->assertMatchesRegularExpression('/\p{Arabic}/u', $message, "Untranslated Arabic message: {$message}");
                $this->assertDoesNotMatchRegularExpression('/\b(The|field|must|selected)\b/', $message, "English text in Arabic message: {$message}");
            } else {
                $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $message, "Arabic text in English message: {$message}");
            }
        }
    }
}
