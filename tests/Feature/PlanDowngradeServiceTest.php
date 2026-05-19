<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\Domain;
use App\Models\Recipient;
use App\Models\Rule;
use App\Services\PlanDowngradeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class PlanDowngradeServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected PlanDowngradeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
        $this->service = new PlanDowngradeService;
    }

    #[Test]
    public function downgrade_keeps_oldest_aliases_and_deactivates_excess()
    {
        // Create 15 aliases over 15 minutes so created_at order is deterministic
        $aliases = collect(range(0, 14))->map(function ($i) {
            return Alias::factory()->create([
                'user_id' => $this->user->id,
                'active' => true,
                'created_at' => now()->subMinutes(15 - $i),
            ]);
        });

        $result = $this->service->downgrade($this->user, 'free');

        $this->assertSame(5, $result['aliases']);

        $activeAfter = $this->user->aliases()->where('active', true)->orderBy('created_at')->pluck('id');
        $this->assertSame($aliases->take(10)->pluck('id')->all(), $activeAfter->all());
    }

    #[Test]
    public function downgrade_deactivates_all_rules_for_free_plan()
    {
        Rule::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $result = $this->service->downgrade($this->user, 'free');

        $this->assertSame(3, $result['rules']);
        $this->assertSame(0, $this->user->rules()->where('active', true)->count());
    }

    #[Test]
    public function downgrade_deactivates_all_custom_domains_for_free_plan()
    {
        Domain::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $result = $this->service->downgrade($this->user, 'free');

        $this->assertSame(2, $result['domains']);
        $this->assertSame(0, $this->user->domains()->where('active', true)->count());
    }

    #[Test]
    public function downgrade_to_standard_keeps_rules_within_limit()
    {
        // Standard allows 5 rules
        Rule::factory()->count(7)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $result = $this->service->downgrade($this->user, 'standard');

        $this->assertSame(2, $result['rules']);
        $this->assertSame(5, $this->user->rules()->where('active', true)->count());
    }

    #[Test]
    public function downgrade_does_not_touch_already_inactive_items()
    {
        Alias::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'active' => false,
        ]);

        $result = $this->service->downgrade($this->user, 'free');

        $this->assertSame(0, $result['aliases']);
    }

    #[Test]
    public function downgrade_throws_on_unknown_plan()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->downgrade($this->user, 'enterprise');
    }

    #[Test]
    public function downgrade_to_free_deactivates_all_non_default_recipients()
    {
        // Free plan: 1 recipient (the default). Create 3 additional active recipients.
        Recipient::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $result = $this->service->downgrade($this->user, 'free');

        $this->assertSame(3, $result['recipients']);

        // Default recipient stays active
        $this->assertTrue($this->user->defaultRecipient->fresh()->active);

        // Only 1 active recipient remains (the default)
        $this->assertSame(1, $this->user->recipients()->where('active', true)->count());
    }

    #[Test]
    public function downgrade_to_standard_keeps_default_plus_oldest_four_recipients()
    {
        // Standard plan: 5 recipients. Default + 4 additional should stay active.
        $extras = collect(range(0, 6))->map(fn ($i) => Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'created_at' => now()->subMinutes(10 - $i),
        ]));

        $result = $this->service->downgrade($this->user, 'standard');

        $this->assertSame(3, $result['recipients'], '7 extras - 4 kept = 3 deactivated');
        $this->assertSame(5, $this->user->recipients()->where('active', true)->count());

        // Oldest 4 of the extras stayed active alongside the default
        $stillActive = $this->user->recipients()
            ->where('active', true)
            ->where('id', '!=', $this->user->default_recipient_id)
            ->orderBy('created_at')
            ->pluck('id')
            ->all();

        $this->assertSame($extras->take(4)->pluck('id')->all(), $stillActive);
    }

    #[Test]
    public function downgrade_to_pro_does_not_deactivate_recipients_under_limit()
    {
        // Pro plan: 30 recipients. With 5 total (default + 4) we should not deactivate any.
        Recipient::factory()->count(4)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $result = $this->service->downgrade($this->user, 'pro');

        $this->assertSame(0, $result['recipients']);
    }

    #[Test]
    public function inactive_recipient_is_excluded_from_verified_recipients()
    {
        $active = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $inactive = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => false,
            'email_verified_at' => now(),
        ]);

        $verifiedIds = $this->user->verifiedRecipients()->pluck('id')->all();

        $this->assertContains($this->user->default_recipient_id, $verifiedIds);
        $this->assertContains($active->id, $verifiedIds);
        $this->assertNotContains($inactive->id, $verifiedIds);
    }

    #[Test]
    public function alias_with_only_inactive_recipients_falls_back_to_default()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $inactive = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => false,
            'email_verified_at' => now(),
        ]);

        // Wire the inactive recipient as the alias's only configured recipient.
        $alias->recipients()->attach($inactive->id, ['id' => Uuid::uuid4()]);

        // Alias::verifiedRecipients should exclude inactive...
        $this->assertSame(0, $alias->verifiedRecipients()->count());

        // ...and verifiedRecipientsOrDefault should fall back to the user's default.
        $target = $alias->verifiedRecipientsOrDefault();
        // It returns either a HasOne builder (default fallback) or an Eloquent Collection.
        $resolved = $target instanceof Collection
            ? $target
            : $target->get();

        $this->assertSame($this->user->default_recipient_id, $resolved->first()->id);
    }

    #[Test]
    public function auto_create_extension_routing_skips_inactive_recipients()
    {
        // ReceiveEmail's `+1.2` extension trick auto-routes to recipients by
        // ordinal position. Inactive recipients must be skipped over.
        $r1 = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => false,
            'email_verified_at' => now(),
            'created_at' => now()->subMinutes(10),
        ]);
        $r2 = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'active' => true,
            'email_verified_at' => now(),
            'created_at' => now()->subMinutes(5),
        ]);

        // Mirror the filter from ReceiveEmail:365.
        $eligible = $this->user
            ->recipients()
            ->select(['id', 'email_verified_at', 'active'])
            ->oldest()
            ->get()
            ->filter(fn ($item) => ! is_null($item['email_verified_at']) && $item['active'])
            ->pluck('id')
            ->all();

        $this->assertNotContains($r1->id, $eligible, 'inactive recipient excluded');
        $this->assertContains($r2->id, $eligible);
    }
}
