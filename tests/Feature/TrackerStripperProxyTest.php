<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\RedirectToken;
use App\Services\TrackerStripper;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrackerStripperProxyTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected TrackerStripper $stripper;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        $this->stripper = app(TrackerStripper::class);
    }

    #[Test]
    public function proxy_links_replaces_href_with_redirect_token()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $html = '<a href="https://brand.com/page?utm_source=x">Click</a>';

        $out = $this->stripper->proxyLinks($html, $alias);

        $this->assertStringContainsString('/r/', $out);
        $this->assertStringNotContainsString('utm_source', $out);
        $this->assertEquals(1, $this->stripper->linksRewritten);
        $this->assertEquals(1, RedirectToken::count());
        $this->assertSame(
            'https://brand.com/page?utm_source=x',
            RedirectToken::first()->target_url
        );
    }

    #[Test]
    public function proxy_links_skips_mailto_anchors_and_non_http_schemes()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $html = '<a href="mailto:foo@bar">mail</a> <a href="#x">jump</a> <a href="javascript:alert(1)">bad</a>';

        $out = $this->stripper->proxyLinks($html, $alias);

        $this->assertStringContainsString('mailto:foo@bar', $out);
        $this->assertStringContainsString('#x', $out);
        $this->assertStringContainsString('javascript:alert(1)', $out);
        $this->assertStringNotContainsString('/r/', $out);
        $this->assertEquals(0, RedirectToken::count());
    }

    #[Test]
    public function proxy_links_rewrites_multiple_anchors()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $html = '<a href="https://a.com">A</a><a href="https://b.com">B</a>';

        $out = $this->stripper->proxyLinks($html, $alias);

        $this->assertEquals(2, $this->stripper->linksRewritten);
        $this->assertEquals(2, RedirectToken::count());
    }

    #[Test]
    public function prune_command_deletes_expired_tokens_only()
    {
        $expired = RedirectToken::mint(null, 'https://a.com');
        $expired->update(['expires_at' => now()->subDay()]);

        $live = RedirectToken::mint(null, 'https://b.com');

        $this->artisan('mailflusher:prune-redirect-tokens')->assertSuccessful();

        $this->assertNull(RedirectToken::find($expired->token));
        $this->assertNotNull(RedirectToken::find($live->token));
    }

    #[Test]
    public function strip_trackers_settings_endpoint_updates_user()
    {
        $this->user->update(['strip_trackers' => 'off']);

        $response = $this->actingAs($this->user, 'web')
            ->post(route('settings.strip_trackers'), ['strip_trackers' => 'pixels_only']);

        $response->assertRedirect();
        $this->assertSame('pixels_only', $this->user->fresh()->strip_trackers);
    }

    #[Test]
    public function strip_trackers_settings_endpoint_rejects_invalid_value()
    {
        $this->user->update(['strip_trackers' => 'off']);

        $response = $this->actingAs($this->user, 'web')
            ->from(route('settings.show'))
            ->post(route('settings.strip_trackers'), ['strip_trackers' => 'bogus']);

        $response->assertSessionHasErrors('strip_trackers');
        $this->assertSame('off', $this->user->fresh()->strip_trackers);
    }

    #[Test]
    public function free_user_cannot_enable_pixels_and_links()
    {
        $this->user->update(['plan' => 'free', 'strip_trackers' => 'off']);

        $response = $this->actingAs($this->user, 'web')
            ->from(route('settings.show'))
            ->post(route('settings.strip_trackers'), ['strip_trackers' => 'pixels_and_links']);

        $response->assertSessionHasErrors('strip_trackers');
        $this->assertSame('off', $this->user->fresh()->strip_trackers);
    }

    #[Test]
    public function free_user_can_still_enable_pixels_only()
    {
        $this->user->update(['plan' => 'free', 'strip_trackers' => 'off']);

        $response = $this->actingAs($this->user, 'web')
            ->post(route('settings.strip_trackers'), ['strip_trackers' => 'pixels_only']);

        $response->assertRedirect();
        $this->assertSame('pixels_only', $this->user->fresh()->strip_trackers);
    }

    #[Test]
    public function standard_user_can_enable_pixels_and_links()
    {
        $this->user->update(['plan' => 'standard', 'strip_trackers' => 'off']);

        $response = $this->actingAs($this->user, 'web')
            ->post(route('settings.strip_trackers'), ['strip_trackers' => 'pixels_and_links']);

        $response->assertRedirect();
        $this->assertSame('pixels_and_links', $this->user->fresh()->strip_trackers);
    }

    #[Test]
    public function can_use_link_stripping_reflects_plan()
    {
        $this->user->update(['plan' => 'free']);
        $this->assertFalse($this->user->fresh()->canUseLinkStripping());

        $this->user->update(['plan' => 'standard']);
        $this->assertTrue($this->user->fresh()->canUseLinkStripping());

        $this->user->update(['plan' => 'pro']);
        $this->assertTrue($this->user->fresh()->canUseLinkStripping());
    }
}
