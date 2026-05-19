<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\PlanGrant;
use App\Services\PlanService;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected PlanService $planService;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
        $this->planService = app(PlanService::class);
    }

    #[Test]
    public function grant_plan_sets_user_plan_and_expiry()
    {
        $this->travelTo(now()->startOfMinute());

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: CarbonInterval::days(14),
            source: 'stripe_subscription',
        );

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertTrue($this->user->plan_expires_at->equalTo(now()->addDays(14)));
        $this->assertNull($this->user->trial_ends_at);
        $this->assertFalse($this->user->has_used_trial);
    }

    #[Test]
    public function trial_grant_sets_trial_fields_and_flips_has_used_trial()
    {
        $this->travelTo(now()->startOfMinute());

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: CarbonInterval::days(14),
            source: 'signup_trial',
            isTrial: true,
        );

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertTrue($this->user->plan_expires_at->equalTo(now()->addDays(14)));
        $this->assertTrue($this->user->trial_ends_at->equalTo(now()->addDays(14)));
        $this->assertTrue($this->user->has_used_trial);
        $this->assertNull($this->user->trial_reminder_stage);
    }

    #[Test]
    public function trial_grant_resets_reminder_stage()
    {
        $this->user->update(['trial_reminder_stage' => 3]);

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: CarbonInterval::days(14),
            source: 'signup_trial',
            isTrial: true,
        );

        $this->assertNull($this->user->fresh()->trial_reminder_stage);
    }

    #[Test]
    public function non_trial_grant_does_not_flip_has_used_trial()
    {
        $this->planService->grantPlan(
            user: $this->user,
            plan: 'standard',
            duration: CarbonInterval::days(30),
            source: 'promo:WELCOME',
        );

        $this->assertFalse($this->user->fresh()->has_used_trial);
    }

    #[Test]
    public function grant_writes_audit_row()
    {
        $this->travelTo(now()->startOfMinute());

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: CarbonInterval::days(14),
            source: 'signup_trial',
            isTrial: true,
        );

        $this->assertDatabaseCount('plan_grants', 1);

        $grant = PlanGrant::first();
        $this->assertSame($this->user->id, $grant->user_id);
        $this->assertSame('pro', $grant->plan);
        $this->assertSame('signup_trial', $grant->source);
        $this->assertTrue($grant->started_at->equalTo(now()));
        $this->assertTrue($grant->ends_at->equalTo(now()->addDays(14)));
    }

    #[Test]
    public function user_on_trial_returns_true_when_trial_ends_at_is_future()
    {
        $this->user->update(['trial_ends_at' => now()->addDay()]);

        $this->assertTrue($this->user->onTrial());
    }

    #[Test]
    public function user_on_trial_returns_false_when_trial_ends_at_is_past()
    {
        $this->user->update(['trial_ends_at' => now()->subDay()]);

        $this->assertFalse($this->user->onTrial());
    }

    #[Test]
    public function user_on_trial_returns_false_when_trial_ends_at_is_null()
    {
        $this->assertNull($this->user->trial_ends_at);
        $this->assertFalse($this->user->onTrial());
    }

    #[Test]
    public function paid_grant_with_null_duration_sets_plan_with_no_expiry()
    {
        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: null,
            source: 'stripe_subscription:price_xxx',
        );

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertNull($this->user->plan_expires_at);
        $this->assertNull($this->user->trial_ends_at);

        $this->assertDatabaseHas('plan_grants', [
            'user_id' => $this->user->id,
            'plan' => 'pro',
            'ends_at' => null,
            'source' => 'stripe_subscription:price_xxx',
        ]);
    }

    #[Test]
    public function non_trial_grant_clears_existing_trial_fields()
    {
        // Simulate a user currently on trial converting to paid
        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addDays(7),
            'trial_ends_at' => now()->addDays(7),
            'trial_reminder_stage' => 7,
            'has_used_trial' => true,
        ]);

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: null,
            source: 'stripe_subscription:price_xxx',
        );

        $this->user->refresh();

        $this->assertNull($this->user->trial_ends_at);
        $this->assertNull($this->user->trial_reminder_stage);
        $this->assertNull($this->user->plan_expires_at);
        $this->assertTrue($this->user->has_used_trial, 'has_used_trial should persist');
    }

    #[Test]
    public function trial_grant_with_null_duration_throws()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: null,
            source: 'signup_trial',
            isTrial: true,
        );
    }

    #[Test]
    public function expire_plan_resets_user_and_writes_audit_row()
    {
        Alias::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->subMinute(),
            'trial_ends_at' => now()->subMinute(),
        ]);

        $result = $this->planService->expirePlan($this->user, 'manual_test');

        $this->user->refresh();

        $this->assertSame('free', $this->user->plan);
        $this->assertNull($this->user->plan_expires_at);
        $this->assertNull($this->user->trial_ends_at);
        $this->assertSame('pro', $result['previous_plan_key']);
        $this->assertSame('Pro', $result['previous_plan_name']);
        $this->assertTrue($result['was_trial']);
        $this->assertSame(5, $result['deactivated']['aliases']);

        $this->assertDatabaseHas('plan_grants', [
            'user_id' => $this->user->id,
            'plan' => 'free',
            'source' => 'manual_test',
        ]);
    }

    #[Test]
    public function grant_plan_rejects_malformed_source()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->planService->grantPlan(
            user: $this->user,
            plan: 'pro',
            duration: CarbonInterval::days(14),
            source: "trial\nFAKE LOG LINE", // newline + space — out of allowed character set
        );
    }

    #[Test]
    public function expire_plan_rejects_malformed_source()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->user->update(['plan' => 'pro', 'plan_expires_at' => now()->subMinute()]);

        $this->planService->expirePlan($this->user, 'bad source with spaces');
    }

    #[Test]
    public function expire_plan_is_idempotent_for_already_free_users()
    {
        // user starts on free with no pending expiry
        $this->assertSame('free', $this->user->plan);
        $this->assertNull($this->user->plan_expires_at);

        $auditCountBefore = PlanGrant::count();

        $result = $this->planService->expirePlan($this->user, 'should_be_noop');

        $this->assertSame('free', $result['previous_plan_key']);
        $this->assertFalse($result['was_trial']);
        $this->assertSame($auditCountBefore, PlanGrant::count(), 'No audit row written for no-op');
    }
}
