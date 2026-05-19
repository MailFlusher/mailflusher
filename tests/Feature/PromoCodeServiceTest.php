<?php

namespace Tests\Feature;

use App\Exceptions\PromoCodeException;
use App\Models\PlanGrant;
use App\Models\PromoCode;
use App\Services\PromoCodeService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PromoCodeServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected PromoCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
        $this->service = app(PromoCodeService::class);
    }

    #[Test]
    public function free_user_can_redeem_a_valid_code()
    {
        $this->travelTo(now()->startOfMinute());

        $code = PromoCode::factory()->create(['code' => 'WELCOME30', 'plan' => 'pro', 'duration_days' => 30]);

        $redemption = $this->service->redeem($this->user, 'welcome30');

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertTrue($this->user->plan_expires_at->equalTo(now()->addDays(30)));
        $this->assertSame($code->id, $redemption->promo_code_id);
        $this->assertSame('pro', $redemption->plan);
        $this->assertSame(30, $redemption->duration_days);

        $this->assertSame(1, $code->fresh()->redemption_count);

        $this->assertDatabaseHas('plan_grants', [
            'user_id' => $this->user->id,
            'plan' => 'pro',
            'source' => 'promo:WELCOME30',
        ]);
    }

    #[Test]
    public function code_lookup_is_case_insensitive_and_trimmed()
    {
        PromoCode::factory()->create(['code' => 'PROMO1', 'duration_days' => 7]);

        $this->service->redeem($this->user, '  promo1  ');

        $this->assertSame('pro', $this->user->fresh()->plan);
    }

    #[Test]
    public function malformed_code_throws_invalid()
    {
        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('That code does not look right.');

        $this->service->redeem($this->user, 'has spaces');
    }

    #[Test]
    public function unknown_code_throws_not_found()
    {
        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('That promo code is not valid.');

        $this->service->redeem($this->user, 'NOPE123');
    }

    #[Test]
    public function inactive_code_throws_inactive()
    {
        PromoCode::factory()->create(['code' => 'DEADCODE', 'active' => false]);

        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('no longer active');

        $this->service->redeem($this->user, 'DEADCODE');
    }

    #[Test]
    public function expired_code_throws_expired()
    {
        PromoCode::factory()->create(['code' => 'OLDCODE', 'expires_at' => now()->subDay()]);

        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('expired');

        $this->service->redeem($this->user, 'OLDCODE');
    }

    #[Test]
    public function exhausted_code_throws_exhausted()
    {
        PromoCode::factory()->create([
            'code' => 'CAPCODE',
            'max_redemptions' => 5,
            'redemption_count' => 5,
        ]);

        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('redemption limit');

        $this->service->redeem($this->user, 'CAPCODE');
    }

    #[Test]
    public function user_with_active_stripe_subscription_cannot_redeem()
    {
        $this->user->update(['stripe_subscription_id' => 'sub_abc']);

        PromoCode::factory()->create(['code' => 'NICE10']);

        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('Cancel your paid subscription');

        $this->service->redeem($this->user, 'NICE10');
    }

    #[Test]
    public function same_user_cannot_redeem_same_code_twice()
    {
        PromoCode::factory()->create(['code' => 'ONCEONLY']);

        $this->service->redeem($this->user, 'ONCEONLY');

        $this->expectException(PromoCodeException::class);
        $this->expectExceptionMessage('already redeemed');

        $this->service->redeem($this->user, 'ONCEONLY');
    }

    #[Test]
    public function redeeming_a_second_code_extends_existing_plan_expiry()
    {
        $this->travelTo(now()->startOfMinute());

        PromoCode::factory()->create(['code' => 'FIRST', 'plan' => 'pro', 'duration_days' => 30]);
        PromoCode::factory()->create(['code' => 'SECOND', 'plan' => 'pro', 'duration_days' => 15]);

        $this->service->redeem($this->user, 'FIRST');

        $this->travel(5)->days();

        $this->service->redeem($this->user, 'SECOND');

        $this->user->refresh();

        // First gave 30d from start; second adds 15d to remaining ~25d, total ~40d from "second now".
        // We expect plan_expires_at to be roughly start + 30 + 15 days.
        $expectedSecondsFromSecondRedeem = (25 * 86400) + (15 * 86400);
        $actualSeconds = (int) now()->diffInSeconds($this->user->plan_expires_at, absolute: true);

        $this->assertEqualsWithDelta($expectedSecondsFromSecondRedeem, $actualSeconds, 2);
    }

    #[Test]
    public function redemption_increments_counter_and_writes_row()
    {
        $code = PromoCode::factory()->create(['code' => 'ABC123', 'max_redemptions' => 2]);

        $this->service->redeem($this->user, 'ABC123');

        $code->refresh();
        $this->assertSame(1, $code->redemption_count);
        $this->assertDatabaseCount('promo_redemptions', 1);
    }

    #[Test]
    public function audit_log_records_promo_source_with_code_in_it()
    {
        PromoCode::factory()->create(['code' => 'AUDITME', 'duration_days' => 7]);

        $this->service->redeem($this->user, 'AUDITME');

        $grant = PlanGrant::latest('id')->first();
        $this->assertSame('promo:AUDITME', $grant->source);
    }
}
