<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GrantSignupTrialTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
    }

    #[Test]
    public function verified_event_grants_signup_trial()
    {
        $this->assertFalse($this->user->has_used_trial);
        $this->assertSame('free', $this->user->plan);

        event(new Verified($this->user));

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertTrue($this->user->onTrial());
        $this->assertTrue($this->user->has_used_trial);
        $this->assertNotNull($this->user->trial_ends_at);
    }

    #[Test]
    public function listener_noop_when_user_already_used_trial()
    {
        $this->user->update(['has_used_trial' => true]);

        event(new Verified($this->user));

        $this->user->refresh();

        $this->assertSame('free', $this->user->plan);
        $this->assertNull($this->user->trial_ends_at);
    }

    #[Test]
    public function listener_noop_when_trial_disabled()
    {
        config(['mailflusher.trial.enabled' => false]);

        event(new Verified($this->user));

        $this->user->refresh();

        $this->assertSame('free', $this->user->plan);
        $this->assertFalse($this->user->has_used_trial);
    }

    #[Test]
    public function audit_row_recorded_with_signup_trial_source()
    {
        event(new Verified($this->user));

        $this->assertDatabaseHas('plan_grants', [
            'user_id' => $this->user->id,
            'plan' => 'pro',
            'source' => 'signup_trial',
        ]);
    }
}
