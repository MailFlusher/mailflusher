<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewAliasHourlyLimitTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
    }

    #[Test]
    public function free_plan_limit_is_ten()
    {
        $this->user->update(['plan' => 'free']);

        $this->assertSame(10, $this->user->fresh()->newAliasHourlyLimit());
    }

    #[Test]
    public function standard_plan_limit_is_twenty()
    {
        $this->user->update(['plan' => 'standard']);

        $this->assertSame(20, $this->user->fresh()->newAliasHourlyLimit());
    }

    #[Test]
    public function pro_plan_limit_is_fifty()
    {
        $this->user->update(['plan' => 'pro']);

        $this->assertSame(50, $this->user->fresh()->newAliasHourlyLimit());
    }

    #[Test]
    public function falls_back_to_global_config_when_plan_value_missing()
    {
        // Remove the plan value and verify fallback
        config(['mailflusher.plans.free.new_alias_hourly_limit' => null]);
        config(['mailflusher.new_alias_hourly_limit' => 7]);

        $this->user->update(['plan' => 'free']);

        $this->assertSame(7, $this->user->fresh()->newAliasHourlyLimit());
    }
}
