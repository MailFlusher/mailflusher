<?php

namespace Tests\Feature;

use App\Models\Alias;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BurnerAliasTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
    }

    #[Test]
    public function alias_with_past_expires_at_is_time_expired()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'expires_at' => now()->subHour(),
        ]);

        $this->assertTrue($alias->isBurner());
        $this->assertTrue($alias->isTimeExpired());
        $this->assertTrue($alias->isExpired());
    }

    #[Test]
    public function alias_with_future_expires_at_is_not_expired()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'expires_at' => now()->addHour(),
        ]);

        $this->assertTrue($alias->isBurner());
        $this->assertFalse($alias->isTimeExpired());
        $this->assertFalse($alias->isExpired());
    }

    #[Test]
    public function alias_with_emails_forwarded_at_max_has_hit_email_limit()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'max_emails' => 3,
            'emails_forwarded' => 3,
        ]);

        $this->assertTrue($alias->hasHitEmailLimit());
        $this->assertTrue($alias->isExpired());
    }

    #[Test]
    public function alias_below_max_emails_has_not_hit_limit()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'max_emails' => 3,
            'emails_forwarded' => 2,
        ]);

        $this->assertFalse($alias->hasHitEmailLimit());
        $this->assertFalse($alias->isExpired());
    }

    #[Test]
    public function non_burner_alias_is_never_expired()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertFalse($alias->isBurner());
        $this->assertFalse($alias->isExpired());
    }

    #[Test]
    public function mark_expired_deactivates_and_stamps_expired_at()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->subMinute(),
        ]);

        $this->assertNull($alias->expired_at);

        $alias->markExpired();

        $this->assertFalse($alias->fresh()->active);
        $this->assertNotNull($alias->fresh()->expired_at);
    }

    #[Test]
    public function scheduled_sweep_expires_time_based_burners()
    {
        $due = Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->subMinutes(5),
        ]);

        $notDue = Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->addHour(),
        ]);

        $this->artisan('mailflusher:expire-burner-aliases')->assertSuccessful();

        $this->assertFalse($due->fresh()->active);
        $this->assertNotNull($due->fresh()->expired_at);
        $this->assertTrue($notDue->fresh()->active);
    }

    #[Test]
    public function scheduled_sweep_ignores_count_based_burners()
    {
        // Count-based expiry is enforced at forward time, not by the scheduled sweep.
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'max_emails' => 5,
            'emails_forwarded' => 5,
        ]);

        $this->artisan('mailflusher:expire-burner-aliases')->assertSuccessful();

        $this->assertTrue($alias->fresh()->active);
    }

    #[Test]
    public function has_reached_burner_limit_returns_true_at_plan_cap()
    {
        config(['mailflusher.plans.free.burner_aliases' => 2]);
        $this->user->update(['plan' => 'free']);

        // 2 active burners - at the cap
        Alias::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $this->assertTrue($this->user->fresh()->hasReachedBurnerAliasLimit());
    }

    #[Test]
    public function has_reached_burner_limit_ignores_inactive_and_non_burner_aliases()
    {
        config(['mailflusher.plans.free.burner_aliases' => 2]);
        $this->user->update(['plan' => 'free']);

        // 1 active burner
        Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->addDay(),
        ]);

        // 1 inactive burner (shouldn't count)
        Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => false,
            'expires_at' => now()->addDay(),
        ]);

        // 5 non-burner aliases (shouldn't count)
        Alias::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $this->assertFalse($this->user->fresh()->hasReachedBurnerAliasLimit());
    }

    #[Test]
    public function pro_plan_has_no_burner_limit()
    {
        config(['mailflusher.plans.pro.burner_aliases' => null]);
        $this->user->update(['plan' => 'pro']);

        Alias::factory()->count(50)->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $this->assertFalse($this->user->fresh()->hasReachedBurnerAliasLimit());
    }
}
