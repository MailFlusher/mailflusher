<?php

namespace Tests\Feature;

use App\Models\RedirectToken;
use App\Services\TrackerStripper;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrackerStripperTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected TrackerStripper $stripper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stripper = app(TrackerStripper::class);
    }

    #[Test]
    public function strips_1x1_tracking_pixel_images()
    {
        $html = '<p>Hello</p><img src="https://random.example/open.gif" width="1" height="1"><p>World</p>';

        $out = $this->stripper->stripPixels($html);

        $this->assertStringNotContainsString('open.gif', $out);
        $this->assertStringContainsString('Hello', $out);
        $this->assertEquals(1, $this->stripper->pixelsRemoved);
    }

    #[Test]
    public function strips_images_from_known_tracker_domains()
    {
        config(['trackers.pixel_domains' => ['mailchimp.com']]);

        $html = '<img src="https://list.mailchimp.com/track?x=123"><img src="https://cdn.brand.com/logo.png">';

        $out = $this->stripper->stripPixels($html);

        $this->assertStringNotContainsString('mailchimp.com', $out);
        $this->assertStringContainsString('cdn.brand.com/logo.png', $out);
        $this->assertEquals(1, $this->stripper->pixelsRemoved);
    }

    #[Test]
    public function keeps_normal_inline_images_intact()
    {
        $html = '<img src="https://cdn.brand.com/hero.jpg" width="600" height="400" alt="hero">';

        $out = $this->stripper->stripPixels($html);

        $this->assertStringContainsString('hero.jpg', $out);
        $this->assertEquals(0, $this->stripper->pixelsRemoved);
    }

    #[Test]
    public function strip_tracking_params_removes_utms_and_click_ids()
    {
        $url = 'https://brand.com/page?utm_source=x&utm_medium=y&fbclid=abc&id=42&_hsenc=foo';

        $cleaned = $this->stripper->stripTrackingParams($url);

        $this->assertStringNotContainsString('utm_source', $cleaned);
        $this->assertStringNotContainsString('utm_medium', $cleaned);
        $this->assertStringNotContainsString('fbclid', $cleaned);
        $this->assertStringNotContainsString('_hsenc', $cleaned);
        $this->assertStringContainsString('id=42', $cleaned);
    }

    #[Test]
    public function strip_tracking_params_handles_no_query_string()
    {
        $url = 'https://brand.com/page';
        $this->assertSame($url, $this->stripper->stripTrackingParams($url));
    }

    #[Test]
    public function clean_link_params_rewrites_anchors()
    {
        $html = '<a href="https://brand.com/page?utm_source=news&id=42">Read</a> <a href="mailto:foo@bar">mail</a> <a href="#anchor">jump</a>';

        $out = $this->stripper->cleanLinkParams($html);

        $this->assertStringNotContainsString('utm_source=news', $out);
        $this->assertStringContainsString('id=42', $out);
        $this->assertStringContainsString('mailto:foo@bar', $out);
        $this->assertStringContainsString('#anchor', $out);
        $this->assertEquals(1, $this->stripper->linksRewritten);
    }

    #[Test]
    public function clean_link_params_leaves_clean_links_untouched()
    {
        $html = '<a href="https://brand.com/page?id=42">Read</a>';

        $out = $this->stripper->cleanLinkParams($html);

        $this->assertStringContainsString('id=42', $out);
        $this->assertEquals(0, $this->stripper->linksRewritten);
    }

    #[Test]
    public function redirect_token_mint_stores_target_and_expires()
    {
        $token = RedirectToken::mint(null, 'https://example.com/post?id=1');

        $this->assertNotEmpty($token->token);
        $this->assertSame('https://example.com/post?id=1', $token->target_url);
        $this->assertTrue($token->expires_at->isFuture());
    }

    #[Test]
    public function redirect_endpoint_302s_to_cleaned_target()
    {
        $token = RedirectToken::mint(null, 'https://example.com/p?utm_source=x&id=42');

        $response = $this->get("/r/{$token->token}");

        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://example.com/p', $location);
        $this->assertStringNotContainsString('utm_source=x', $location);
        $this->assertStringContainsString('id=42', $location);
        $this->assertEquals(1, $token->fresh()->clicks);
    }

    #[Test]
    public function redirect_endpoint_404s_for_unknown_token()
    {
        $response = $this->get('/r/abcdefgh1234');

        $response->assertNotFound();
    }

    #[Test]
    public function redirect_endpoint_404s_for_expired_token()
    {
        $token = RedirectToken::mint(null, 'https://example.com');
        $token->update(['expires_at' => now()->subDay()]);

        $response = $this->get("/r/{$token->token}");

        $response->assertNotFound();
    }
}
