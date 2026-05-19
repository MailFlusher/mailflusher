<?php

namespace Tests\Feature;

use App\Models\PromoCode;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PromoRedeemEndpointTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->actingAs($this->user);

        // Throttle middleware uses Redis in this app; tests run without Redis.
        $this->withoutMiddleware([ThrottleRequests::class, ThrottleRequestsWithRedis::class]);
    }

    #[Test]
    public function authenticated_user_redeems_via_endpoint()
    {
        PromoCode::factory()->create(['code' => 'GOOD30', 'plan' => 'pro', 'duration_days' => 30]);

        $response = $this->from(route('subscription.index'))
            ->post(route('promo.redeem'), ['code' => 'good30']);

        $response->assertRedirect(route('subscription.index'));
        $response->assertSessionHas('flash');
        $this->assertSame('pro', $this->user->fresh()->plan);
    }

    #[Test]
    public function endpoint_surfaces_validation_error_for_bad_code()
    {
        $response = $this->from(route('subscription.index'))
            ->post(route('promo.redeem'), ['code' => 'NOSUCHCODE']);

        $response->assertRedirect(route('subscription.index'));
        $response->assertSessionHasErrors('code');
        $this->assertSame('free', $this->user->fresh()->plan);
    }

    #[Test]
    public function endpoint_requires_code_field()
    {
        $response = $this->post(route('promo.redeem'), []);

        $response->assertSessionHasErrors('code');
    }
}
