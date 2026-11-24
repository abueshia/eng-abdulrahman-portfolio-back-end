<?php

/**
 * FrontendDeployHookTest
 *
 * Verifies that saving or deleting public content (projects, experiences, FAQs,
 * approved testimonials) calls the Vercel Deploy Hook from config, at most once
 * per debounce window, and that a missing URL or a failing hook never breaks saving.
 *
 * Runs against the dedicated test database configured in phpunit.xml.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\Support\FrontendDeployHook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FrontendDeployHookTest extends TestCase
{
    use RefreshDatabase;

    private const HOOK_URL = 'https://api.vercel.com/v1/integrations/deploy/prj_test/hook_test';

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        config(['services.vercel.deploy_hook_url' => self::HOOK_URL]);
        Cache::forget(FrontendDeployHook::LOCK_KEY);
    }

    public function test_nothing_is_sent_when_hook_url_is_not_configured(): void
    {
        $this->fakeHook();
        config(['services.vercel.deploy_hook_url' => null]);

        Project::factory()->create();
        Faq::factory()->create();

        Http::assertNothingSent();
    }

    public function test_saving_and_deleting_public_content_triggers_the_hook(): void
    {
        $this->fakeHook();
        $expected = 0;

        $models = [
            'project' => fn () => Project::factory()->create(),
            'experience' => fn () => Experience::factory()->create(),
            'faq' => fn () => Faq::factory()->create(),
        ];

        foreach ($models as $label => $factory) {
            $model = $factory();
            $this->assertHookCalledTimes(++$expected, "{$label} created");

            $this->resetDebounce();
            $model->update(['sort_order' => 99]);
            $this->assertHookCalledTimes(++$expected, "{$label} updated");

            $this->resetDebounce();
            $model->delete();
            $this->assertHookCalledTimes(++$expected, "{$label} deleted");

            $this->resetDebounce();
        }

        Http::assertSent(fn ($request): bool => $request->method() === 'POST' && $request->url() === self::HOOK_URL);
    }

    public function test_hook_is_debounced_to_one_call_per_window(): void
    {
        $this->fakeHook();
        Project::factory()->create();
        Experience::factory()->create();
        Faq::factory()->create();
        Project::query()->first()?->update(['sort_order' => 5]);

        Http::assertSentCount(1);

        // Once the window expires, the next change triggers a new build.
        Cache::forget(FrontendDeployHook::LOCK_KEY);
        Faq::factory()->create();

        Http::assertSentCount(2);
    }

    public function test_only_approved_testimonials_trigger_the_hook(): void
    {
        $this->fakeHook();

        // A pending website submission must not rebuild the site.
        $pending = Testimonial::factory()->create(['is_active' => false, 'project_id' => null]);
        $pending->update(['rating' => 4]);
        Http::assertNothingSent();

        // Approving it does.
        $pending->update(['is_active' => true]);
        Http::assertSentCount(1);

        // Un-approving it does too (it disappears from the site).
        $this->resetDebounce();
        $pending->update(['is_active' => false]);
        Http::assertSentCount(2);

        // Deleting a pending testimonial does not; deleting an approved one does.
        $this->resetDebounce();
        $pending->delete();
        Http::assertSentCount(2);

        $approved = Testimonial::factory()->create(['is_active' => true, 'project_id' => null]);
        Http::assertSentCount(3);

        $this->resetDebounce();
        $approved->delete();
        Http::assertSentCount(4);
    }

    public function test_a_failing_hook_never_breaks_saving(): void
    {
        // First call: connection failure. Second call: HTTP 500.
        $calls = 0;
        Http::fake([self::HOOK_URL => static function () use (&$calls) {
            if (++$calls === 1) {
                throw new ConnectionException('Connection timed out');
            }

            return Http::response('Internal Server Error', 500);
        }]);

        $project = Project::factory()->create();
        $this->assertModelExists($project);

        $this->resetDebounce();
        $faq = Faq::factory()->create();
        $this->assertModelExists($faq);

        $this->assertSame(2, $calls);
    }

    private function fakeHook(): void
    {
        Http::fake([self::HOOK_URL => Http::response(['job' => ['state' => 'PENDING']], 201)]);
    }

    private function resetDebounce(): void
    {
        Cache::forget(FrontendDeployHook::LOCK_KEY);
    }

    private function assertHookCalledTimes(int $times, string $context): void
    {
        $sent = Http::recorded(fn ($request): bool => $request->url() === self::HOOK_URL)->count();
        $this->assertSame($times, $sent, "Unexpected deploy hook calls after: {$context}");
    }
}
