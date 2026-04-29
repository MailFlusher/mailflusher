<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Services\Importers\AddyImporter;
use App\Services\Importers\SimpleLoginImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        // Give the user room to import
        $this->user->update(['plan' => 'pro']);
    }

    #[Test]
    public function simplelogin_dry_run_counts_fetched_aliases()
    {
        Http::fake([
            'https://app.simplelogin.io/api/v2/aliases*' => Http::sequence()
                ->push(['aliases' => [
                    ['email' => 'foo@simplelogin.io', 'note' => 'Netflix', 'enabled' => true],
                    ['email' => 'bar@simplelogin.io', 'note' => null, 'enabled' => false],
                ]])
                ->push(['aliases' => []]), // empty page → stop
        ]);

        $result = app(SimpleLoginImporter::class)->dryRun($this->user, 'sl_testtoken');

        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['importable']);
        $this->assertSame(0, $result['skipped']);
        $this->assertCount(2, $result['samples']);
        $this->assertSame('foo@simplelogin.io', $result['samples'][0]['email']);
    }

    #[Test]
    public function simplelogin_import_creates_aliases()
    {
        Http::fake([
            'https://app.simplelogin.io/api/v2/aliases*' => Http::sequence()
                ->push(['aliases' => [
                    ['email' => 'foo@simplelogin.io', 'note' => 'Netflix', 'enabled' => true],
                    ['email' => 'bar@simplelogin.io', 'note' => 'Amazon', 'enabled' => false],
                ]])
                ->push(['aliases' => []]),
        ]);

        $result = app(SimpleLoginImporter::class)->import($this->user, 'sl_testtoken');

        $this->assertSame(2, $result['imported']);
        $this->assertSame(0, $result['skipped_over_limit']);
        $this->assertEquals(2, $this->user->aliases()->count());

        $descriptions = $this->user->aliases()->get()->pluck('description')->toArray();
        $this->assertContains('Netflix', $descriptions);
        $this->assertContains('Amazon', $descriptions);

        // Active flag preserved
        $this->assertEquals(1, $this->user->aliases()->where('active', true)->count());
        $this->assertEquals(1, $this->user->aliases()->where('active', false)->count());
    }

    #[Test]
    public function addy_dry_run_counts_fetched_aliases()
    {
        Http::fake([
            'https://app.addy.io/api/v1/aliases*' => Http::response([
                'data' => [
                    ['email' => 'a@addy.io', 'description' => 'GitHub', 'active' => true],
                    ['email' => 'b@addy.io', 'description' => 'Spotify', 'active' => true],
                ],
                'meta' => ['last_page' => 1],
            ], 200),
        ]);

        $result = app(AddyImporter::class)->dryRun($this->user, 'addy-test-token');

        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['importable']);
    }

    #[Test]
    public function addy_import_creates_aliases()
    {
        Http::fake([
            'https://app.addy.io/api/v1/aliases*' => Http::response([
                'data' => [
                    ['email' => 'a@addy.io', 'description' => 'GitHub', 'active' => true],
                    ['email' => 'b@addy.io', 'description' => 'Spotify', 'active' => false],
                ],
                'meta' => ['last_page' => 1],
            ], 200),
        ]);

        $result = app(AddyImporter::class)->import($this->user, 'addy-test-token');

        $this->assertSame(2, $result['imported']);
        $this->assertEquals(2, $this->user->aliases()->count());
    }

    #[Test]
    public function importer_respects_alias_limit()
    {
        $this->user->update(['plan' => 'free']); // 10 alias limit

        // Pre-fill 9 existing aliases
        Alias::factory()->count(9)->create(['user_id' => $this->user->id]);

        // Callback fake — same response across repeated calls (dry-run + import)
        Http::fake(function ($request) {
            if (str_contains($request->url(), 'page_id=0')) {
                return Http::response([
                    'aliases' => [
                        ['email' => 'a@simplelogin.io', 'note' => 'A', 'enabled' => true],
                        ['email' => 'b@simplelogin.io', 'note' => 'B', 'enabled' => true],
                        ['email' => 'c@simplelogin.io', 'note' => 'C', 'enabled' => true],
                    ],
                ], 200);
            }

            return Http::response(['aliases' => []], 200);
        });

        $dry = app(SimpleLoginImporter::class)->dryRun($this->user, 'sl_x');
        $this->assertSame(3, $dry['total']);
        $this->assertSame(1, $dry['importable']); // only 1 slot left
        $this->assertSame(2, $dry['skipped']);

        $result = app(SimpleLoginImporter::class)->import($this->user, 'sl_x');
        $this->assertSame(1, $result['imported']);
        $this->assertSame(2, $result['skipped_over_limit']);
        $this->assertEquals(10, $this->user->aliases()->count());
    }

    #[Test]
    public function importer_throws_on_non_200_response()
    {
        Http::fake([
            'https://app.addy.io/api/v1/aliases*' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $this->expectException(\RuntimeException::class);
        app(AddyImporter::class)->dryRun($this->user, 'bad-token');
    }

    #[Test]
    public function dry_run_endpoint_validates_service()
    {
        $response = $this->postJson('/api/v1/import/dry-run', [
            'service' => 'totally-made-up',
            'token' => 'xyz',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function dry_run_endpoint_returns_counts()
    {
        Http::fake([
            'https://app.simplelogin.io/api/v2/aliases*' => Http::sequence()
                ->push(['aliases' => [['email' => 'x@simplelogin.io', 'note' => null, 'enabled' => true]]])
                ->push(['aliases' => []]),
        ]);

        $response = $this->postJson('/api/v1/import/dry-run', [
            'service' => 'simplelogin',
            'token' => 'sl_xxx',
        ]);

        $response->assertSuccessful()
            ->assertJson(['service' => 'simplelogin', 'total' => 1, 'importable' => 1]);
    }

    #[Test]
    public function import_endpoint_actually_imports()
    {
        Http::fake([
            'https://app.addy.io/api/v1/aliases*' => Http::response([
                'data' => [
                    ['email' => 'imp@addy.io', 'description' => 'imported', 'active' => true],
                ],
                'meta' => ['last_page' => 1],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/import', [
            'service' => 'addy',
            'token' => 'addy-token',
        ]);

        $response->assertSuccessful()
            ->assertJson(['service' => 'addy', 'imported' => 1]);
        $this->assertEquals(1, $this->user->aliases()->count());
    }

    #[Test]
    public function simplelogin_sends_authentication_header()
    {
        Http::fake([
            'https://app.simplelogin.io/api/v2/aliases*' => Http::sequence()
                ->push(['aliases' => []]),
        ]);

        app(SimpleLoginImporter::class)->dryRun($this->user, 'SLTOKEN123');

        Http::assertSent(fn ($req) => ($req->header('Authentication')[0] ?? '') === 'SLTOKEN123');
    }

    #[Test]
    public function addy_sends_bearer_token()
    {
        Http::fake([
            'https://app.addy.io/api/v1/aliases*' => Http::response([
                'data' => [],
                'meta' => ['last_page' => 1],
            ], 200),
        ]);

        app(AddyImporter::class)->dryRun($this->user, 'ADDYTOKEN456');

        Http::assertSent(fn ($req) => ($req->header('Authorization')[0] ?? '') === 'Bearer ADDYTOKEN456');
    }
}
