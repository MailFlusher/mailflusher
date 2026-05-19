<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\Rule;
use App\Notifications\Account\TrialEnded;
use App\Notifications\Account\TrialEndingSoon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessPlanLifecycleCommandTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
    }

    #[Test]
    public function seven_day_reminder_fires_when_trial_ends_within_seven_days()
    {
        Notification::fake();

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addDays(6),
            'trial_ends_at' => now()->addDays(6),
            'trial_reminder_stage' => null,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        Notification::assertSentTo(
            $this->user,
            TrialEndingSoon::class,
            fn ($n) => $n->daysRemaining === 7
        );

        $this->assertSame(7, $this->user->fresh()->trial_reminder_stage);
    }

    #[Test]
    public function three_day_reminder_fires_after_seven_already_sent()
    {
        Notification::fake();

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addDays(2),
            'trial_ends_at' => now()->addDays(2),
            'trial_reminder_stage' => 7,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        Notification::assertSentTo(
            $this->user,
            TrialEndingSoon::class,
            fn ($n) => $n->daysRemaining === 3
        );

        $this->assertSame(3, $this->user->fresh()->trial_reminder_stage);
    }

    #[Test]
    public function one_day_reminder_fires_when_trial_ends_within_one_day()
    {
        Notification::fake();

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addHours(12),
            'trial_ends_at' => now()->addHours(12),
            'trial_reminder_stage' => 3,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        Notification::assertSentTo(
            $this->user,
            TrialEndingSoon::class,
            fn ($n) => $n->daysRemaining === 1
        );

        $this->assertSame(1, $this->user->fresh()->trial_reminder_stage);
    }

    #[Test]
    public function reminder_does_not_refire_at_same_stage()
    {
        Notification::fake();

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addDays(6),
            'trial_ends_at' => now()->addDays(6),
            'trial_reminder_stage' => 7,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        Notification::assertNothingSentTo($this->user);
    }

    #[Test]
    public function expired_trial_downgrades_user_and_sends_notification()
    {
        Notification::fake();

        Alias::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);
        Rule::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->subMinute(),
            'trial_ends_at' => now()->subMinute(),
            'has_used_trial' => true,
            'trial_reminder_stage' => 1,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        $fresh = $this->user->fresh();
        $this->assertSame('free', $fresh->plan);
        $this->assertNull($fresh->plan_expires_at);
        $this->assertNull($fresh->trial_ends_at);
        $this->assertNull($fresh->trial_reminder_stage);
        $this->assertTrue($fresh->has_used_trial, 'has_used_trial should persist after expiry');

        $this->assertSame(10, $fresh->aliases()->where('active', true)->count());
        $this->assertSame(0, $fresh->rules()->where('active', true)->count());

        Notification::assertSentTo($fresh, TrialEnded::class, function ($n) {
            return $n->previousPlanName === 'Pro'
                && $n->deactivated['aliases'] === 5
                && $n->deactivated['rules'] === 2;
        });
    }

    #[Test]
    public function expired_paid_plan_downgrades_but_does_not_send_trial_ended_notification()
    {
        Notification::fake();

        $this->user->update([
            'plan' => 'standard',
            'plan_expires_at' => now()->subDay(),
            'trial_ends_at' => null,
        ]);

        $this->artisan('mailflusher:process-plan-lifecycle')->assertSuccessful();

        $this->assertSame('free', $this->user->fresh()->plan);
        Notification::assertNotSentTo($this->user, TrialEnded::class);
    }
}
